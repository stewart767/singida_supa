<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Campus;
use App\Models\Position;
use App\Models\JobApplication;
use App\Models\JobApplicationStage;
use App\Repositories\Contracts\VacancyRepositoryInterface;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use App\Services\RecruitmentService;
use App\Services\OfferLetterService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicRecruitmentWebController extends Controller
{
    public function __construct(
        protected VacancyRepositoryInterface $vacancyRepo,
        protected JobApplicationRepositoryInterface $jobAppRepo,
        protected RecruitmentService $recruitmentService,
        protected OfferLetterService $offerLetterService
    ) {}

    public function trackApplicationPage()
    {
        if (!\App\Models\Setting::get('enable_recruitment_module', true)) {
            abort(404);
        }

        return view('public.careers.track-application');
    }

    public function index(Request $request)
    {
        if (!\App\Models\Setting::get('enable_recruitment_module', true) || !\App\Models\Setting::get('enable_public_career_portal', true)) {
            abort(404, 'Careers portal is currently disabled.');
        }

        $query = Vacancy::with(['designation', 'position', 'category', 'campus'])
            ->where('status', 'Published');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('vacancy_number', 'like', "%{$search}%")
                  ->orWhere('required_skills', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('job_category_id', $request->get('category_id'));
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->get('designation_id'));
        }

        if ($request->filled('campus_id')) {
            $query->where('campus_id', $request->get('campus_id'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->get('employment_type'));
        }

        if ($request->filled('deadline')) {
            $query->where('application_deadline', '<=', $request->get('deadline'));
        }

        $vacancies = $query->latest()->paginate(9);
        $categories = JobCategory::where('status', 'active')->get();
        $designations = Designation::where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();

        return view('public.careers.index', compact('vacancies', 'categories', 'designations', 'campuses'));
    }

    public function show(string $vacancy_number)
    {
        if (!\App\Models\Setting::get('enable_recruitment_module', true)) {
            abort(404);
        }

        $vacancy = Vacancy::with(['designation', 'position', 'category', 'campus'])->where('vacancy_number', $vacancy_number)->firstOrFail();

        if ($vacancy->status !== 'Published') {
            abort(404, 'Job vacancy not found or closed.');
        }

        $relatedVacancies = Vacancy::where('job_category_id', $vacancy->job_category_id)
            ->where('id', '!=', $vacancy->id)
            ->where('status', 'Published')
            ->take(3)
            ->get();

        // Log vacancy view event
        \App\Services\AuditLogService::log(
            'vacancy_viewed',
            "Vacancy {$vacancy->vacancy_number} was viewed.",
            [
                'entity_type' => 'Vacancy',
                'entity_id' => $vacancy->id,
            ]
        );

        return view('public.careers.show', compact('vacancy', 'relatedVacancies'));
    }

    public function applyForm(string $vacancy_number)
    {
        if (!\App\Models\Setting::get('enable_recruitment_module', true)) {
            abort(404, 'Careers portal is currently disabled.');
        }

        $vacancy = Vacancy::with(['designation', 'position', 'category', 'campus'])->where('vacancy_number', $vacancy_number)->firstOrFail();

        if ($vacancy->status !== 'Published') {
            abort(404, 'Vacancy is not active.');
        }

        // Save selected job reference in session
        session([
            'selected_job_id' => $vacancy->id,
            'selected_job_number' => $vacancy->vacancy_number,
        ]);

        if (\App\Models\Setting::get('enable_online_applications', true)) {
            $campuses = Campus::where('status', 'active')->get();
            
            $positionType = 'other';
            $designationName = strtolower($vacancy->designation->name ?? '');
            $categoryName = strtolower($vacancy->category->name ?? '');
            $title = strtolower($vacancy->job_title);

            if (str_contains($designationName, 'teacher') || str_contains($designationName, 'tutor') || str_contains($designationName, 'lecturer') || str_contains($designationName, 'instructor') || str_contains($categoryName, 'teaching') || str_contains($title, 'teacher') || str_contains($title, 'developer') || str_contains($title, 'laravel')) {
                $positionType = 'teacher';
            } elseif (str_contains($designationName, 'accountant') || str_contains($designationName, 'finance') || str_contains($categoryName, 'finance') || str_contains($title, 'finance')) {
                $positionType = 'accountant';
            } elseif (str_contains($designationName, 'procurement') || str_contains($designationName, 'supplies') || str_contains($categoryName, 'procurement')) {
                $positionType = 'procurement';
            } elseif (str_contains($designationName, 'hr') || str_contains($designationName, 'human') || str_contains($categoryName, 'hr')) {
                $positionType = 'hr';
            } elseif (str_contains($designationName, 'ict') || str_contains($designationName, 'computer') || str_contains($designationName, 'it ') || str_contains($categoryName, 'ict')) {
                $positionType = 'ict';
            }

            $draft = null;
            if (Auth::check()) {
                $draft = JobApplication::where('user_id', Auth::id())
                    ->where('vacancy_id', $vacancy->id)
                    ->whereNull('submitted_at')
                    ->first();
            }

            return view('public.careers.apply', compact('vacancy', 'campuses', 'positionType', 'draft'));
        }

        $applyUrl = $vacancy->external_url ?: 'https://ajiramarket.co.tz';
        return redirect()->away($applyUrl);
    }

    public function submitApplication(Request $request)
    {
        if (!\App\Models\Setting::get('enable_recruitment_module', true) || !\App\Models\Setting::get('enable_online_applications', true)) {
            abort(404, 'Online applications are disabled.');
        }

        $rules = [
            'vacancy_id' => 'required|exists:vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'region' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'cv_file' => 'required|file',
        ];

        $request->validate($rules);

        // Provision/authenticate guest
        $isGuest = false;
        if (!Auth::check()) {
            $isGuest = true;
            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'ajira_linked' => true,
                ]);

                $applicantRole = \App\Models\Role::where('name', 'applicant')->first();
                if ($applicantRole) {
                    $user->roles()->syncWithoutDetaching([$applicantRole->id]);
                }
            }
            $userId = $user->id;
        } else {
            $userId = Auth::id();
        }

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('applications/cvs', 'public');
        }

        $appNumber = 'SUPA-JOB-' . date('Y') . '-' . str_pad((string) (JobApplication::count() + 1), 6, '0', STR_PAD_LEFT);

        $application = JobApplication::create([
            'application_number' => $appNumber,
            'user_id' => $userId,
            'vacancy_id' => $request->vacancy_id,
            'status' => 'Applied',
            'current_step' => 11,
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'nida_number' => $request->input('nida_number', '19980515123456789012'),
            'tin_number' => $request->input('tin_number', '123-456-789'),
            'phone' => $request->phone,
            'email' => $request->email,
            'region' => $request->region,
            'district' => $request->district,
            'physical_address' => $request->input('physical_address', 'Singida'),
            'education_history' => $request->input('education', []),
            'experience_history' => $request->input('experience', []),
            'referees' => $request->input('references', []),
            'attachments' => [
                'cv' => $cvPath,
            ],
            'submitted_at' => now(),
        ]);

        JobApplicationStage::create([
            'job_application_id' => $application->id,
            'stage' => 'Applied',
            'comments' => 'Application submitted through direct submission.',
        ]);

        AuditLogService::log('job_application_submitted', "Job Application {$application->application_number} submitted successfully via direct post.");

        if ($isGuest) {
            return redirect()->route('public.careers.index')->with('success', 'Application submitted successfully! Please use your phone number to login once the admin creates and sends your login credentials.');
        }

        return redirect()->route('public.careers.dashboard')->with('success', 'Application submitted successfully.');
    }

    public function saveWizardStep(Request $request)
    {
        $step = (int) $request->input('step');
        $vacancyId = $request->input('vacancy_id');
        $appId = $request->input('application_id');

        $vacancy = Vacancy::findOrFail($vacancyId);

        // Fetch or create application draft
        $application = null;
        if ($appId) {
            $application = JobApplication::find($appId);
        }

        if (Auth::check() && !$application) {
            $application = JobApplication::where('user_id', Auth::id())
                ->where('vacancy_id', $vacancyId)
                ->whereNull('submitted_at')
                ->first();
        }

        // STEP 1: Position selection (confirmation step) - Display only
        if ($step === 1) {
            return response()->json([
                'success' => true, 
                'application_id' => $application ? $application->id : null, 
                'current_step' => 1
            ]);
        }

        // STEP 2: Personal Info (Validation, User Creation, Application Draft Creation/Update)
        // Check dynamic Passport Photo upload first
        if ($step === 2 && $request->hasFile('passport_photo')) {
            $file = $request->file('passport_photo');
            if ($application) {
                $path = $file->store('job_documents/' . $application->application_number . '/attachments', 'public');
                $attachments = $application->attachments ?? [];
                $attachments['passport_photo'] = $path;
                $application->update(['attachments' => $attachments]);
            } else {
                $path = $file->store('job_documents/temp', 'public');
                $request->session()->put('temp_passport_photo', $path);
            }

            return response()->json([
                'success' => true,
                'application_id' => $application ? $application->id : null,
                'path' => $path,
                'csrf_token' => csrf_token()
            ]);
        }

        if ($step === 2) {
            $rules = [
                'full_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date|before:today',
                'nida_number' => 'required|string|max:30',
                'tin_number' => 'nullable|string|max:30',
                'nssf_number' => 'nullable|string|max:30',
                'phone' => 'required|string|max:30',
                'whatsapp_number' => 'required|string|max:30',
                'email' => 'required|email|max:255',
                'region' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'physical_address' => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Check duplicate NIDA, Email, Phone checks on applications for this vacancy
            $dupNida = JobApplication::where('vacancy_id', $vacancyId)->where('nida_number', $request->nida_number);
            $dupEmail = JobApplication::where('vacancy_id', $vacancyId)->where('email', $request->email);
            $dupPhone = JobApplication::where('vacancy_id', $vacancyId)->where('phone', $request->phone);

            if ($application) {
                $dupNida->where('id', '!=', $application->id);
                $dupEmail->where('id', '!=', $application->id);
                $dupPhone->where('id', '!=', $application->id);
            }

            if ($dupNida->exists()) {
                return response()->json(['success' => false, 'errors' => ['nida_number' => ['An application with this NIDA number has already been registered for this vacancy.']]], 422);
            }
            if ($dupEmail->exists()) {
                return response()->json(['success' => false, 'errors' => ['email' => ['An application with this email address has already been registered for this vacancy.']]], 422);
            }
            if ($dupPhone->exists()) {
                return response()->json(['success' => false, 'errors' => ['phone' => ['An application with this phone number has already been registered for this vacancy.']]], 422);
            }

            // If not logged in, dynamically create a guest user
            if (!Auth::check()) {
                $user = \App\Models\User::where('email', $request->email)->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => $request->full_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => Hash::make($request->input('password', Str::random(12))),
                        'role' => 'applicant',
                        'is_active' => true,
                        'email_verified_at' => now(),
                        'ajira_linked' => true,
                    ]);

                    $applicantRole = \App\Models\Role::where('name', 'applicant')->first();
                    if ($applicantRole) {
                        $user->roles()->syncWithoutDetaching([$applicantRole->id]);
                    }
                }
                Auth::login($user);
                $request->session()->regenerate();
                $request->session()->put('is_guest_applicant', true);
            }

            $userId = Auth::id();

            if (!$application) {
                $appNumber = 'SUPA-JOB-' . date('Y') . '-' . str_pad((string) (JobApplication::count() + 1), 6, '0', STR_PAD_LEFT);
                $application = JobApplication::create([
                    'application_number' => $appNumber,
                    'user_id' => $userId,
                    'vacancy_id' => $vacancyId,
                    'status' => 'Draft',
                    'current_step' => 2,
                    'full_name' => $request->full_name,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->date_of_birth,
                    'nida_number' => $request->nida_number,
                    'tin_number' => $request->tin_number,
                    'nssf_number' => $request->nssf_number,
                    'phone' => $request->phone,
                    'whatsapp_number' => $request->whatsapp_number,
                    'email' => $request->email,
                    'region' => $request->region,
                    'district' => $request->district,
                    'physical_address' => $request->physical_address,
                ]);
            } else {
                $application->update([
                    'current_step' => max($application->current_step, 2),
                    'full_name' => $request->full_name,
                    'gender' => $request->gender,
                    'date_of_birth' => $request->date_of_birth,
                    'nida_number' => $request->nida_number,
                    'tin_number' => $request->tin_number,
                    'nssf_number' => $request->nssf_number,
                    'phone' => $request->phone,
                    'whatsapp_number' => $request->whatsapp_number,
                    'email' => $request->email,
                    'region' => $request->region,
                    'district' => $request->district,
                    'physical_address' => $request->physical_address,
                ]);
            }

            // Move temp passport photo file if exists in session
            $attachments = $application->attachments ?? [];
            if ($request->session()->has('temp_passport_photo')) {
                $tempPath = $request->session()->pull('temp_passport_photo');
                $correctPath = 'job_documents/' . $application->application_number . '/attachments/' . basename($tempPath);
                
                if (Storage::disk('public')->exists($tempPath)) {
                    // Make sure folder directory exists
                    $dir = dirname(Storage::disk('public')->path($correctPath));
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    Storage::disk('public')->move($tempPath, $correctPath);
                    $attachments['passport_photo'] = $correctPath;
                    $application->update(['attachments' => $attachments]);
                }
            }

            return response()->json([
                'success' => true,
                'application_id' => $application->id,
                'current_step' => $application->current_step,
                'csrf_token' => csrf_token()
            ]);
        }

        // Ensure application exists for subsequent steps
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application draft not initialized.'], 400);
        }

        // STEP 3: Experience (STTC Work & Other Experience)
        if ($step === 3) {
            $application->update([
                'worked_at_sttc' => (bool)$request->worked_at_sttc,
                'sttc_experience' => $request->sttc_experience,
                'experience_history' => $request->experience_history ?? [],
                'current_step' => max($application->current_step, 3),
            ]);
            return response()->json(['success' => true, 'application_id' => $application->id, 'current_step' => $application->current_step]);
        }

        // STEP 4: Education & Certificate File Upload
        if ($step === 4) {
            $eduHistory = $request->input('education_history', []);
            
            // Handle certificates files upload
            if ($request->hasFile('certificates')) {
                foreach ($request->file('certificates') as $idx => $file) {
                    if (isset($eduHistory[$idx])) {
                        $path = $file->store('job_documents/' . $application->application_number . '/education', 'public');
                        $eduHistory[$idx]['certificate_path'] = $path;
                    }
                }
            }

            $application->update([
                'education_history' => $eduHistory,
                'current_step' => max($application->current_step, 4),
            ]);
            return response()->json(['success' => true, 'application_id' => $application->id, 'current_step' => $application->current_step]);
        }

        // STEP 5: ICT Competency
        if ($step === 5) {
            $application->update([
                'ict_description' => $request->ict_description,
                'ict_skills' => $request->ict_skills ?? [],
                'current_step' => max($application->current_step, 5),
            ]);
            return response()->json(['success' => true, 'application_id' => $application->id, 'current_step' => $application->current_step]);
        }

        // STEP 6: Professional Qualifications / Teaching Experience
        if ($step === 6) {
            // Handle dynamic qualification certificate file upload
            if ($request->hasFile('qualification_file')) {
                $file = $request->file('qualification_file');
                $path = $file->store('job_documents/' . $application->application_number . '/qualifications', 'public');
                return response()->json([
                    'success' => true,
                    'path' => $path,
                    'csrf_token' => csrf_token()
                ]);
            }

            $teachingDetails = [
                'specialized_subjects' => $request->input('teaching_subjects', []),
                'other_subjects' => $request->input('teaching_other_subjects'),
                'years_experience' => $request->input('teaching_years'),
                'level_taught' => $request->input('teaching_level'),
                'institution_taught' => $request->input('teaching_institution'),
            ];

            $qualifications = $request->input('qualifications', []);

            $application->update([
                'professional_qualifications' => [
                    'teaching_details' => $teachingDetails,
                    'qualifications' => $qualifications,
                ],
                'current_step' => max($application->current_step, 6),
            ]);

            return response()->json([
                'success' => true, 
                'application_id' => $application->id, 
                'current_step' => $application->current_step
            ]);
        }

        // STEP 7: Motivation Letter
        if ($step === 7) {
            $application->update([
                'motivation_letter' => $request->motivation_letter,
                'current_step' => max($application->current_step, 7),
            ]);
            return response()->json(['success' => true, 'application_id' => $application->id, 'current_step' => $application->current_step]);
        }

        // STEP 8: Attachments Uploads
        if ($step === 8) {
            $attachments = $application->attachments ?? [];
            $docTypes = [
                'cv', 'cover_letter', 'academic_certificates', 
                'academic_transcripts', 'birth_certificate', 'nida', 'passport_photo',
                'nssf', 'tin', 'professional_membership', 'recommendation_letter', 'training_certificates'
            ];

            foreach ($docTypes as $docType) {
                if ($request->hasFile($docType)) {
                    $path = $request->file($docType)->store('job_documents/' . $application->application_number . '/attachments', 'public');
                    $attachments[$docType] = $path;
                }
            }

            // Verify vacancy mandatory document requirements ONLY on step continue transition
            $isFileUpload = false;
            foreach ($docTypes as $docType) {
                if ($request->hasFile($docType)) {
                    $isFileUpload = true;
                    break;
                }
            }

            if (!$isFileUpload) {
                $mandatory = ['cv', 'cover_letter', 'academic_certificates', 'academic_transcripts', 'nida', 'passport_photo'];
                $missing = [];
                foreach ($mandatory as $mDoc) {
                    if (!isset($attachments[$mDoc])) {
                        $missing[] = 'Kiamatisho cha ' . str_replace('_', ' ', strtoupper($mDoc)) . ' ni lazima kupakiwa.';
                    }
                }

                if (!empty($missing)) {
                    return response()->json(['success' => false, 'errors' => ['attachments' => $missing]], 422);
                }
            }

            $application->update([
                'attachments' => $attachments,
                'current_step' => max($application->current_step, 8),
            ]);
            return response()->json(['success' => true, 'application_id' => $application->id, 'current_step' => $application->current_step]);
        }

        // STEP 9: Declaration, Signature Canvas & Final Submit
        if ($step === 9) {
            $rules = [
                'certified_correct' => 'accepted',
                'digital_signature' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $application->update([
                'certified_correct' => true,
                'digital_signature' => $request->digital_signature,
                'declaration_date' => now(),
                'status' => 'Submitted',
                'submitted_at' => now(),
                'current_step' => 9,
            ]);

            // Save stage log
            JobApplicationStage::create([
                'job_application_id' => $application->id,
                'stage' => 'Submitted',
                'comments' => 'Maombi yamewasilishwa kikamilifu.',
            ]);

            AuditLogService::log('job_application_submitted', "Job Application {$application->application_number} submitted successfully.");

            $isGuest = $request->session()->get('is_guest_applicant');
            if ($isGuest) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                session()->flash('success', 'Application submitted successfully! Please use your phone number to login once the admin creates and sends your login credentials.');
            }

            return response()->json([
                'success' => true, 
                'message' => 'Maombi yamewasilishwa kikamilifu', 
                'current_step' => 9,
                'redirect_url' => $isGuest ? route('public.careers.index') : route('public.careers.dashboard')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid step index.'], 400);
    }

    public function downloadPdfPreview(int $id)
    {
        $application = JobApplication::with('vacancy.position')->findOrFail($id);

        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $html = "
        <html>
        <head>
            <title>Preview Application - {$application->application_number}</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; padding: 40px; font-size: 13px; line-height: 1.6; }
                .header { border-bottom: 3px double #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; text-align: center; }
                .logo { font-size: 24px; font-weight: 800; color: #1e3a8a; }
                .title { font-size: 18px; margin-top: 5px; text-transform: uppercase; font-weight: 700; }
                .section { margin-bottom: 25px; }
                .section-title { font-size: 14px; font-weight: 800; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; margin-bottom: 12px; color: #0f172a; text-transform: uppercase; }
                .row { display: flex; margin-bottom: 8px; }
                .label { width: 180px; font-weight: bold; color: #475569; }
                .val { flex: 1; color: #0f172a; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 15px; }
                table, th, td { border: 1px solid #cbd5e1; }
                th, td { padding: 8px; text-align: left; }
                th { background-color: #f8fafc; font-weight: 800; }
                .signature-box { border: 1px solid #cbd5e1; height: 80px; width: 220px; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; margin-top: 10px; }
            </style>
        </head>
        <body onload='window.print()'>
            <div class='header'>
                <div class='logo'>SINGIDA TEACHERS' TRAINING COLLEGE</div>
                <div class='title'>Job Application Profile Summary</div>
                <div><strong>Application No:</strong> {$application->application_number}</div>
            </div>

            <div class='section'>
                <div class='section-title'>1. Personal Details</div>
                <div class='row'><div class='label'>Full Name:</div><div class='val'>{$application->full_name}</div></div>
                <div class='row'><div class='label'>Gender:</div><div class='val'>{$application->gender}</div></div>
                <div class='row'><div class='label'>Date of Birth:</div><div class='val'>".($application->date_of_birth ? $application->date_of_birth->format('d M Y') : 'N/A')."</div></div>
                <div class='row'><div class='label'>NIDA Number:</div><div class='val'>{$application->nida_number}</div></div>
                <div class='row'><div class='label'>TIN Number:</div><div class='val'>{$application->tin_number}</div></div>
                <div class='row'><div class='label'>NSSF Number:</div><div class='val'>{$application->nssf_number}</div></div>
                <div class='row'><div class='label'>Phone:</div><div class='val'>{$application->phone}</div></div>
                <div class='row'><div class='label'>WhatsApp:</div><div class='val'>{$application->whatsapp_number}</div></div>
                <div class='row'><div class='label'>Email:</div><div class='val'>{$application->email}</div></div>
                <div class='row'><div class='label'>Physical Address:</div><div class='val'>{$application->physical_address}, {$application->district}, {$application->region}</div></div>
            </div>

            <div class='section'>
                <div class='section-title'>2. Position Applied For</div>
                <div class='row'><div class='label'>Job Vacancy:</div><div class='val'>".($application->vacancy->job_title ?? 'N/A')."</div></div>
                <div class='row'><div class='label'>Vacancy No:</div><div class='val'>".($application->vacancy->vacancy_number ?? 'N/A')."</div></div>
                <div class='row'><div class='label'>Campus:</div><div class='val'>".($application->vacancy->campus->name ?? 'N/A')."</div></div>
            </div>

            <div class='section'>
                <div class='section-title'>3. Employment History</div>
                <div class='row'><div class='label'>Previous STTC Worker:</div><div class='val'>".($application->worked_at_sttc ? 'YES' : 'NO')."</div></div>
                ";
                if ($application->worked_at_sttc && is_array($application->sttc_experience)) {
                    $sttc = $application->sttc_experience;
                    $html .= "
                    <div class='row'><div class='label'>STTC Campus:</div><div class='val'>".($sttc['campus'] ?? '')."</div></div>
                    <div class='row'><div class='label'>STTC Department:</div><div class='val'>".($sttc['department'] ?? '')."</div></div>
                    <div class='row'><div class='label'>STTC Period:</div><div class='val'>".($sttc['start_year'] ?? '')." - ".($sttc['end_year'] ?? '')."</div></div>
                    <div class='row'><div class='label'>Reason for Leaving:</div><div class='val'>".($sttc['reason_for_leaving'] ?? 'N/A')."</div></div>
                    ";
                }
                
                $html .= "
                <p><strong>Other Experience:</strong></p>
                <table>
                    <thead>
                        <tr>
                            <th>Employer</th>
                            <th>Position</th>
                            <th>Employment Type</th>
                            <th>Years</th>
                            <th>Responsibilities</th>
                        </tr>
                    </thead>
                    <tbody>";
                    if (is_array($application->experience_history)) {
                        foreach ($application->experience_history as $exp) {
                            $html .= "<tr>
                                <td>".($exp['employer'] ?? '')."</td>
                                <td>".($exp['position'] ?? '')."</td>
                                <td>".($exp['employment_type'] ?? 'N/A')."</td>
                                <td>".($exp['start_year'] ?? '')." - ".($exp['end_year'] ?? '')."</td>
                                <td>".($exp['responsibilities'] ?? '')."</td>
                            </tr>";
                        }
                    }
                    $html .= "</tbody>
                </table>
            </div>

            <div class='section'>
                <div class='section-title'>4. Education Details</div>
                <table>
                    <thead>
                        <tr>
                            <th>Institution</th>
                            <th>Level</th>
                            <th>Award</th>
                            <th>Programme</th>
                            <th>Years</th>
                            <th>GPA/Grade</th>
                        </tr>
                    </thead>
                    <tbody>";
                    if (is_array($application->education_history)) {
                        foreach ($application->education_history as $edu) {
                            $html .= "<tr>
                                <td>".($edu['institution'] ?? '')."</td>
                                <td>".($edu['level'] ?? '')."</td>
                                <td>".($edu['award'] ?? '')."</td>
                                <td>".($edu['programme'] ?? '')."</td>
                                <td>".($edu['start_year'] ?? '')." - ".($edu['completion_year'] ?? '')."</td>
                                <td>".($edu['gpa_grade'] ?? '')."</td>
                            </tr>";
                        }
                    }
                    $html .= "</tbody>
                </table>
            </div>

            <div class='section'>
                <div class='section-title'>5. ICT Competency</div>
                <p><strong>Description:</strong> {$application->ict_description}</p>
                <table>
                    <thead>
                        <tr>
                            <th>ICT Skill</th>
                            <th>Proficiency Level</th>
                        </tr>
                    </thead>
                    <tbody>";
                    if (is_array($application->ict_skills)) {
                        foreach ($application->ict_skills as $skill) {
                            $html .= "<tr>
                                <td>".($skill['skill'] ?? '')."</td>
                                <td>".($skill['level'] ?? '')."</td>
                            </tr>";
                        }
                    }
                    $html .= "</tbody>
                </table>
            </div>

            <div class='section'>
                <div class='section-title'>6. Qualifications & Teaching Experience</div>";
                
                $qualHtml = "";
                $profQuals = $application->professional_qualifications;
                if (is_array($profQuals)) {
                    // Render Teaching Experience details
                    if (isset($profQuals['teaching_details']) && !empty($profQuals['teaching_details']['years_experience'])) {
                        $td = $profQuals['teaching_details'];
                        $subjects = implode(', ', $td['specialized_subjects'] ?? []);
                        if (!empty($td['other_subjects'])) {
                            $subjects .= ($subjects ? ', ' : '') . $td['other_subjects'];
                        }
                        $qualHtml .= "
                        <p><strong>Teaching Specialization & Experience:</strong></p>
                        <div class='row'><div class='label'>Subjects Specialization:</div><div class='val'>{$subjects}</div></div>
                        <div class='row'><div class='label'>Years of Experience:</div><div class='val'>".($td['years_experience'] ?? '0')."</div></div>
                        <div class='row'><div class='label'>Level Taught:</div><div class='val'>".($td['level_taught'] ?? 'N/A')."</div></div>
                        <div class='row'><div class='label'>Institution Taught:</div><div class='val'>".($td['institution_taught'] ?? 'N/A')."</div></div>
                        <br/>
                        ";
                    }

                    // Render Qualifications list
                    $quals = $profQuals['qualifications'] ?? [];
                    if (is_array($quals) && count($quals) > 0) {
                        $qualHtml .= "
                        <p><strong>Professional Memberships & Certificates:</strong></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Qualification / Board</th>
                                    <th>Registration Number</th>
                                    <th>Date Issued</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>";
                            foreach ($quals as $q) {
                                $qualHtml .= "<tr>
                                    <td>".($q['name'] ?? '')."</td>
                                    <td>".($q['registration_number'] ?? '')."</td>
                                    <td>".($q['date_issued'] ?? '')."</td>
                                    <td>".($q['expiry_date'] ?: 'N/A')."</td>
                                </tr>";
                            }
                            $qualHtml .= "</tbody>
                        </table>
                        ";
                    }
                }

                if (empty($qualHtml)) {
                    $qualHtml = "<p style='color:#64748b; font-style:italic;'>No teaching specialization or professional qualifications listed.</p>";
                }

                $html .= $qualHtml . "
            </div>

            <div class='section'>
                <div class='section-title'>7. Motivation Letter</div>
                <p style='white-space: pre-wrap; font-style: italic; color: #334155; line-height: 1.8;'>".htmlspecialchars($application->motivation_letter)."</p>
            </div>

            <div class='section'>
                <div class='section-title'>8. Declaration & Digital Signature</div>
                <p>I certify that the information provided in this application is true, correct, and complete to the best of my knowledge.</p>
                <div class='row'><div class='label'>Declaration Date:</div><div class='val'>".($application->declaration_date ? $application->declaration_date->format('d M Y') : 'N/A')."</div></div>
                <div class='row'>
                    <div class='label'>Digital Signature:</div>
                    <div class='val'>
                        <img src='{$application->digital_signature}' style='max-height:60px;' />
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        return response($html)->header('Content-Type', 'text/html');
    }

    public function downloadJd(string $vacancy_number)
    {
        $vacancy = Vacancy::where('vacancy_number', $vacancy_number)->firstOrFail();
        
        $html = "
        <html>
        <head>
            <style>
                body { font-family: sans-serif; color: #333; line-height: 1.5; padding: 20px; }
                h1 { font-size: 24px; color: #1E3A8A; }
                h2 { font-size: 18px; color: #3B82F6; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                .meta { margin-bottom: 20px; font-size: 14px; }
                .section { margin-bottom: 20px; }
            </style>
        </head>
        <body onload='window.print()'>
            <h1>Job Description: {$vacancy->job_title}</h1>
            <div class='meta'>
                <strong>Vacancy Number:</strong> {$vacancy->vacancy_number}<br>
                <strong>Designation:</strong> " . ($vacancy->designation->name ?? 'N/A') . "<br>
                <strong>Employment Type:</strong> {$vacancy->employment_type}<br>
                <strong>Location:</strong> {$vacancy->location}
            </div>

            <div class='section'>
                <h2>Responsibilities</h2>
                <p>" . nl2br($vacancy->responsibilities) . "</p>
            </div>

            <div class='section'>
                <h2>Qualifications</h2>
                <p>" . nl2br($vacancy->qualifications) . "</p>
            </div>
        </body>
        </html>
        ";

        return response($html)->header('Content-Type', 'text/html');
    }

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $applications = JobApplication::with(['vacancy.designation', 'vacancy.position', 'offerLetter', 'interviews', 'writtenTests'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('public.careers.dashboard', compact('applications'));
    }

    public function applyAgain(string $vacancy_number)
    {
        $vacancy = Vacancy::with(['designation', 'position', 'category', 'campus'])
            ->where('vacancy_number', $vacancy_number)
            ->firstOrFail();

        return view('public.careers.apply_again', compact('vacancy'));
    }

    public function signOfferLetter(Request $request, \App\Models\OfferLetter $offerLetter)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        if ($offerLetter->jobApplication->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $success = $this->offerLetterService->signOfferLetter($offerLetter, $request->signature);

        if ($success) {
            $this->recruitmentService->transitionStage(
                $offerLetter->jobApplication,
                'Hired',
                'Offer letter accepted and signed digitally by the candidate.',
                null,
                Auth::user()
            );
            return back()->with('success', 'Offer letter accepted and signed successfully!');
        }

        return back()->with('error', 'Failed to sign the offer letter.');
    }
}
