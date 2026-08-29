<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationConsent;
use App\Models\PrivacyPolicy;
use App\Models\TermsCondition;
use App\Models\AcademicYear;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->hasRole(['data_protection_officer', 'legal_officer']))) {
            abort(403, 'Unauthorized access to Personal Data Compliance.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAccess();

        $policies = PrivacyPolicy::with('publisher')->latest()->get();
        $terms = TermsCondition::with('publisher')->latest()->get();

        $query = ApplicationConsent::with(['application.applicant.user', 'privacyPolicy', 'termsCondition']);

        // Search by applicant name, email, or application number
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('application.applicant.user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('application', function ($aq) use ($search) {
                    $aq->where('application_number', 'like', "%{$search}%");
                })
                ->orWhere('consent_version', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by policy/terms version
        if ($request->has('policy_version') && !empty($request->policy_version)) {
            $query->where('consent_version', $request->policy_version);
        }

        // Filter by academic year (admission cycle)
        if ($request->has('cycle') && !empty($request->cycle)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('academic_year_id', $request->cycle);
            });
        }

        // Filter by programme
        if ($request->has('programme') && !empty($request->programme)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('programme_id', $request->programme);
            });
        }

        // Filter by specific applicant (History check)
        if ($request->has('applicant_id') && !empty($request->applicant_id)) {
            $query->where('applicant_id', $request->applicant_id);
        }

        $logs = $query->latest('consented_at')->paginate(15, ['*'], 'logs_page');

        // Fetch distinct programmes, academic years and versions for filters
        $programmesList = \App\Models\Programme::orderBy('name')->get();
        $cyclesList = \App\Models\AcademicYear::orderBy('name', 'desc')->get();
        $distinctVersions = PrivacyPolicy::select('version')->distinct()->pluck('version');

        // Pending Acceptance List (Applicants who have not accepted the latest versions)
        $latestPolicy = PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $latestVersion = $latestPolicy ? $latestPolicy->version : null;

        $pendingQuery = \App\Models\Applicant::with(['user', 'applications' => function($q) {
            $q->latest();
        }]);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $pendingQuery->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($latestVersion) {
            $pendingQuery->where(function($q) use ($latestVersion) {
                $q->whereNull('privacy_policy_version')
                  ->orWhere('privacy_policy_version', '!=', $latestVersion)
                  ->orWhereNull('consent_status')
                  ->orWhere('consent_status', '!=', 'accepted');
            });
        }

        $pendingApplicants = $pendingQuery->paginate(15, ['*'], 'pending_page');

        // Stats
        $stats = [
            'total_accepted' => ApplicationConsent::where('consent_given', true)->count(),
            'total_withdrawn' => ApplicationConsent::whereNotNull('withdrawn_at')->count(),
            'by_policy_version' => ApplicationConsent::select('consent_version', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('consent_version')
                ->get(),
            'by_cycle' => ApplicationConsent::select('academic_years.name as cycle', \Illuminate\Support\Facades\DB::raw('count(application_consents.id) as count'))
                ->join('applications', 'application_consents.application_id', '=', 'applications.id')
                ->join('academic_years', 'applications.academic_year_id', '=', 'academic_years.id')
                ->groupBy('academic_years.name')
                ->get(),
        ];

        return view('admin.compliance.index', compact(
            'policies', 'terms', 'logs', 'stats', 'programmesList', 'cyclesList', 'distinctVersions', 'pendingApplicants', 'latestVersion'
        ));
    }

    public function storePrivacy(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'version' => 'required|string|unique:privacy_policies,version',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'consent_file' => 'nullable|file|mimes:pdf|max:10240',
            'effective_date' => 'nullable|date',
            'language' => 'required|string|max:10',
        ]);

        if (empty($validated['content']) && !$request->hasFile('consent_file')) {
            return redirect()->back()->withErrors(['content' => 'You must provide either text content or upload a written consent document.'])->withInput();
        }

        if ($request->hasFile('consent_file')) {
            $path = $request->file('consent_file')->store('consent_forms', 'public');
            $validated['file_path'] = $path;
        }

        $validated['status'] = 'Draft';
        $validated['published_by'] = Auth::id();

        PrivacyPolicy::create($validated);

        return redirect()->back()->with('success', 'Privacy Policy Draft created successfully.');
    }

    public function publishPrivacy(PrivacyPolicy $policy)
    {
        $this->checkAccess();

        // Archive current published policies
        PrivacyPolicy::where('status', 'Published')->update(['status' => 'Archived']);

        $policy->update([
            'status' => 'Published',
            'effective_date' => now()->toDateString(),
            'published_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::log('compliance_policy_published', "Published Privacy Policy version {$policy->version}");

        return redirect()->back()->with('success', "Privacy Policy version {$policy->version} published successfully.");
    }

    public function storeTerms(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'version' => 'required|string|unique:terms_conditions,version',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'consent_file' => 'nullable|file|mimes:pdf|max:10240',
            'effective_date' => 'nullable|date',
            'language' => 'required|string|max:10',
        ]);

        if (empty($validated['content']) && !$request->hasFile('consent_file')) {
            return redirect()->back()->withErrors(['content' => 'You must provide either text content or upload a written Terms & Conditions document.'])->withInput();
        }

        if ($request->hasFile('consent_file')) {
            $path = $request->file('consent_file')->store('consent_forms', 'public');
            $validated['file_path'] = $path;
        }

        $validated['status'] = 'Draft';
        $validated['published_by'] = Auth::id();

        TermsCondition::create($validated);

        return redirect()->back()->with('success', 'Terms & Conditions Draft created successfully.');
    }

    public function publishTerms(TermsCondition $terms)
    {
        $this->checkAccess();

        // Archive current published terms
        TermsCondition::where('status', 'Published')->update(['status' => 'Archived']);

        $terms->update([
            'status' => 'Published',
            'effective_date' => now()->toDateString(),
            'published_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::log('compliance_terms_published', "Published Terms and Conditions version {$terms->version}");

        return redirect()->back()->with('success', "Terms & Conditions version {$terms->version} published successfully.");
    }

    public function editPrivacy(PrivacyPolicy $policy)
    {
        $this->checkAccess();
        if ($policy->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft versions of the Privacy Policy can be edited.');
        }
        return view('admin.compliance.edit_privacy', compact('policy'));
    }

    public function updatePrivacy(Request $request, PrivacyPolicy $policy)
    {
        $this->checkAccess();
        if ($policy->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft versions of the Privacy Policy can be updated.');
        }

        $validated = $request->validate([
            'version' => 'required|string|unique:privacy_policies,version,' . $policy->id,
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'consent_file' => 'nullable|file|mimes:pdf|max:10240',
            'effective_date' => 'nullable|date',
            'language' => 'required|string|max:10',
        ]);

        if (empty($validated['content']) && !$request->hasFile('consent_file') && !$policy->file_path) {
            return redirect()->back()->withErrors(['content' => 'You must provide either text content or upload a written Privacy Policy document.'])->withInput();
        }

        if ($request->hasFile('consent_file')) {
            $path = $request->file('consent_file')->store('consent_forms', 'public');
            $validated['file_path'] = $path;
        }

        $policy->update($validated);

        return redirect()->route('admin.compliance.index')->with('success', 'Privacy Policy draft updated successfully.');
    }

    public function editTerms(TermsCondition $terms)
    {
        $this->checkAccess();
        if ($terms->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft versions of the Terms & Conditions can be edited.');
        }
        return view('admin.compliance.edit_terms', compact('terms'));
    }

    public function updateTerms(Request $request, TermsCondition $terms)
    {
        $this->checkAccess();
        if ($terms->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft versions of the Terms & Conditions can be updated.');
        }

        $validated = $request->validate([
            'version' => 'required|string|unique:terms_conditions,version,' . $terms->id,
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'consent_file' => 'nullable|file|mimes:pdf|max:10240',
            'effective_date' => 'nullable|date',
            'language' => 'required|string|max:10',
        ]);

        if (empty($validated['content']) && !$request->hasFile('consent_file') && !$terms->file_path) {
            return redirect()->back()->withErrors(['content' => 'You must provide either text content or upload a written Terms & Conditions document.'])->withInput();
        }

        if ($request->hasFile('consent_file')) {
            $path = $request->file('consent_file')->store('consent_forms', 'public');
            $validated['file_path'] = $path;
        }

        $terms->update($validated);

        return redirect()->route('admin.compliance.index')->with('success', 'Terms & Conditions draft updated successfully.');
    }

    public function previewPrivacy(PrivacyPolicy $policy)
    {
        $this->checkAccess();
        $document = $policy;
        $type = 'Privacy Policy';
        return view('admin.compliance.preview', compact('document', 'type'));
    }

    public function previewTerms(TermsCondition $terms)
    {
        $this->checkAccess();
        $document = $terms;
        $type = 'Terms & Conditions';
        return view('admin.compliance.preview', compact('document', 'type'));
    }

    public function rollbackPrivacy(PrivacyPolicy $policy)
    {
        $this->checkAccess();
        
        // Set currently published policies to Archived
        PrivacyPolicy::where('status', 'Published')->update(['status' => 'Archived']);

        $policy->update([
            'status' => 'Published',
            'effective_date' => now()->toDateString(),
            'published_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::log('compliance_policy_rollback', "Rolled back to Privacy Policy version {$policy->version}");

        return redirect()->back()->with('success', "Rolled back to Privacy Policy version {$policy->version} successfully.");
    }

    public function rollbackTerms(TermsCondition $terms)
    {
        $this->checkAccess();

        // Set currently published terms to Archived
        TermsCondition::where('status', 'Published')->update(['status' => 'Archived']);

        $terms->update([
            'status' => 'Published',
            'effective_date' => now()->toDateString(),
            'published_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::log('compliance_terms_rollback', "Rolled back to Terms & Conditions version {$terms->version}");

        return redirect()->back()->with('success', "Rolled back to Terms & Conditions version {$terms->version} successfully.");
    }

    public function destroyPrivacy(PrivacyPolicy $policy)
    {
        $this->checkAccess();

        // Delete the file if it exists
        if ($policy->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($policy->file_path);
        }

        $version = $policy->version;
        $policy->delete();

        \App\Services\AuditLogService::log('compliance_policy_deleted', "Deleted Privacy Policy version {$version}");

        return redirect()->route('admin.compliance.index')->with('success', "Privacy Policy version {$version} deleted successfully.");
    }

    public function destroyTerms(TermsCondition $terms)
    {
        $this->checkAccess();

        // Delete the file if it exists
        if ($terms->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($terms->file_path);
        }

        $version = $terms->version;
        $terms->delete();

        \App\Services\AuditLogService::log('compliance_terms_deleted', "Deleted Terms & Conditions version {$version}");

        return redirect()->route('admin.compliance.index')->with('success', "Terms & Conditions version {$version} deleted successfully.");
    }

    public function exportLogs(Request $request)
    {
        $this->checkAccess();

        $query = ApplicationConsent::with(['application.applicant.user', 'privacyPolicy', 'termsCondition']);

        // Search by applicant name, email, or application number
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('application.applicant.user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('application', function ($aq) use ($search) {
                    $aq->where('application_number', 'like', "%{$search}%");
                })
                ->orWhere('consent_version', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by policy/terms version
        if ($request->has('policy_version') && !empty($request->policy_version)) {
            $query->where('consent_version', $request->policy_version);
        }

        // Filter by academic year (admission cycle)
        if ($request->has('cycle') && !empty($request->cycle)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('academic_year_id', $request->cycle);
            });
        }

        // Filter by programme
        if ($request->has('programme') && !empty($request->programme)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('programme_id', $request->programme);
            });
        }

        // Filter by specific applicant (History check)
        if ($request->has('applicant_id') && !empty($request->applicant_id)) {
            $query->where('applicant_id', $request->applicant_id);
        }

        $logs = $query->latest('consented_at')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="consent_audit_report_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Consent ID', 
                'Applicant Name', 
                'Applicant Email', 
                'Application Number', 
                'Policy Version', 
                'Terms Version', 
                'Language', 
                'Source', 
                'Device', 
                'Browser', 
                'OS', 
                'IP Address', 
                'Timestamp', 
                'Hash'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->application->applicant->user->name ?? 'N/A',
                    $log->application->applicant->user->email ?? 'N/A',
                    $log->application->application_number ?? 'N/A',
                    $log->privacyPolicy->version ?? $log->consent_version,
                    $log->termsCondition->version ?? $log->consent_version,
                    $log->consent_language,
                    $log->consent_source,
                    $log->device_type,
                    $log->browser_name,
                    $log->operating_system,
                    $log->ip_address,
                    $log->consented_at ? $log->consented_at->toDateTimeString() : '',
                    $log->consent_hash
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdfLogs(Request $request)
    {
        $this->checkAccess();

        $query = ApplicationConsent::with(['application.applicant.user', 'privacyPolicy', 'termsCondition']);

        // Search by applicant name, email, or application number
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('application.applicant.user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('application', function ($aq) use ($search) {
                    $aq->where('application_number', 'like', "%{$search}%");
                })
                ->orWhere('consent_version', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filter by policy/terms version
        if ($request->has('policy_version') && !empty($request->policy_version)) {
            $query->where('consent_version', $request->policy_version);
        }

        // Filter by academic year (admission cycle)
        if ($request->has('cycle') && !empty($request->cycle)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('academic_year_id', $request->cycle);
            });
        }

        // Filter by programme
        if ($request->has('programme') && !empty($request->programme)) {
            $query->whereHas('application', function ($aq) use ($request) {
                $aq->where('programme_id', $request->programme);
            });
        }

        // Filter by specific applicant (History check)
        if ($request->has('applicant_id') && !empty($request->applicant_id)) {
            $query->where('applicant_id', $request->applicant_id);
        }

        $logs = $query->latest('consented_at')->get();

        $generatedAt = now()->format('d M Y, h:i A');
        $refNumber = 'SUPA/COMP/' . date('Ymd') . '/' . strtoupper(substr(md5(microtime()), 0, 5));

        $logos = [
            'sttc_logo' => Setting::get('sttc_logo') ? asset('storage/' . Setting::get('sttc_logo')) : '',
            'out_logo' => Setting::get('out_logo') ? asset('storage/' . Setting::get('out_logo')) : '',
            'system_logo' => Setting::get('system_logo') ? asset('storage/' . Setting::get('system_logo')) : '',
            'official_seal' => Setting::get('official_seal') ? asset('storage/' . Setting::get('official_seal')) : '',
            'university_name' => Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)"),
        ];

        return view('pdf.compliance-report-pdf', compact('logs', 'generatedAt', 'refNumber', 'logos'));
    }
}
