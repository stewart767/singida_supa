<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Intake;
use App\Models\Programme;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicantWebController extends Controller
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepo
    ) {}

    public function dashboard()
    {
        $user = Auth::user();
        $consentRequired = \App\Models\PrivacyPolicy::where('status', 'Published')->exists() || 
                           \App\Models\TermsCondition::where('status', 'Published')->exists();

        if ($consentRequired && $user && $user->applicant && !$user->applicant->initial_consent_given) {
            return redirect()->route('applicant.wizard', ['step' => 1])->with('error', 'You must agree to the university admissions consent form to continue.');
        }

        $application = null;
        $rejectedDocs = collect();
        $hasRejectedDocs = false;

        if ($user->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
            if ($application) {
                $rejectedDocs = $application->documents()->where('verification_status', 'rejected')->get();
                $hasRejectedDocs = $rejectedDocs->isNotEmpty();
            }
        }

        return view('applicant.dashboard', compact('user', 'application', 'rejectedDocs', 'hasRejectedDocs'));
    }

    public function wizard()
    {
        $loginRequired = \App\Models\Setting::get('applicant_login_required', false);

        if (!Auth::check()) {
            if ($loginRequired) {
                return redirect()->route('login');
            }

            // Create temporary guest user
            $guestUserId = session('guest_user_id');
            $user = null;
            if ($guestUserId) {
                $user = \App\Models\User::find($guestUserId);
            }

            if (!$user) {
                $randomStr = \Illuminate\Support\Str::random(10);
                $user = \App\Models\User::create([
                    'name' => 'Guest Applicant',
                    'email' => 'guest_' . $randomStr . '@supa-guest.com',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'role' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                \App\Models\Applicant::create([
                    'user_id' => $user->id
                ]);
                session(['guest_user_id' => $user->id]);
            }

            Auth::login($user);
        }

        $user = Auth::user();
        
        $consentRequired = \App\Models\PrivacyPolicy::where('status', 'Published')->exists() || 
                           \App\Models\TermsCondition::where('status', 'Published')->exists();

        if ($consentRequired && $user && $user->applicant && !$user->applicant->initial_consent_given) {
            return redirect()->route('applicant.consent.notice', array_filter(['programme_id' => request('programme_id')]));
        }

        $application = null;

        if ($user && $user->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
            
            // Mark application as public submission if guest
            $isGuestSession = false;
            try {
                if (request()->hasSession() && request()->session()->has('guest_user_id')) {
                    $isGuestSession = request()->session()->get('guest_user_id') == $user->id;
                }
            } catch (\Throwable $e) {}

            if ($application && ($isGuestSession || \Illuminate\Support\Str::contains($user->email, '@supa-guest.com'))) {
                $application->update(['is_public_submission' => true]);
            }
        }

        $programmes = Programme::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $intakes = Intake::where('is_active', true)->get();
        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();

        return view('applicant.wizard', compact('user', 'application', 'programmes', 'academicYears', 'intakes', 'activePolicy'));
    }
}
