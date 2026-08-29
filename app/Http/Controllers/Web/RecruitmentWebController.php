<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacancyRequest;
use App\Http\Requests\StoreJobCategoryRequest;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\StorePositionRequest;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\Campus;
use App\Models\JobApplication;
use App\Models\Interview;
use App\Models\InterviewScorecard;
use App\Models\WrittenTest;
use App\Models\OfferLetter;
use App\Models\TalentPool;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Repositories\Contracts\VacancyRepositoryInterface;
use App\Services\RecruitmentService;
use App\Services\OfferLetterService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecruitmentWebController extends Controller
{
    public function __construct(
        protected JobApplicationRepositoryInterface $jobAppRepo,
        protected VacancyRepositoryInterface $vacancyRepo,
        protected RecruitmentService $recruitmentService,
        protected OfferLetterService $offerLetterService
    ) {}

    // 1. Dashboard
    public function dashboard()
    {
        $this->authorize('viewRecruitmentDashboard', JobApplication::class);

        $metrics = [
            'total_vacancies' => Vacancy::count(),
            'active_vacancies' => Vacancy::where('status', 'Published')->count(),
            'closed_vacancies' => Vacancy::where('status', 'Closed')->count(),
            'total_applicants' => JobApplication::distinct('user_id')->count('user_id'),
            'applications_today' => JobApplication::whereDate('created_at', now()->toDateString())->count(),
            'under_review' => JobApplication::where('status', 'Under Review')->count(),
            'shortlisted' => JobApplication::where('status', 'Shortlisted')->count(),
            'interview_scheduled' => JobApplication::where('status', 'Interview Scheduled')->count(),
            'written_tests' => JobApplication::where('status', 'Written Test')->count(),
            'final_interviews' => JobApplication::where('status', 'Final Interview')->count(),
            'selected_candidates' => JobApplication::where('status', 'Selected')->count(),
            'rejected_candidates' => JobApplication::where('status', 'Rejected')->count(),
            'offer_letters_sent' => OfferLetter::where('status', 'Sent')->count(),
            'positions_filled' => JobApplication::where('status', 'Hired')->count(),
        ];

        // Chart Data calculations
        $appsByDesig = DB::table('job_applications')
            ->join('vacancies', 'job_applications.vacancy_id', '=', 'vacancies.id')
            ->join('designations', 'vacancies.designation_id', '=', 'designations.id')
            ->select('designations.name as designation_name', DB::raw('count(job_applications.id) as count'))
            ->groupBy('designations.name')
            ->get();

        $appsByPos = DB::table('job_applications')
            ->join('vacancies', 'job_applications.vacancy_id', '=', 'vacancies.id')
            ->join('positions', 'vacancies.position_id', '=', 'positions.id')
            ->select('positions.name as position_name', DB::raw('count(job_applications.id) as count'))
            ->groupBy('positions.name')
            ->get();

        $monthlyTrends = DB::table('job_applications')
            ->select(DB::raw("strftime('%Y-%m', created_at) as month"), DB::raw('count(id) as count'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $genderDistribution = DB::table('job_applications')
            ->select('gender', DB::raw('count(id) as count'))
            ->groupBy('gender')
            ->get();

        // Feed defaults if database is empty for charts to prevent runtime errors
        if ($monthlyTrends->isEmpty()) {
            $monthlyTrends = collect([['month' => date('Y-m'), 'count' => 0]]);
        }

        // External redirect analytics
        $totalRedirects = DB::table('external_application_redirects')->count();

        $redirectsByVacancy = DB::table('external_application_redirects')
            ->join('vacancies', 'external_application_redirects.vacancy_id', '=', 'vacancies.id')
            ->select('vacancies.job_title', 'vacancies.vacancy_number', DB::raw('count(external_application_redirects.id) as count'))
            ->groupBy('vacancies.id', 'vacancies.job_title', 'vacancies.vacancy_number')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $redirectsByProvider = DB::table('external_application_redirects')
            ->select('provider', DB::raw('count(id) as count'))
            ->groupBy('provider')
            ->get();

        $dailyRedirectTrends = DB::table('external_application_redirects')
            ->select(DB::raw("date(redirected_at) as date"), DB::raw('count(id) as count'))
            ->where('redirected_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        if ($dailyRedirectTrends->isEmpty()) {
            $dailyRedirectTrends = collect([['date' => date('Y-m-d'), 'count' => 0]]);
        }

        // Funnel steps: Vacancy viewed, Logged in, Career profile completed, Redirected to Ajira Market
        $funnelVacancyViewed = DB::table('audit_logs')->where('action', 'vacancy_viewed')->count();
        if ($funnelVacancyViewed === 0) {
            $funnelVacancyViewed = Vacancy::where('application_type', 'external')->count() * 10;
        }
        $funnelLoggedIn = DB::table('users')->count();
        $funnelProfileCompleted = DB::table('career_profiles')->count();
        $funnelRedirected = $totalRedirects;

        $funnelData = [
            'viewed' => $funnelVacancyViewed,
            'logged_in' => $funnelLoggedIn,
            'profile_completed' => $funnelProfileCompleted,
            'redirected' => $funnelRedirected,
        ];

        return view('admin.recruitment.dashboard', compact(
            'metrics', 'appsByDesig', 'appsByPos', 'monthlyTrends', 'genderDistribution',
            'totalRedirects', 'redirectsByVacancy', 'redirectsByProvider', 'dailyRedirectTrends', 'funnelData'
        ));
    }

    // 2. Job Categories
    public function categories()
    {
        $this->authorize('manageCategories', JobCategory::class);
        $categories = JobCategory::orderBy('display_order')->get();
        return view('admin.recruitment.categories', compact('categories'));
    }

    public function storeCategory(StoreJobCategoryRequest $request)
    {
        JobCategory::create($request->validated());
        return back()->with('success', 'Job category created successfully.');
    }

    public function updateCategory(StoreJobCategoryRequest $request, JobCategory $category)
    {
        $category->update($request->validated());
        return back()->with('success', 'Job category updated successfully.');
    }

    public function destroyCategory(JobCategory $category)
    {
        $this->authorize('manageCategories', JobCategory::class);
        $category->delete();
        return back()->with('success', 'Job category deleted successfully.');
    }

    // 3. Designations
    public function designations()
    {
        $this->authorize('manageDesignations', Designation::class);
        $designations = Designation::with('headOfDesignation')->get();
        $staff = User::where('role', '!=', 'applicant')->get();
        return view('admin.recruitment.designations', compact('designations', 'staff'));
    }

    public function storeDesignation(StoreDesignationRequest $request)
    {
        Designation::create($request->validated());
        return back()->with('success', 'Designation created successfully.');
    }

    public function updateDesignation(StoreDesignationRequest $request, Designation $designation)
    {
        $designation->update($request->validated());
        return back()->with('success', 'Designation updated successfully.');
    }

    // 3.1. Campuses
    public function campuses()
    {
        $this->authorize('manageVacancies', Vacancy::class);
        $campuses = Campus::all();
        return view('admin.recruitment.campuses', compact('campuses'));
    }

    public function storeCampus(\App\Http\Requests\StoreCampusRequest $request)
    {
        Campus::create($request->validated());
        return back()->with('success', 'Campus created successfully.');
    }

    public function updateCampus(\App\Http\Requests\StoreCampusRequest $request, Campus $campus)
    {
        $campus->update($request->validated());
        return back()->with('success', 'Campus updated successfully.');
    }

    public function deleteCampus(Campus $campus)
    {
        $this->authorize('manageVacancies', Vacancy::class);
        $campus->delete();
        return back()->with('success', 'Campus deleted successfully.');
    }

    // 4. Positions
    public function positions()
    {
        $this->authorize('managePositions', Position::class);
        $positions = Position::with(['designation', 'category', 'reportsTo', 'campus'])->get();
        $designations = Designation::where('status', 'active')->get();
        $categories = JobCategory::where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        return view('admin.recruitment.positions', compact('positions', 'designations', 'categories', 'campuses'));
    }

    public function storePosition(StorePositionRequest $request)
    {
        $data = $request->validated();
        if (empty($data['job_category_id'])) {
            $data['job_category_id'] = JobCategory::first()?->id ?? 1;
        }
        Position::create($data);
        return back()->with('success', 'Position created successfully.');
    }

    public function updatePosition(StorePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        if (empty($data['job_category_id'])) {
            $data['job_category_id'] = JobCategory::first()?->id ?? 1;
        }
        $position->update($data);
        return back()->with('success', 'Position updated successfully.');
    }

    // 5. Vacancy Management
    public function vacancies()
    {
        $this->authorize('viewVacancies', Vacancy::class);
        $vacancies = Vacancy::with(['designation', 'position', 'category', 'campus'])->latest()->get();
        $designations = Designation::where('status', 'active')->get();
        $positions = Position::where('status', 'active')->get();
        $categories = JobCategory::where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        $existingRegions = Vacancy::whereNotNull('recommended_region')->where('recommended_region', '!=', '')->distinct()->pluck('recommended_region');
        return view('admin.recruitment.vacancies', compact('vacancies', 'designations', 'positions', 'categories', 'campuses', 'existingRegions'));
    }

    public function storeVacancy(StoreVacancyRequest $request)
    {
        $data = $request->validated();
        if (empty($data['job_category_id'])) {
            $data['job_category_id'] = JobCategory::first()?->id ?? 1;
        }
        $data['vacancy_number'] = 'VAC-' . date('Y') . '-' . strtoupper(Str::random(6));

        if ($request->hasFile('featured_image_file')) {
            $data['featured_image'] = $request->file('featured_image_file')->store('vacancies', 'public');
        }

        Vacancy::create($data);
        return back()->with('success', 'Vacancy created successfully.');
    }

    public function updateVacancy(StoreVacancyRequest $request, Vacancy $vacancy)
    {
        $data = $request->validated();
        if (empty($data['job_category_id'])) {
            $data['job_category_id'] = JobCategory::first()?->id ?? 1;
        }

        if ($request->hasFile('featured_image_file')) {
            if ($vacancy->featured_image) {
                Storage::disk('public')->delete($vacancy->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image_file')->store('vacancies', 'public');
        }

        $vacancy->update($data);
        return back()->with('success', 'Vacancy updated successfully.');
    }

    public function destroyVacancy(Vacancy $vacancy)
    {
        $this->authorize('manageVacancies', Vacancy::class);
        $this->recruitmentService->deleteVacancy($vacancy);
        return back()->with('success', 'Vacancy deleted successfully.');
    }

    public function toggleVacancyStatus(Vacancy $vacancy, string $action)
    {
        $this->authorize('manageVacancies', Vacancy::class);
        
        switch ($action) {
            case 'publish':
                $this->recruitmentService->publishVacancy($vacancy);
                break;
            case 'close':
                $this->recruitmentService->closeVacancy($vacancy);
                break;
            case 'archive':
                $this->recruitmentService->archiveVacancy($vacancy);
                break;
            case 'duplicate':
                $this->recruitmentService->duplicateVacancy($vacancy);
                break;
        }

        return back()->with('success', 'Vacancy updated successfully.');
    }

    // 6. Applications Directory & Management
    public function applications(Request $request)
    {
        $this->authorize('viewApplications', JobApplication::class);

        $filters = $request->only([
            'search', 'status', 'vacancy_id', 'position_id', 'designation_id',
            'job_category_id', 'gender', 'region', 'district', 'nida_number',
            'sort_by', 'sort_order'
        ]);

        $perPage = (int) $request->input('per_page', 15);
        $applications = $this->jobAppRepo->getFilteredApplications($filters, $perPage);

        $vacancies = Vacancy::orderBy('job_title')->get();
        $designations = Designation::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        $statusCounts = [
            'All' => JobApplication::count(),
            'Applied' => JobApplication::where('status', 'Applied')->count(),
            'Under Review' => JobApplication::where('status', 'Under Review')->count(),
            'Shortlisted' => JobApplication::where('status', 'Shortlisted')->count(),
            'Written Test' => JobApplication::where('status', 'Written Test')->count(),
            'Interview Scheduled' => JobApplication::where('status', 'Interview Scheduled')->count(),
            'Final Interview' => JobApplication::where('status', 'Final Interview')->count(),
            'Selected' => JobApplication::where('status', 'Selected')->count(),
            'Offer Letter' => JobApplication::where('status', 'Offer Letter')->count(),
            'Hired' => JobApplication::where('status', 'Hired')->count(),
            'Rejected' => JobApplication::where('status', 'Rejected')->count(),
        ];

        $metrics = [
            'total' => $statusCounts['All'],
            'pending' => $statusCounts['Applied'] + $statusCounts['Under Review'],
            'shortlisted' => $statusCounts['Shortlisted'],
            'assessment' => $statusCounts['Written Test'] + $statusCounts['Interview Scheduled'] + $statusCounts['Final Interview'],
            'hired' => $statusCounts['Hired'] + $statusCounts['Selected'],
        ];

        return view('admin.recruitment.applications', compact(
            'applications', 'filters', 'vacancies', 'designations', 'positions', 'statusCounts', 'metrics'
        ));
    }

    public function bulkActionApplications(Request $request)
    {
        $this->authorize('viewApplications', JobApplication::class);

        $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:job_applications,id',
            'action' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        $ids = $request->input('application_ids');
        $action = $request->input('action');
        $comments = $request->input('comments') ?? 'Bulk action executed by HR Admin.';

        $applications = JobApplication::whereIn('id', $ids)->get();
        $processedCount = 0;

        foreach ($applications as $app) {
            $this->recruitmentService->transitionStage($app, $action, $comments, null, Auth::user());
            $processedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$processedCount} applicant(s) to '{$action}'."
            ]);
        }

        return back()->with('success', "Successfully updated {$processedCount} applicant(s) to '{$action}'.");
    }

    public function exportApplicationsCsv(Request $request)
    {
        $this->authorize('viewApplications', JobApplication::class);

        $filters = $request->only([
            'search', 'status', 'vacancy_id', 'position_id', 'designation_id',
            'job_category_id', 'gender', 'region', 'district', 'nida_number',
            'sort_by', 'sort_order'
        ]);

        $applications = $this->jobAppRepo->getFilteredApplications($filters, 5000);

        $headers = [
            'Application #',
            'Full Name',
            'Gender',
            'Date of Birth',
            'Phone',
            'WhatsApp',
            'Email',
            'NIDA Number',
            'Region',
            'District',
            'Vacancy Title',
            'Vacancy Number',
            'Designation',
            'Status',
            'Worked at STTC',
            'Date Applied',
        ];

        $rows = [];
        foreach ($applications as $app) {
            $rows[] = [
                $app->application_number,
                $app->full_name,
                ucfirst($app->gender ?? 'N/A'),
                $app->date_of_birth ? $app->date_of_birth->format('Y-m-d') : 'N/A',
                $app->phone,
                $app->whatsapp_number ?? 'N/A',
                $app->email,
                $app->nida_number ?? 'N/A',
                $app->region ?? 'N/A',
                $app->district ?? 'N/A',
                $app->vacancy->job_title ?? 'N/A',
                $app->vacancy->vacancy_number ?? 'N/A',
                $app->vacancy->designation->name ?? 'N/A',
                $app->status,
                $app->worked_at_sttc ? 'Yes' : 'No',
                $app->created_at ? $app->created_at->format('Y-m-d H:i') : 'N/A',
            ];
        }

        $output = fopen('php://temp', 'r+');
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="job_applications_' . date('Ymd_His') . '.csv"');
    }

    public function showApplication(JobApplication $application)
    {
        $this->authorize('viewApplication', $application);
        $application->load(['vacancy.designation', 'vacancy.position', 'stages.assignedHrOfficer', 'interviews.scorecards', 'writtenTests', 'offerLetter']);
        
        // Fetch panel/staff members for scheduling interview modal
        $staff = User::where('role', '!=', 'applicant')->get();

        return view('admin.recruitment.show_application', compact('application', 'staff'));
    }

    // 7. ATS Pipeline (Kanban Board)
    public function ats()
    {
        $this->authorize('viewApplications', JobApplication::class);

        $stages = [
            'Applied', 'Under Review', 'Shortlisted', 'Interview Scheduled', 
            'Written Test', 'Final Interview', 'Reference Check', 
            'Medical Examination', 'Selected', 'Offer Letter', 'Hired', 'Rejected'
        ];

        $applicationsByStage = [];
        foreach ($stages as $stage) {
            $applicationsByStage[$stage] = JobApplication::with(['vacancy.position', 'vacancy.designation'])
                ->where('status', $stage)
                ->get();
        }

        return view('admin.recruitment.ats', compact('stages', 'applicationsByStage'));
    }

    public function updateApplicationStage(Request $request, JobApplication $application)
    {
        $request->validate([
            'stage' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        $this->recruitmentService->transitionStage($application, $request->stage, $request->comments ?? 'Stage updated in pipeline.', null, Auth::user());

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Applicant stage updated successfully!']);
        }

        return back()->with('success', 'Applicant stage updated successfully!');
    }

    public function manageCredentials(Request $request, JobApplication $application)
    {
        $this->authorize('viewApplication', $application);

        $request->validate([
            'password_option' => 'required|in:keep,generate,custom',
            'custom_password' => 'required_if:password_option,custom|nullable|string|min:6',
            'phone' => 'required|string|max:30',
            'whatsapp_number' => 'required|string|max:30',
            'email' => 'required|email|max:255',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:sms,whatsapp,email',
        ]);

        $user = $application->user;

        // Ensure user exists, otherwise create
        if (!$user) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $application->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => \Illuminate\Support\Facades\Hash::make(Str::random(12)),
                    'role' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $applicantRole = \App\Models\Role::where('name', 'applicant')->first();
                if ($applicantRole) {
                    $user->roles()->syncWithoutDetaching([$applicantRole->id]);
                }
            }
            $application->update(['user_id' => $user->id]);
        }

        // Determine password
        $password = null;
        if ($request->password_option === 'generate') {
            $password = Str::random(8);
        } elseif ($request->password_option === 'custom') {
            $password = $request->custom_password;
        }

        if ($password) {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'password_force_change' => false
            ]);
        }

        // Keep user phone, email synced with selected destination if changed
        $user->update([
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        // Send notifications
        $channels = $request->channels;
        $message = "Dear {$application->full_name}, your login credentials for SUPA Careers portal have been created/updated.\n";
        $message .= "Login Identity (Phone Number): {$request->phone}\n";
        if ($password) {
            $message .= "Password: {$password}\n";
        } else {
            $message .= "Password: Use your existing password.\n";
        }
        $message .= "Login URL: " . route('login');

        $emailSent = false;
        $smsSent = false;
        $whatsappSent = false;

        // 1. SMS Dispatch
        if (in_array('sms', $channels)) {
            $smsSent = app(\App\Services\SmsService::class)->send($request->phone, $message);
        }

        // 2. WhatsApp Dispatch
        if (in_array('whatsapp', $channels)) {
            \Illuminate\Support\Facades\Log::info("WHATSAPP SENT to {$request->whatsapp_number}: {$message}");
            $whatsappSent = true;
        }

        // 3. Email Dispatch
        if (in_array('email', $channels)) {
            try {
                \Illuminate\Support\Facades\Mail::to($request->email)->send(
                    new \App\Mail\RecruitmentNotificationMail(
                        $application->full_name,
                        "Your SUPA Careers Login Credentials",
                        nl2br($message)
                    )
                );
                $emailSent = true;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send credentials mail: " . $e->getMessage());
            }
        }

        // Add a stage timeline log for the credentials dispatch
        $sentChannelsStr = implode(', ', array_map('strtoupper', $channels));
        \App\Models\JobApplicationStage::create([
            'job_application_id' => $application->id,
            'stage' => $application->status,
            'assigned_hr_officer_id' => Auth::id(),
            'comments' => "Login credentials created and sent to candidate via [{$sentChannelsStr}].",
            'notification_history' => [
                'email' => $emailSent,
                'sms' => $smsSent,
                'whatsapp' => $whatsappSent,
                'email_recipient' => $request->email,
                'sms_recipient' => $request->phone,
                'whatsapp_recipient' => $request->whatsapp_number,
                'timestamp' => now()->toDateTimeString()
            ]
        ]);

        AuditLogService::log('applicant_credentials_dispatched', "Credentials dispatched to applicant user {$user->id} / application {$application->application_number} via {$sentChannelsStr}.");

        return back()->with('success', "Login credentials updated and successfully dispatched via {$sentChannelsStr}.");
    }

    // 8. Interview Management
    public function interviews()
    {
        $this->authorize('scheduleInterviews', JobApplication::class);

        $interviews = Interview::with(['jobApplication.vacancy.position', 'jobApplication.vacancy.designation'])->latest()->get();
        $staff = User::where('role', '!=', 'applicant')->get();

        return view('admin.recruitment.interviews', compact('interviews', 'staff'));
    }

    public function scheduleInterview(Request $request)
    {
        $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'type' => 'required|in:Physical,Online,Phone',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'venue' => 'nullable|required_if:type,Physical|string|max:255',
            'meeting_link' => 'nullable|required_if:type,Online|url|max:255',
            'instructions' => 'nullable|string',
            'panel_members' => 'required|array',
            'panel_members.*' => 'exists:users,id',
        ]);

        $application = JobApplication::findOrFail($request->job_application_id);

        $interview = Interview::create([
            'job_application_id' => $request->job_application_id,
            'type' => $request->type,
            'date' => $request->date,
            'time' => $request->time,
            'venue' => $request->venue,
            'meeting_link' => $request->meeting_link,
            'instructions' => $request->instructions,
            'panel_members' => $request->panel_members,
        ]);

        // Transition stage to Interview Scheduled
        $this->recruitmentService->transitionStage($application, 'Interview Scheduled', 'Interview scheduled for candidate.', null, Auth::user());

        return back()->with('success', 'Interview scheduled successfully.');
    }

    // 9. Scorecard & Interview Scores
    public function scores()
    {
        $this->authorize('scoreInterviews', JobApplication::class);

        // Fetch completed and pending scorecards / interviews
        $interviews = Interview::with(['jobApplication.vacancy.position', 'scorecards.interviewer'])->latest()->get();

        return view('admin.recruitment.scores', compact('interviews'));
    }

    public function submitScorecard(Request $request)
    {
        $request->validate([
            'interview_id' => 'required|exists:interviews,id',
            'communication' => 'required|integer|min:1|max:10',
            'technical_knowledge' => 'required|integer|min:1|max:10',
            'problem_solving' => 'required|integer|min:1|max:10',
            'leadership' => 'required|integer|min:1|max:10',
            'teamwork' => 'required|integer|min:1|max:10',
            'confidence' => 'required|integer|min:1|max:10',
            'professionalism' => 'required|integer|min:1|max:10',
            'comments' => 'nullable|string',
        ]);

        $scorecard = InterviewScorecard::updateOrCreate(
            [
                'interview_id' => $request->interview_id,
                'interviewer_id' => Auth::id(),
            ],
            $request->all()
        );

        // Log scorecard submit audit
        AuditLogService::log('interview_score_submitted', "Scorecard submitted for interview ID {$request->interview_id}");

        return back()->with('success', 'Scorecard submitted successfully.');
    }

    // 10. Written Tests
    public function writtenTests()
    {
        $this->authorize('shortlist', JobApplication::class);

        $tests = WrittenTest::with(['jobApplication.vacancy.position'])->latest()->get();
        $shortlistedApplications = JobApplication::whereIn('status', ['Shortlisted', 'Written Test'])->get();

        return view('admin.recruitment.written_tests', compact('tests', 'shortlistedApplications'));
    }

    public function assignWrittenTest(Request $request)
    {
        $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'test_name' => 'required|string|max:255',
            'assigned_date' => 'required|date',
            'questions_file' => 'nullable|file|max:5120',
        ]);

        $application = JobApplication::findOrFail($request->job_application_id);
        
        $questionsPath = null;
        if ($request->hasFile('questions_file')) {
            $questionsPath = $request->file('questions_file')->store('written_tests', 'public');
        }

        WrittenTest::create([
            'job_application_id' => $request->job_application_id,
            'test_name' => $request->test_name,
            'assigned_date' => $request->assigned_date,
            'questions_file_path' => $questionsPath,
            'status' => 'Assigned',
        ]);

        $this->recruitmentService->transitionStage($application, 'Written Test', 'Written test assigned: ' . $request->test_name, null, Auth::user());

        return back()->with('success', 'Written test assigned successfully.');
    }

    public function recordTestMarks(Request $request, WrittenTest $test)
    {
        $request->validate([
            'marks' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'script_file' => 'nullable|file|max:5120',
        ]);

        $data = [
            'marks' => $request->marks,
            'comments' => $request->comments,
            'status' => 'Completed',
        ];

        if ($request->hasFile('script_file')) {
            $data['script_file_path'] = $request->file('script_file')->store('written_tests/scripts', 'public');
        }

        $test->update($data);

        return back()->with('success', 'Written test marks recorded successfully.');
    }

    // 11. Final Evaluation
    public function evaluations()
    {
        $this->authorize('evaluate', JobApplication::class);

        $applications = JobApplication::with(['vacancy.position', 'interviews.scorecards', 'writtenTests'])
            ->whereIn('status', ['Final Interview', 'Reference Check', 'Medical Examination', 'Selected'])
            ->get();

        return view('admin.recruitment.evaluation', compact('applications'));
    }

    public function submitFinalDecision(Request $request, JobApplication $application)
    {
        $request->validate([
            'decision' => 'required|in:Selected,Rejected,Request Another Interview,Move Back',
            'comments' => 'required|string',
        ]);

        $decision = $request->decision;

        if ($decision === 'Request Another Interview') {
            $this->recruitmentService->transitionStage($application, 'Interview Scheduled', 'Requesting another interview: ' . $request->comments, null, Auth::user());
        } elseif ($decision === 'Move Back') {
            $this->recruitmentService->transitionStage($application, 'Under Review', 'Moved back to review: ' . $request->comments, null, Auth::user());
        } else {
            // Selected or Rejected
            $this->recruitmentService->transitionStage($application, $decision, $request->comments, null, Auth::user());

            // If rejected and qualified, save pool option handles it or automatically enrolls
            if ($decision === 'Rejected' && Setting::get('enable_talent_pool', true)) {
                // Determine pool category based on position
                $posName = strtolower($application->vacancy->position->name ?? '');
                $category = 'Administrative Pool';
                if (Str::contains($posName, ['graduate', 'intern'])) {
                    $category = 'Graduate Pool';
                } elseif (Str::contains($posName, ['developer', 'engineer', 'technician', 'it', 'web'])) {
                    $category = 'Technical Pool';
                } elseif (Str::contains($posName, ['lecturer', 'professor', 'teacher', 'academic'])) {
                    $category = 'Academic Pool';
                }

                $this->recruitmentService->addToTalentPool($application->user, $category, 'Enrolled automatically via final evaluation rejection. Rejection Comments: ' . $request->comments);
            }
        }

        return back()->with('success', 'Final evaluation decision submitted successfully.');
    }

    // 12. Offer Letters
    public function offerLetters()
    {
        $this->authorize('generateOfferLetters', JobApplication::class);

        $offerLetters = OfferLetter::with('jobApplication.vacancy.position')->latest()->get();
        $selectedApplications = JobApplication::where('status', 'Selected')->get();

        return view('admin.recruitment.offer_letters', compact('offerLetters', 'selectedApplications'));
    }

    public function generateOfferLetter(Request $request)
    {
        $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'salary' => 'required|string|max:100',
            'benefits' => 'required|string',
            'reporting_date' => 'required|date|after_or_equal:today',
            'employment_terms' => 'required|string',
        ]);

        $application = JobApplication::findOrFail($request->job_application_id);

        $offerLetter = $this->offerLetterService->generateOfferLetter($application, $request->all());

        // Transition stage
        $this->recruitmentService->transitionStage($application, 'Offer Letter', 'Offer letter generated.', null, Auth::user());

        return back()->with('success', 'Offer letter generated successfully.');
    }

    // 13. Talent Pool
    public function talentPool(Request $request)
    {
        $this->authorize('manageTalentPool', JobApplication::class);

        $query = TalentPool::with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $candidates = $query->latest()->paginate(15);

        return view('admin.recruitment.talent_pool', compact('candidates'));
    }

    // 14. Reports
    public function reports()
    {
        $this->authorize('viewReports', JobApplication::class);

        // Fetch reporting metrics
        $vacancyPerformance = DB::table('vacancies')
            ->leftJoin('job_applications', 'vacancies.id', '=', 'job_applications.vacancy_id')
            ->select('vacancies.vacancy_number', 'vacancies.job_title', DB::raw('count(job_applications.id) as applicant_count'))
            ->groupBy('vacancies.vacancy_number', 'vacancies.job_title')
            ->get();

        $stageStats = DB::table('job_applications')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('admin.recruitment.reports', compact('vacancyPerformance', 'stageStats'));
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('viewReports', JobApplication::class);

        $headers = ['Application Number', 'Applicant Name', 'Email', 'Phone', 'Vacancy Title', 'Status', 'Date Applied'];
        $rows = [];

        $apps = JobApplication::with('vacancy')->get();
        foreach ($apps as $app) {
            $rows[] = [
                $app->application_number,
                $app->full_name,
                $app->email,
                $app->phone,
                $app->vacancy->job_title ?? 'N/A',
                $app->status,
                $app->created_at->toDateString(),
            ];
        }

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="recruitment_report_' . date('Ymd') . '.csv"');
    }

    // 15. Settings
    public function settings()
    {
        $this->authorize('manageSettings', JobApplication::class);

        $settings = [
            'enable_recruitment_module' => Setting::get('enable_recruitment_module', true),
            'enable_public_career_portal' => Setting::get('enable_public_career_portal', true),
            'enable_recruitment_email_notifications' => Setting::get('enable_recruitment_email_notifications', true),
            'enable_recruitment_sms_notifications' => Setting::get('enable_recruitment_sms_notifications', true),
            'enable_online_applications' => Setting::get('enable_online_applications', true),
            'enable_interview_scheduling' => Setting::get('enable_interview_scheduling', true),
            'enable_offer_letter_generation' => Setting::get('enable_offer_letter_generation', true),
            'enable_talent_pool' => Setting::get('enable_talent_pool', true),

            'recruitment_stages' => Setting::get('recruitment_stages', 'Applied, Screening, Under Review, Shortlisted, Interview, Written Test, Final Interview, Selected, Offer Letter, Hired, Rejected'),
            'education_levels' => Setting::get('education_levels', 'Secondary, Certificate, Diploma, Bachelor, Postgraduate Diploma, Master\'s, PhD, Other'),
            'professional_qualifications' => Setting::get('professional_qualifications', 'CPA, NBAA, PSPTB, HR Registration, Labour Law Training, HR Certification, MBA, Leadership, Governance, Public Administration, Records Management, Office Management'),
            'required_documents' => Setting::get('required_documents', 'cv, cover_letter, academic_certificates, academic_transcripts, birth_certificate, nida, passport_photo, nssf, tin, professional_membership, recommendation_letter, training_certificates'),
            'referee_requirements' => Setting::get('referee_requirements', '3'),
            'ict_skills' => Setting::get('ict_skills', 'Microsoft Word, Excel, PowerPoint, Access, Outlook, Google Workspace, Moodle, Canvas LMS, ERP Systems, Student Information Systems, AI Tools, Internet Research, Email Communication, Graphic Design, Data Analysis, Programming'),
            'ajira_market_registration_url' => Setting::get('ajira_market_registration_url', ''),
        ];

        return view('admin.recruitment.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $this->authorize('manageSettings', JobApplication::class);

        $booleanKeys = [
            'enable_recruitment_module',
            'enable_public_career_portal',
            'enable_recruitment_email_notifications',
            'enable_recruitment_sms_notifications',
            'enable_online_applications',
            'enable_interview_scheduling',
            'enable_offer_letter_generation',
            'enable_talent_pool',
        ];

        foreach ($booleanKeys as $key) {
            Setting::set($key, $request->has($key) ? '1' : '0', 'recruitment', 'boolean');
        }

        $textKeys = [
            'recruitment_stages',
            'education_levels',
            'professional_qualifications',
            'required_documents',
            'referee_requirements',
            'ict_skills',
            'ajira_market_registration_url',
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->get($key), 'recruitment', 'string');
            }
        }

        if ($request->hasFile('login_background_image')) {
            $request->validate([
                'login_background_image' => ['image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120']
            ]);
            $path = $request->file('login_background_image')->store('branding', 'public');
            Setting::set('login_background_image', $path, 'branding', 'string');
        }

        // Log audit
        AuditLogService::log('recruitment_settings_updated', "Recruitment module settings updated");

        return back()->with('success', 'Recruitment settings updated successfully.');
    }
}
