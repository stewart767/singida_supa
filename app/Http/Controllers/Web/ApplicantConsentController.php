<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationConsent;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantConsentController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $consents = ApplicationConsent::with(['application', 'privacyPolicy', 'termsCondition'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('applicant.privacy-consent', compact('user', 'consents'));
    }

    public function downloadReceipt(ApplicationConsent $consent)
    {
        if ($consent->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this consent receipt.');
        }

        $consent->load(['application.applicant.user', 'privacyPolicy', 'termsCondition']);

        return view('pdf.consent-receipt', compact('consent'));
    }

    public function showNotice()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Fetch active published versions of policy and terms
        $activePolicy = PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        // Check if user already has up-to-date consent
        if ($user->isApplicant() && $user->applicant) {
            $policyVersion = $activePolicy ? $activePolicy->version : null;
            $termsVersion = $activeTerms ? $activeTerms->version : null;

            $consentValid = true;
            if ($activePolicy && ($user->applicant->privacy_policy_version !== $policyVersion || $user->applicant->consent_status !== 'accepted')) {
                $consentValid = false;
            }

            if ($activeTerms && ($user->applicant->terms_version !== $termsVersion || $user->applicant->consent_status !== 'accepted')) {
                $consentValid = false;
            }

            if ($consentValid) {
                $progId = request()->get('programme_id') ?? session('selected_programme_id');
                return redirect()->route('applicant.wizard', array_filter(['step' => 1, 'programme_id' => $progId]));
            }
        }

        if (request()->has('programme_id')) {
            session(['selected_programme_id' => request()->get('programme_id')]);
        }

        return view('applicant.consent_notice', compact('user', 'activePolicy', 'activeTerms'));
    }

    public function acceptNotice(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isApplicant()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure applicant record exists
        if (!$user->applicant) {
            $user->applicant()->create();
            $user->load('applicant');
        }

        // Validate declarations
        $request->validate([
            'confirm_accurate' => 'required|accepted',
            'read_privacy' => 'required|accepted',
            'consent_given' => 'required|accepted',
            'understand_rights' => 'required|accepted',
        ]);

        $activePolicy = PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        if (!$activePolicy) {
            return redirect()->back()->with('error', 'Active Privacy Policy not found. Please contact administration.');
        }

        $userAgent = $request->header('User-Agent');
        $userAgentData = ApplicationWorkflowService::parseUserAgent($userAgent);

        $now = now();
        $timestamp = $now->toDateTimeString();

        // Generate Consent Hash (server-side, secure)
        $hashData = [
            'applicant_id' => $user->applicant->id,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'consented_at' => $timestamp,
            'privacy_policy_version' => $activePolicy->version,
            'terms_version' => $activeTerms ? $activeTerms->version : null,
        ];
        $consentHash = hash('sha256', json_encode($hashData));

        // Find active application if exists
        $application = Application::where('applicant_id', $user->applicant->id)
            ->whereIn('status', ['Draft', 'Pending Payment', 'Verification In Progress'])
            ->first();

        $consent = ApplicationConsent::create([
            'application_id' => $application ? $application->id : null,
            'user_id' => $user->id,
            'applicant_id' => $user->applicant->id,
            'privacy_policy_id' => $activePolicy->id,
            'terms_conditions_id' => $activeTerms ? $activeTerms->id : null,
            'consent_version' => $activePolicy->version,
            'consent_language' => app()->getLocale(),
            'consent_source' => 'Web',
            'device_type' => $userAgentData['device_type'],
            'browser_name' => $userAgentData['browser_name'],
            'operating_system' => $userAgentData['operating_system'],
            'application_status_at_consent' => $application ? $application->status : 'Pre-application',
            'consent_given' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'consented_at' => $now,
            'consent_hash' => $consentHash,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Update Applicant Profile
        $user->applicant->update([
            'consent_status' => 'accepted',
            'consented_at' => $now,
            'privacy_policy_version' => $activePolicy->version,
            'terms_version' => $activeTerms ? $activeTerms->version : null,
            'initial_consent_given' => true,
            'initial_consent_version' => $activePolicy->version,
            'initial_consent_at' => $now,
        ]);

        $redirectUrl = route('applicant.wizard', ['step' => 1]);
        if (session()->has('selected_programme_id')) {
            $redirectUrl = route('applicant.wizard', ['step' => 1, 'programme_id' => session('selected_programme_id')]);
            session()->forget('selected_programme_id');
        } elseif (session()->has('selected_job_id')) {
            $vacancy = \App\Models\Vacancy::find(session('selected_job_id'));
            $redirectUrl = $vacancy 
                ? route('public.careers.apply', $vacancy->vacancy_number) 
                : route('public.careers.dashboard');
        }

        return redirect()->intended($redirectUrl)->with('success', 'Thank you! Your consent has been recorded. You can now proceed.');
    }

    public function declineNotice(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLogService::log('applicant_consent_declined', "Applicant declined consent terms");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function downloadDocumentFile($type)
    {
        abort(403, 'Downloading official compliance documents is disabled.');
    }
}
