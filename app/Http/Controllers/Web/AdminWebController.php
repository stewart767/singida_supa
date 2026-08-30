<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Contact;
use App\Models\News;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Services\ReportExporterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminWebController extends Controller
{
    public function __construct(
        protected ApplicationRepositoryInterface $applicationRepo,
        protected ReportExporterService $reportService
    ) {}

    public function dashboard()
    {
        $metrics = $this->reportService->getAnalyticsSummary();
        $recentApplications = Application::with(['applicant.user', 'programme'])->latest()->take(5)->get();
        $recentPayments = Payment::with(['application.applicant.user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('metrics', 'recentApplications', 'recentPayments'));
    }

    public function applications(Request $request)
    {
        $filters = $request->only(['search', 'status', 'programme_id', 'admission_category', 'gender', 'region', 'sort_by', 'sort_order']);
        $applications = $this->applicationRepo->getFilteredApplications($filters);

        $programmes = Programme::orderBy('name')->get();
        $academicYears = \App\Models\AcademicYear::where('is_active', true)->get();
        $intakes = \App\Models\Intake::where('is_active', true)->get();

        $stats = [
            'total' => Application::count(),
            'approved' => Application::where('status', 'Approved')->count(),
            'pending' => Application::whereIn('status', ['Pending Payment', 'Under Review', 'Submitted'])->count(),
            'rejected' => Application::where('status', 'Rejected')->count(),
        ];

        return view('admin.applications.index', compact('applications', 'filters', 'programmes', 'academicYears', 'intakes', 'stats'));
    }

    public function showApplication(Application $application)
    {
        $application->load(['applicant.user', 'academicProfile', 'documents', 'payment', 'admissionLetter', 'activities' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);
        return view('admin.applications.show', compact('application'));
    }
    
    public function storeApplication(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'programme_id' => ['required', 'exists:programmes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'intake_id' => ['required', 'exists:intakes,id'],
            'admission_type' => ['required', 'string', 'in:Diploma,Form Six'],
            'admission_category' => ['required', 'string', 'in:Direct Entry,Foundation'],
            'status' => ['required', 'string', 'in:Draft,Pending Payment,Under Review,Verified,Approved,Rejected,Waitlist'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date'],
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => 'applicant',
                'is_active' => true,
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
            ]);

            $applicantRole = \App\Models\Role::where('name', 'applicant')->first();
            if ($applicantRole) {
                $user->roles()->sync([$applicantRole->id]);
            }

            $applicant = \App\Models\Applicant::create([
                'user_id' => $user->id,
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'whatsapp_number' => $validated['phone'],
            ]);

            $year = date('Y');
            $count = Application::whereYear('created_at', $year)->count() + 1;
            $appNumber = 'SUPA-' . $year . '-' . str_pad((string) $count, 6, '0', STR_PAD_LEFT);

            $application = Application::create([
                'application_number' => $appNumber,
                'applicant_id' => $applicant->id,
                'programme_id' => $validated['programme_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'intake_id' => $validated['intake_id'],
                'admission_type' => $validated['admission_type'],
                'admission_category' => $validated['admission_category'],
                'status' => $validated['status'],
                'submitted_at' => now(),
            ]);

            // Create default academic profile
            \App\Models\AcademicProfile::create([
                'application_id' => $application->id,
                'admission_type' => $validated['admission_type'],
                'acsee_school' => 'OUT Academic Center',
                'acsee_combination' => 'N/A',
                'acsee_points' => 0.0,
                'acsee_number' => 'S' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT) . '/' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT) . '/' . date('Y', strtotime('-1 year')),
            ]);

            // Create default payment
            \App\Models\Payment::create([
                'application_id' => $application->id,
                'control_number' => '99' . str_pad((string) rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                'amount' => (float) Setting::get('application_fee_default', 20000),
                'payment_status' => $validated['status'] === 'Approved' ? 'paid' : 'unpaid',
            ]);

            if ($validated['status'] === 'Approved') {
                $seqNumber = str_pad((string) (\App\Models\AdmissionLetter::count() + 1), 4, '0', STR_PAD_LEFT);
                $admNumber = 'SUPA/ADM/' . date('Y') . '/' . $seqNumber;
                $verificationCode = strtoupper(Str::random(16));

                \App\Models\AdmissionLetter::create([
                    'application_id' => $application->id,
                    'admission_number' => $admNumber,
                    'verification_code' => $verificationCode,
                    'qr_code_hash' => hash('sha256', $admNumber . '|' . $verificationCode),
                    'reporting_date' => now()->addMonths(1),
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Admission Created',
                'description' => "Manually created admission for applicant '{$user->name}' ({$user->email}) with application number '{$appNumber}'",
                'ip_address' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Admission for '{$user->name}' created successfully!",
                    'application' => $application
                ]);
            }

            return redirect()->route('admin.applications.index')->with('success', "Admission for '{$user->name}' created successfully.");
        });
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['application.applicant.user', 'application.programme'])->latest();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('control_number', 'like', "%{$search}%")
                  ->orWhereHas('application.applicant.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15);
        $filters = $request->only(['search', 'status']);
        return view('admin.payments.index', compact('payments', 'filters'));
    }

    public function programmes()
    {
        $programmes = Programme::latest()->get();
        return view('admin.programmes.index', compact('programmes'));
    }

    public function storeProgramme(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:programmes,code',
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'annual_fee' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable',
            'photo_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:5120',
            'entry_requirements' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('programmes', 'public');
            $validated['image'] = $path;
        } elseif ($request->hasFile('image')) {
            $path = $request->file('image')->store('programmes', 'public');
            $validated['image'] = $path;
        }

        $programme = Programme::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Academic Programme created successfully.',
                'programme' => $programme
            ]);
        }

        return redirect()->back()->with('success', 'Academic Programme created successfully.');
    }

    public function updateProgramme(Request $request, Programme $programme)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:programmes,code,' . $programme->id,
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'annual_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'image' => 'nullable|string',
            'entry_requirements' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $programme->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Programme details & photo updated successfully.',
                'programme' => $programme
            ]);
        }

        return redirect()->back()->with('success', 'Programme details & photo updated successfully.');
    }

    public function deleteProgramme(Programme $programme)
    {
        $programme->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Programme deleted from catalog.'
            ]);
        }

        return redirect()->back()->with('success', 'Programme deleted from catalog.');
    }

    public function cms()
    {
        $logos = [
            'sttc_logo' => Setting::get('sttc_logo') ? asset('storage/' . Setting::get('sttc_logo')) : null,
            'out_logo' => Setting::get('out_logo') ? asset('storage/' . Setting::get('out_logo')) : null,
            'official_seal' => Setting::get('official_seal') ? asset('storage/' . Setting::get('official_seal')) : null,
            'registrar_signature' => Setting::get('registrar_signature') ? asset('storage/' . Setting::get('registrar_signature')) : null,
            'system_logo' => Setting::get('system_logo') ? asset('storage/' . Setting::get('system_logo')) : null,
            'login_background_image' => Setting::get('login_background_image') ? asset('storage/' . Setting::get('login_background_image')) : null,
            'university_name' => Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT"),
            'footer_copyright' => Setting::get('footer_copyright', '© ' . date('Y') . ' SUPA / OUT University Admission Management System. All rights reserved.'),
            'developer_name' => Setting::get('developer_name', 'Reliance Solutions & Technology'),
            'developer_url' => Setting::get('developer_url', 'http://www.reliancesolutions.co.tz'),
        ];

        $defaultSliders = [
            ['id' => 1, 'title' => 'Admissions for 2026 / 2027 Are Now Open.', 'subtitle' => 'Experience world-class open, distance, and digital higher education.', 'cta' => 'Apply Now', 'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070', 'status' => 'Active'],
            ['id' => 2, 'title' => 'Study with Excellence & Innovation.', 'subtitle' => 'Choose from over 85 accredited undergraduate, postgraduate, and foundation programmes.', 'cta' => 'Explore Programmes', 'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070', 'status' => 'Active'],
            ['id' => 3, 'title' => 'Your Future Starts Here.', 'subtitle' => 'Begin your academic journey with absolute confidence.', 'cta' => 'Track Application', 'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070', 'status' => 'Active']
        ];
        $banners = Setting::get('cms_hero_sliders', $defaultSliders);
        if (is_array($banners)) {
            foreach ($banners as &$b) {
                if (isset($b['image']) && $b['image'] && !Str::startsWith($b['image'], 'http') && !Str::startsWith($b['image'], 'data:')) {
                    $b['image'] = asset('storage/' . $b['image']);
                }
            }
        }

        $defaultAbout = [
            'title' => Setting::get('about_title', 'Leading the Future of Higher Distance Learning in Africa.'),
            'badge' => Setting::get('about_badge', 'Open & Distance Learning Excellence'),
            'description' => Setting::get('about_description', 'The SUPA / OUT Admission Portal is designed to provide qualified candidates across East Africa and globally with seamless, transparent access to accredited academic qualifications.'),
            'mission' => Setting::get('about_mission', 'To expand accessible higher education through innovative digital technologies.'),
            'vision' => Setting::get('about_vision', 'To be a premier global institution in open & distance university education.'),
            'verificationText' => Setting::get('about_verification_text', 'QR-verified official admission letters with instant validation.'),
            'campusImage' => Setting::get('about_campus_image') ? asset('storage/' . Setting::get('about_campus_image')) : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070',
            'ctaBackgroundImage' => Setting::get('cta_background_image') ? asset('storage/' . Setting::get('cta_background_image')) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070',
            'ctaBadge' => Setting::get('cta_badge', 'Academic Cycle 2026 / 2027'),
            'ctaTitle' => Setting::get('cta_title', 'Ready to Begin Your Academic Journey?'),
            'ctaDescription' => Setting::get('cta_description', 'Take the first step towards securing your university degree. Submit your application online today in less than 10 minutes.'),
        ];

        $defaultFooter = [
            'tagline' => Setting::get('footer_tagline', 'Empowering global learners through accredited open & distance higher education.'),
            'copyright' => Setting::get('footer_copyright', '© 2026 SUPA / Open University Admission Portal. All Rights Reserved.'),
            'phone' => Setting::get('support_phone', '+255 22 2668820'),
            'email' => Setting::get('support_email', 'admissions@supa.ac.tz'),
            'address' => Setting::get('footer_address', 'Kawawa Road, Kinondoni, Dar es Salaam, Tanzania'),
            'facebook' => Setting::get('footer_facebook', 'https://facebook.com/supauniversity'),
            'twitter' => Setting::get('footer_twitter', 'https://twitter.com/supauniversity'),
            'linkedin' => Setting::get('footer_linkedin', 'https://linkedin.com/school/supauniversity'),
            'youtube' => Setting::get('footer_youtube', 'https://youtube.com/supauniversity'),
        ];

        $programmesList = Programme::select('id', 'code', 'name', 'department', 'faculty', 'duration_years', 'annual_fee', 'is_active', 'image')->get()->map(function($p) {
            $img = $p->image;
            if ($img && !Str::startsWith($img, 'http') && !Str::startsWith($img, 'data:')) {
                $img = asset('storage/' . $img);
            }
            if (!$img) {
                $img = match($p->code) {
                    'BAED' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800',
                    'BSCED' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=800',
                    'IMPTE' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800',
                    default => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800'
                };
            }
            return [
                'id' => $p->id,
                'code' => $p->code,
                'name' => $p->name,
                'department' => $p->department ?? '',
                'faculty' => $p->faculty ?? '',
                'duration_years' => $p->duration_years ?? 3,
                'annual_fee' => $p->annual_fee ?? 0,
                'featured' => (bool)$p->is_active,
                'image' => $img
            ];
        });

        // Scan media directory
        $mediaFiles = [];
        if (Storage::disk('public')->exists('media')) {
            $files = Storage::disk('public')->files('media');
            foreach ($files as $idx => $file) {
                $mediaFiles[] = [
                    'id' => $idx + 1,
                    'name' => basename($file),
                    'url' => asset('storage/' . $file),
                    'size' => round(Storage::disk('public')->size($file) / 1024, 1) . ' KB',
                    'date' => date('M d, Y', Storage::disk('public')->lastModified($file))
                ];
            }
        }
        if (empty($mediaFiles)) {
            $mediaFiles = [
                ['id' => 1, 'name' => 'Campus_Main_Gate.jpg', 'url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=800', 'size' => '1.2 MB', 'date' => 'Jul 24, 2026'],
                ['id' => 2, 'name' => 'Graduation_Ceremony_2026.jpg', 'url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800', 'size' => '2.4 MB', 'date' => 'Jul 25, 2026'],
                ['id' => 3, 'name' => 'Library_Digital_Hub.jpg', 'url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800', 'size' => '980 KB', 'date' => 'Jul 25, 2026']
            ];
        }

        $usersList = User::with('roles')->latest()->get()->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role ?? 'User',
                'roles' => $u->roles->map(fn($r) => strtoupper($r->name))->toArray(),
                'status' => $u->is_active ? 'Active' : 'Deactivated',
                'is_locked' => (bool)$u->is_locked,
                'password_force_change' => (bool)$u->password_force_change,
                'email_verified_at' => $u->email_verified_at ? $u->email_verified_at->toIso8601String() : null
            ];
        });

        $auditLogs = AuditLog::with('user')->latest()->take(30)->get()->map(function($a) {
            return [
                'id' => $a->id,
                'event' => '[' . strtoupper($a->action ?? 'Event') . '] ' . $a->description,
                'user' => $a->user->name ?? 'System Auto',
                'timestamp' => $a->created_at ? $a->created_at->diffForHumans() : 'Just now'
            ];
        });

        $systemSettings = [
            'academicYear' => Setting::get('academic_year', '2026/2027 (Active)'),
            'applicationFee' => (float) Setting::get('application_fee_default', 20000),
            'autoCalculatedCategories' => (bool) Setting::get('auto_calculated_categories', true),
            'emailNotifications' => (bool) Setting::get('email_notifications', true),
            'supportEmail' => Setting::get('support_email', 'admissions@supa.ac.tz'),
            'supportPhone' => Setting::get('support_phone', '+255 22 266 8820'),
            'applicantLoginRequired' => (bool) Setting::get('applicant_login_required', false),
            'emailVerificationRequired' => (bool) Setting::get('email_verification_required', false),
            'passwordMinLength' => (int) Setting::get('password_min_length', 8),
            'passwordRequireSpecial' => (bool) Setting::get('password_require_special', false),
            'applicantAutoActivate' => (bool) Setting::get('applicant_auto_activate', true),
            'showNewsAnnouncements' => (bool) Setting::get('show_news_announcements', true),
            'allowMultipleApplications' => (bool) Setting::get('allow_multiple_applications', false),
            'draftExpirationDays' => (int) Setting::get('draft_expiration_days', 30),
            'topAnnouncementBadge' => Setting::get('top_announcement_badge', '2026/2027'),
            'topAnnouncementText' => Setting::get('top_announcement_text', 'Online Admissions Now Open for Undergraduate & Postgraduate Programmes'),
            'topAnnouncementLinkText' => Setting::get('top_announcement_link_text', 'Track Application Status'),
            'topAnnouncementLinkUrl' => Setting::get('top_announcement_link_url', ''),
            'topAnnouncementPhone' => Setting::get('top_announcement_phone', '+255 22 266 8820'),
            'applicationsWithLogin' => Application::where('is_public_submission', false)->count(),
            'applicationsWithoutLogin' => Application::where('is_public_submission', true)->count(),
        ];

        $newsList = News::latest()->get()->map(function($n) {
            return [
                'id' => $n->id,
                'slug' => $n->slug,
                'title' => $n->title,
                'summary' => $n->summary,
                'content' => $n->content,
                'image' => $n->image_path ? (Str::startsWith($n->image_path, 'http') ? $n->image_path : asset('storage/' . $n->image_path)) : null,
                'published_at' => $n->published_at ? $n->published_at->format('Y-m-d') : date('Y-m-d'),
                'is_featured' => (bool)$n->is_featured,
                'created_at' => $n->created_at ? $n->created_at->format('M d, Y') : ''
            ];
        });

        $contactMessages = Contact::latest()->get()->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'subject' => $c->subject,
                'message' => $c->message,
                'is_read' => (bool)$c->is_read,
                'date' => $c->created_at ? $c->created_at->format('M d, Y h:i A') : 'Just now'
            ];
        });

        $contactSettings = [
            'phone' => Setting::get('contact_phone', '+255 22 266 8820 / +255 754 123 456'),
            'email' => Setting::get('contact_email', 'admissions@supa.ac.tz'),
            'whatsapp' => Setting::get('contact_whatsapp', '+255754123456'),
            'address' => Setting::get('contact_address', 'Singida Campus, Main Academic Building, Singida, Tanzania'),
            'hours' => Setting::get('contact_hours', 'Monday - Friday: 08:00 AM - 05:00 PM'),
            'map_url' => Setting::get('contact_map_url', 'https://maps.google.com/?q=Singida')
        ];

        $defaultCategories = [
            ['id' => 1, 'code' => 'UG', 'title' => 'Undergraduate Programmes', 'subtitle' => 'Bachelor of Science, Education, Commerce', 'color' => 'blue', 'is_active' => true],
            ['id' => 2, 'code' => 'PG', 'title' => 'Postgraduate Degrees', 'subtitle' => "Master's & Postgraduate Diplomas", 'color' => 'amber', 'is_active' => true],
            ['id' => 3, 'code' => 'FC', 'title' => 'Foundation Courses', 'subtitle' => 'Bridging courses for Direct Entry', 'color' => 'emerald', 'is_active' => true],
        ];
        $programmeCategories = collect(Setting::get('programme_categories', $defaultCategories))->map(function($c) {
            $c['is_active'] = isset($c['is_active']) ? (bool)$c['is_active'] : true;
            return $c;
        })->toArray();

        $catalogHeader = [
            'title' => Setting::get('catalog_title', 'Academic Catalog'),
            'subtitle' => Setting::get('catalog_subtitle', 'Explore Degrees & Diplomas'),
        ];

        $pageBanners = [
            'programmes' => Setting::get('banner_programmes') ? asset('storage/' . Setting::get('banner_programmes')) : null,
            'requirements' => Setting::get('banner_requirements') ? asset('storage/' . Setting::get('banner_requirements')) : null,
            'track' => Setting::get('banner_track') ? asset('storage/' . Setting::get('banner_track')) : null,
            'news' => Setting::get('banner_news') ? asset('storage/' . Setting::get('banner_news')) : null,
            'contact' => Setting::get('banner_contact') ? asset('storage/' . Setting::get('banner_contact')) : null,
            'careers' => Setting::get('banner_careers') ? asset('storage/' . Setting::get('banner_careers')) : null,
            'downloads' => Setting::get('banner_downloads') ? asset('storage/' . Setting::get('banner_downloads')) : null,
            'faqs' => Setting::get('banner_faqs') ? asset('storage/' . Setting::get('banner_faqs')) : null,
        ];

        $policies = [
            'privacy' => Setting::get('privacy_policy_content', ''),
            'terms' => Setting::get('terms_conditions_content', ''),
        ];

        $roles = \App\Models\Role::with('permissions')->get();
        $permissions = \App\Models\Permission::all();

        return view('admin.cms.index', compact(
            'logos', 'banners', 'defaultAbout', 'defaultFooter', 
            'programmesList', 'mediaFiles', 'usersList', 'auditLogs', 
            'systemSettings', 'newsList', 'contactMessages', 'contactSettings',
            'programmeCategories', 'catalogHeader', 'pageBanners', 'policies',
            'roles', 'permissions'
        ));
    }

    public function updateProgrammeCategories(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'categories' => ['required', 'string'],
        ]);

        $categories = json_decode($request->input('categories'), true) ?? [];

        if ($request->has('title')) Setting::set('catalog_title', $request->title, 'cms', 'string');
        if ($request->has('subtitle')) Setting::set('catalog_subtitle', $request->subtitle, 'cms', 'string');
        Setting::set('programme_categories', $categories, 'cms', 'json');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Programme Categories Updated',
            'description' => 'Updated Navigation Mega-Menu Academic Categories',
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'Academic programme categories updated & navigation menu published successfully!',
            'categories' => Setting::get('programme_categories'),
            'catalog_title' => Setting::get('catalog_title'),
            'catalog_subtitle' => Setting::get('catalog_subtitle'),
        ]);
    }


    public function storeNews(Request $request)
    {
        if ($request->has('published_at')) {
            $val = $request->input('published_at');
            if ($val === 'null' || $val === '') {
                $val = null;
            }
            $request->merge(['published_at' => $val]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'is_featured' => ['nullable'],
            'published_at' => ['nullable', 'date'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }

        $slug = Str::slug($validated['title']) . '-' . time();

        $news = News::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'News Created',
            'description' => "Created news item '{$news->title}'",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'News article published successfully!',
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
                'summary' => $news->summary,
                'content' => $news->content,
                'image' => $news->image_path ? asset('storage/' . $news->image_path) : null,
                'published_at' => $news->published_at ? $news->published_at->format('Y-m-d') : date('Y-m-d'),
                'is_featured' => (bool)$news->is_featured,
                'created_at' => $news->created_at->format('M d, Y')
            ]
        ]);
    }

    public function updateNews(Request $request, News $news)
    {
        if ($request->has('published_at')) {
            $val = $request->input('published_at');
            if ($val === 'null' || $val === '') {
                $val = null;
            }
            $request->merge(['published_at' => $val]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'is_featured' => ['nullable'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data = [
            'title' => $validated['title'],
            'summary' => $validated['summary'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => array_key_exists('published_at', $validated) ? $validated['published_at'] : $news->published_at,
        ];

        if ($news->title !== $validated['title']) {
            $data['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $data['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'News Updated',
            'description' => "Updated news article '{$news->title}'",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'News article updated successfully!',
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->title,
                'summary' => $news->summary,
                'content' => $news->content,
                'image' => $news->image_path ? (Str::startsWith($news->image_path, 'http') ? $news->image_path : asset('storage/' . $news->image_path)) : null,
                'published_at' => $news->published_at ? $news->published_at->format('Y-m-d') : date('Y-m-d'),
                'is_featured' => (bool)$news->is_featured,
                'created_at' => $news->created_at->format('M d, Y')
            ]
        ]);
    }

    public function toggleFeaturedNews(News $news)
    {
        $news->is_featured = !$news->is_featured;
        $news->save();

        return response()->json([
            'message' => "News item '{$news->title}' featured state updated!",
            'is_featured' => $news->is_featured
        ]);
    }

    public function destroyNews(News $news)
    {
        $title = $news->title;
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }
        $news->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'News Deleted',
            'description' => "Deleted news article '{$title}'",
            'ip_address' => request()->ip()
        ]);

        return response()->json(['message' => "News article '{$title}' removed successfully!"]);
    }

    public function updateContactSettings(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'hours' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set('contact_' . $key, $value, 'contact', 'string');
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Contact Settings Updated',
            'description' => 'Updated Contact Page Details and Opening Hours',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => 'Contact Page Info updated successfully!']);
    }

    public function toggleReadContact(Contact $contact)
    {
        $contact->is_read = !$contact->is_read;
        $contact->save();

        return response()->json([
            'message' => 'Message status updated to ' . ($contact->is_read ? 'Read' : 'Unread'),
            'is_read' => $contact->is_read
        ]);
    }

    public function destroyContact(Contact $contact)
    {
        $contact->delete();

        return response()->json(['message' => 'Contact inquiry message deleted.']);
    }


    public function updateLogos(Request $request)
    {
        $request->validate([
            'sttc_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'out_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'official_seal' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'registrar_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'system_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'login_background_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'university_name' => ['nullable', 'string', 'max:255'],
            'footer_copyright' => ['nullable', 'string', 'max:500'],
            'developer_name' => ['nullable', 'string', 'max:255'],
            'developer_url' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['sttc_logo', 'out_logo', 'official_seal', 'registrar_signature', 'system_logo', 'login_background_image'] as $key) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('branding', 'public');
                Setting::set($key, $path, 'branding', 'string');
            }
        }

        if ($request->has('university_name')) {
            Setting::set('university_name', $request->university_name, 'general', 'string');
        }
        if ($request->has('footer_copyright')) {
            Setting::set('footer_copyright', $request->footer_copyright, 'general', 'string');
        }
        if ($request->has('developer_name')) {
            Setting::set('developer_name', $request->developer_name, 'general', 'string');
        }
        if ($request->has('developer_url')) {
            Setting::set('developer_url', $request->developer_url, 'general', 'string');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Institutional logos and developer branding updated successfully!',
                'logos' => [
                    'sttc_logo' => Setting::get('sttc_logo') ? asset('storage/' . Setting::get('sttc_logo')) : null,
                    'out_logo' => Setting::get('out_logo') ? asset('storage/' . Setting::get('out_logo')) : null,
                    'official_seal' => Setting::get('official_seal') ? asset('storage/' . Setting::get('official_seal')) : null,
                    'registrar_signature' => Setting::get('registrar_signature') ? asset('storage/' . Setting::get('registrar_signature')) : null,
                    'system_logo' => Setting::get('system_logo') ? asset('storage/' . Setting::get('system_logo')) : null,
                    'login_background_image' => Setting::get('login_background_image') ? asset('storage/' . Setting::get('login_background_image')) : null,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Institutional logos and developer branding updated successfully!');
    }

    public function updateSliders(Request $request)
    {
        $request->validate([
            'banners' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
        ]);

        $banners = json_decode($request->input('banners'), true) ?? [];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $imageUrl = $path;
            
            $targetId = $request->input('target_id');
            if ($targetId) {
                foreach ($banners as &$b) {
                    if ((string)$b['id'] === (string)$targetId) {
                        $b['image'] = $imageUrl;
                    }
                }
            }
        }

        Setting::set('cms_hero_sliders', $banners, 'cms', 'json');

        $savedBanners = Setting::get('cms_hero_sliders');
        if (is_array($savedBanners)) {
            foreach ($savedBanners as &$b) {
                if (isset($b['image']) && $b['image'] && !Str::startsWith($b['image'], 'http') && !Str::startsWith($b['image'], 'data:')) {
                    $b['image'] = asset('storage/' . $b['image']);
                }
            }
        }

        return response()->json([
            'message' => 'Hero sliders updated and saved successfully!',
            'banners' => $savedBanners
        ]);
    }

    public function updatePageBanners(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'in:programmes,requirements,track,news,contact,careers,downloads,faqs'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
        ]);

        $key = $request->input('key');
        $settingKey = 'banner_' . $key;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            Setting::set($settingKey, $path, 'banners', 'string');

            return response()->json([
                'success' => true,
                'message' => 'Banner background image updated successfully!',
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file uploaded.'
        ], 400);
    }

    public function deletePageBanner(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'in:programmes,requirements,track,news,contact,careers,downloads,faqs'],
        ]);

        $key = $request->input('key');
        $settingKey = 'banner_' . $key;

        Setting::set($settingKey, '', 'banners', 'string');

        return response()->json([
            'success' => true,
            'message' => 'Banner background image removed successfully.'
        ]);
    }

    public function updatePolicySettings(Request $request)
    {
        $request->validate([
            'privacy_policy_content' => ['nullable', 'string'],
            'terms_conditions_content' => ['nullable', 'string'],
        ]);

        if ($request->has('privacy_policy_content')) {
            Setting::set('privacy_policy_content', $request->privacy_policy_content, 'policy', 'string');
        }
        if ($request->has('terms_conditions_content')) {
            Setting::set('terms_conditions_content', $request->terms_conditions_content, 'policy', 'string');
        }

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Policy Update',
            'description' => 'Updated Privacy Policy and Terms & Conditions settings',
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy & Terms updated successfully!'
        ]);
    }

    public function updateAbout(Request $request)
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'verificationText' => ['nullable', 'string'],
            'campus_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'cta_background_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'ctaBadge' => ['nullable', 'string', 'max:255'],
            'ctaTitle' => ['nullable', 'string', 'max:255'],
            'ctaDescription' => ['nullable', 'string'],
        ]);

        if ($request->has('title')) Setting::set('about_title', $request->title, 'cms', 'string');
        if ($request->has('badge')) Setting::set('about_badge', $request->badge, 'cms', 'string');
        if ($request->has('description')) Setting::set('about_description', $request->description, 'cms', 'string');
        if ($request->has('mission')) Setting::set('about_mission', $request->mission, 'cms', 'string');
        if ($request->has('vision')) Setting::set('about_vision', $request->vision, 'cms', 'string');
        if ($request->has('verificationText')) Setting::set('about_verification_text', $request->verificationText, 'cms', 'string');
        if ($request->has('ctaBadge')) Setting::set('cta_badge', $request->ctaBadge, 'cms', 'string');
        if ($request->has('ctaTitle')) Setting::set('cta_title', $request->ctaTitle, 'cms', 'string');
        if ($request->has('ctaDescription')) Setting::set('cta_description', $request->ctaDescription, 'cms', 'string');

        if ($request->hasFile('campus_image')) {
            $path = $request->file('campus_image')->store('cms', 'public');
            Setting::set('about_campus_image', $path, 'cms', 'string');
        }

        if ($request->hasFile('cta_background_image')) {
            $path = $request->file('cta_background_image')->store('cms', 'public');
            Setting::set('cta_background_image', $path, 'cms', 'string');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CMS Update',
            'description' => 'Updated Homepage About University Section and Campus Cover Image',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => 'About University Section updated and saved successfully!']);
    }

    public function updateFooter(Request $request)
    {
        $request->validate([
            'tagline' => ['nullable', 'string', 'max:500'],
            'copyright' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:55'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->has('tagline')) Setting::set('footer_tagline', $request->tagline, 'cms', 'string');
        if ($request->has('copyright')) Setting::set('footer_copyright', $request->copyright, 'general', 'string');
        if ($request->has('phone')) Setting::set('support_phone', $request->phone, 'general', 'string');
        if ($request->has('email')) Setting::set('support_email', $request->email, 'general', 'string');
        if ($request->has('address')) Setting::set('footer_address', $request->address, 'cms', 'string');
        if ($request->has('facebook')) Setting::set('footer_facebook', $request->facebook, 'social', 'string');
        if ($request->has('twitter')) Setting::set('footer_twitter', $request->twitter, 'social', 'string');
        if ($request->has('linkedin')) Setting::set('footer_linkedin', $request->linkedin, 'social', 'string');
        if ($request->has('youtube')) Setting::set('footer_youtube', $request->youtube, 'social', 'string');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CMS Update',
            'description' => 'Updated Footer & Social Contact Information',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => 'Footer & Social Contact details updated successfully!']);
    }

    public function toggleFeaturedProgramme(Request $request)
    {
        $request->validate([
            'programme_id' => ['required', 'exists:programmes,id'],
        ]);

        $programme = Programme::findOrFail($request->programme_id);
        $programme->is_active = !$programme->is_active;
        $programme->save();

        return response()->json(['message' => "Programme {$programme->code} featured state updated to " . ($programme->is_active ? 'Active' : 'Inactive')]);
    }

    public function updateProgrammePhoto(Request $request)
    {
        $request->validate([
            'programme_id' => ['required', 'exists:programmes,id'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'image_url' => ['nullable', 'string'],
        ]);

        $programme = Programme::findOrFail($request->programme_id);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('programmes', 'public');
            $programme->image = $path;
        } elseif ($request->filled('image_url')) {
            $programme->image = $request->image_url;
        }

        $programme->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Programme Photo Updated',
            'description' => "Updated cover photo for programme '{$programme->code}'",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => "Cover photo for {$programme->code} uploaded and saved successfully!",
            'image' => $programme->image ? (Str::startsWith($programme->image, 'http') ? $programme->image : asset('storage/' . $programme->image)) : null
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'media_file' => ['required', 'file', 'mimes:png,jpg,jpeg,svg,webp,pdf', 'max:10240'],
        ]);

        $file = $request->file('media_file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $path = $file->storeAs('media', $fileName, 'public');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Media Upload',
            'description' => "Uploaded media file '{$fileName}' to public storage",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'Media file uploaded successfully!',
            'file' => [
                'name' => $fileName,
                'url' => asset('storage/' . $path),
                'size' => round(Storage::disk('public')->size($path) / 1024, 1) . ' KB',
                'date' => date('M d, Y')
            ]
        ]);
    }

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $path = 'media/' . basename($request->filename);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return response()->json(['message' => 'Media file deleted successfully!']);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'academicYear' => ['nullable', 'string'],
            'applicationFee' => ['nullable', 'numeric'],
            'supportEmail' => ['nullable', 'email'],
            'supportPhone' => ['nullable', 'string'],
            'applicantLoginRequired' => ['nullable', 'boolean'],
            'emailVerificationRequired' => ['nullable', 'boolean'],
            'passwordMinLength' => ['nullable', 'integer', 'min:4', 'max:32'],
            'passwordRequireSpecial' => ['nullable', 'boolean'],
            'applicantAutoActivate' => ['nullable', 'boolean'],
            'showNewsAnnouncements' => ['nullable', 'boolean'],
            'allowMultipleApplications' => ['nullable', 'boolean'],
            'draftExpirationDays' => ['nullable', 'integer', 'min:1', 'max:365'],
            'topAnnouncementBadge' => ['nullable', 'string'],
            'topAnnouncementText' => ['nullable', 'string'],
            'topAnnouncementLinkText' => ['nullable', 'string'],
            'topAnnouncementLinkUrl' => ['nullable', 'string'],
            'topAnnouncementPhone' => ['nullable', 'string'],
        ]);

        if ($request->has('academicYear')) Setting::set('academic_year', $request->academicYear, 'general', 'string');
        if ($request->has('applicationFee')) Setting::set('application_fee_default', $request->applicationFee, 'finance', 'float');
        if ($request->has('supportEmail')) Setting::set('support_email', $request->supportEmail, 'general', 'string');
        if ($request->has('supportPhone')) Setting::set('support_phone', $request->supportPhone, 'general', 'string');
        
        Setting::set('applicant_login_required', $request->boolean('applicantLoginRequired'), 'admission', 'boolean');
        Setting::set('email_verification_required', $request->boolean('emailVerificationRequired'), 'admission', 'boolean');
        Setting::set('password_min_length', $request->integer('passwordMinLength', 8), 'admission', 'integer');
        Setting::set('password_require_special', $request->boolean('passwordRequireSpecial'), 'admission', 'boolean');
        Setting::set('applicant_auto_activate', $request->boolean('applicantAutoActivate'), 'admission', 'boolean');
        Setting::set('show_news_announcements', $request->boolean('showNewsAnnouncements'), 'admission', 'boolean');
        Setting::set('allow_multiple_applications', $request->boolean('allowMultipleApplications'), 'admission', 'boolean');
        Setting::set('draft_expiration_days', $request->integer('draftExpirationDays', 30), 'admission', 'integer');

        if ($request->has('topAnnouncementBadge')) Setting::set('top_announcement_badge', $request->topAnnouncementBadge, 'general', 'string');
        if ($request->has('topAnnouncementText')) Setting::set('top_announcement_text', $request->topAnnouncementText, 'general', 'string');
        if ($request->has('topAnnouncementLinkText')) Setting::set('top_announcement_link_text', $request->topAnnouncementLinkText, 'general', 'string');
        if ($request->has('topAnnouncementLinkUrl')) Setting::set('top_announcement_link_url', $request->topAnnouncementLinkUrl ?? '', 'general', 'string');
        if ($request->has('topAnnouncementPhone')) Setting::set('top_announcement_phone', $request->topAnnouncementPhone, 'general', 'string');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Settings Update',
            'description' => 'Updated System Settings (Academic Year, Fees, Email/Phone, Multiple Apps & Draft Expiry)',
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => 'System settings updated successfully!']);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['nullable', 'string'],
            'roles' => ['nullable', 'array'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['nullable', 'string'],
        ]);

        $password = !empty($validated['password']) ? $validated['password'] : 'password123';
        $status = !empty($validated['status']) ? $validated['status'] : 'Active';

        $rolesInput = [];
        if (!empty($validated['roles'])) {
            $rolesInput = $validated['roles'];
        } elseif (!empty($validated['role'])) {
            $rolesInput = [$validated['role']];
        }

        if (empty($rolesInput)) {
            $rolesInput = ['APPLICANT'];
        }

        $primaryRole = strtoupper($rolesInput[0]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $primaryRole,
            'is_active' => ($status === 'Active'),
            'password' => Hash::make($password),
        ]);

        $roleIds = \App\Models\Role::whereIn('name', array_map('strtolower', $rolesInput))->pluck('id')->toArray();
        $user->roles()->sync($roleIds);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Created',
            'description' => "Created user '{$user->name}' ({$user->email}) with roles: " . implode(', ', $rolesInput),
            'ip_address' => $request->ip()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User account '{$user->name}' created successfully!",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'roles' => $user->roles->map(fn($r) => strtoupper($r->name))->toArray(),
                    'status' => $user->is_active ? 'Active' : 'Deactivated'
                ]
            ]);
        }

        return redirect()->back()->with('success', "User account '{$user->name}' created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['nullable', 'string'],
            'roles' => ['nullable', 'array'],
            'status' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_locked' => ['nullable', 'boolean'],
            'password_force_change' => ['nullable', 'boolean'],
            'email_verified_at' => ['nullable'],
        ]);

        $rolesInput = [];
        if (!empty($validated['roles'])) {
            $rolesInput = $validated['roles'];
        } elseif (!empty($validated['role'])) {
            $rolesInput = [$validated['role']];
        }

        if (empty($rolesInput)) {
            $rolesInput = ['APPLICANT'];
        }

        $primaryRole = strtoupper($rolesInput[0]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $primaryRole,
            'is_active' => ($validated['status'] === 'Active'),
            'is_locked' => $request->boolean('is_locked'),
            'password_force_change' => $request->boolean('password_force_change'),
        ];

        if ($request->has('email_verified_at')) {
            $data['email_verified_at'] = $request->email_verified_at ? now() : null;
        }

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $roleIds = \App\Models\Role::whereIn('name', array_map('strtolower', $rolesInput))->pluck('id')->toArray();
        $user->roles()->sync($roleIds);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Updated',
            'description' => "Updated user '{$user->name}' details. Roles: " . implode(', ', $rolesInput),
            'ip_address' => $request->ip()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User '{$user->name}' updated successfully!",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'roles' => $user->roles->map(fn($r) => strtoupper($r->name))->toArray(),
                    'status' => $user->is_active ? 'Active' : 'Deactivated',
                    'is_locked' => (bool)$user->is_locked,
                    'password_force_change' => (bool)$user->password_force_change,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null
                ]
            ]);
        }

        return redirect()->back()->with('success', "User '{$user->name}' updated successfully.");
    }

    public function toggleUserStatus(User $user)
    {
        $newStatusVal = !$user->is_active;
        $user->update(['is_active' => $newStatusVal]);
        $newStatus = $newStatusVal ? 'Active' : 'Deactivated';
 
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Status Changed',
            'description' => "Changed user '{$user->name}' status to {$newStatus}",
            'ip_address' => request()->ip()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "User '{$user->name}' account set to {$newStatus}."
            ]);
        }

        return redirect()->back()->with('success', "User status updated to {$newStatus}.");
    }

    public function destroyUser(User $user)
    {
        $userName = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Deleted',
            'description' => "Deleted user account '{$userName}'",
            'ip_address' => request()->ip()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User account '{$userName}' deleted successfully."
            ]);
        }

        return redirect()->back()->with('success', "User account '{$userName}' deleted.");
    }

    public function clearAuditLogs()
    {
        AuditLog::truncate();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Audit Purged',
            'description' => 'Audit logs history was purged by Superadmin',
            'ip_address' => request()->ip()
        ]);

        return response()->json(['message' => 'Audit logs cleared successfully!']);
    }

    public function sendCommunicationAlert(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'recipient' => ['required', 'string'],
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Comm Alert',
            'description' => "Dispatched notification alert '{$request->title}' to {$request->recipient}",
            'ip_address' => $request->ip()
        ]);

        return response()->json(['message' => "Communication alert '{$request->title}' dispatched successfully!"]);
    }

    public function exportPdfReport(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('download_reports')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type', 'applications');
        $filters = $request->only(['year', 'month', 'start_date', 'end_date', 'status']);
        $generatedAt = now()->format('d M Y, h:i A');
        $refNumber = 'SUPA/REP/' . date('Ymd') . '/' . strtoupper(substr(md5(microtime()), 0, 5));

        $logos = [
            'sttc_logo' => Setting::get('sttc_logo') ? asset('storage/' . Setting::get('sttc_logo')) : '',
            'out_logo' => Setting::get('out_logo') ? asset('storage/' . Setting::get('out_logo')) : '',
            'system_logo' => Setting::get('system_logo') ? asset('storage/' . Setting::get('system_logo')) : '',
            'official_seal' => Setting::get('official_seal') ? asset('storage/' . Setting::get('official_seal')) : '',
            'registrar_signature' => Setting::get('registrar_signature') ? asset('storage/' . Setting::get('registrar_signature')) : '',
            'university_name' => Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)"),
        ];

        $records = [];
        $metrics = [];
        $reportTitle = 'Official System Report';

        if ($type === 'applications') {
            $reportTitle = 'Student Applications & Udahili Report';
            $records = $this->reportService->getFilteredReportQuery('applications', $filters)->latest()->get();
            $metrics = [
                'total_records' => $records->count(),
                'approved' => $records->where('status', 'Approved')->count(),
                'pending' => $records->whereIn('status', ['Pending Payment', 'Under Review', 'Submitted', 'Verified', 'Waitlist'])->count(),
                'rejected' => $records->where('status', 'Rejected')->count(),
            ];
        } elseif ($type === 'payments') {
            $reportTitle = 'Payments, Control Numbers & Revenue Report';
            $records = $this->reportService->getFilteredReportQuery('payments', $filters)->latest()->get();
            $metrics = [
                'total_records' => $records->count(),
                'total_amount' => $records->where('payment_status', 'paid')->sum('amount'),
                'verified' => $records->where('payment_status', 'paid')->count(),
            ];
        } elseif ($type === 'admitted') {
            $reportTitle = 'Official Admitted Candidates List';
            $records = $this->reportService->getFilteredReportQuery('admitted', $filters)->latest()->get();
            $degreeCount = $records->filter(fn($a) => \Illuminate\Support\Str::contains(strtolower($a->programme->name ?? ''), 'bachelor') || \Illuminate\Support\Str::contains(strtolower($a->programme->code ?? ''), 'ba') || \Illuminate\Support\Str::contains(strtolower($a->programme->code ?? ''), 'bsc'))->count();
            $metrics = [
                'total_records' => $records->count(),
                'degree_count' => $degreeCount,
            ];
        }
        return view('pdf.reports-pdf', compact('type', 'reportTitle', 'records', 'metrics', 'logos', 'generatedAt', 'refNumber'));
    }

    public function createStorageLink(Request $request)
    {
        try {
            $link = public_path('storage');
            
            if (file_exists($link) || is_link($link)) {
                if (is_link($link)) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        exec('rmdir "' . $link . '"');
                    } else {
                        unlink($link);
                    }
                } else {
                    $files = array_diff(scandir($link), ['.', '..']);
                    if (empty($files)) {
                        rmdir($link);
                    } else {
                        $backupName = public_path('storage_backup_' . time());
                        rename($link, $backupName);
                    }
                }
            }

            \Illuminate\Support\Facades\Artisan::call('storage:link');
            
            return response()->json([
                'success' => true,
                'message' => 'Storage symbolic link created successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create symbolic link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fixDatabaseUrls(Request $request)
    {
        try {
            $count = 0;
            
            // 1. Fix programmes table images
            $programmes = Programme::all();
            foreach ($programmes as $programme) {
                $img = $programme->image;
                if ($img && Str::startsWith($img, 'http')) {
                    if (preg_match('/\/storage\/(.+)$/', $img, $matches)) {
                        $programme->image = $matches[1];
                        $programme->save();
                        $count++;
                    }
                }
            }

            // 2. Fix cms_hero_sliders setting
            $sliders = Setting::get('cms_hero_sliders');
            if (is_array($sliders)) {
                $updated = false;
                foreach ($sliders as &$slider) {
                    if (isset($slider['image']) && Str::startsWith($slider['image'], 'http')) {
                        if (preg_match('/\/storage\/(.+)$/', $slider['image'], $matches)) {
                            $slider['image'] = $matches[1];
                            $updated = true;
                            $count++;
                        }
                    }
                }
                if ($updated) {
                    Setting::set('cms_hero_sliders', $sliders, 'cms', 'json');
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully parsed & converted {$count} image URL(s) to relative paths!"
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fix database URLs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:roles,name', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $role = \App\Models\Role::create([
            'name' => strtolower(str_replace([' ', '-'], '_', $validated['name'])),
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Role Created',
            'description' => "Created role '{$role->display_name}'",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->display_name}' created successfully!",
            'role' => $role->load('permissions')
        ]);
    }

    public function updateRolePermissions(Request $request, \App\Models\Role $role)
    {
        $request->validate([
            'permissions' => ['required', 'array'],
        ]);

        $role->permissions()->sync($request->permissions);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Role Permissions Updated',
            'description' => "Updated permissions for role '{$role->display_name}'",
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Permissions for role '{$role->display_name}' updated successfully!",
            'role' => $role->load('permissions')
        ]);
    }

    public function destroyRole(\App\Models\Role $role)
    {
        $roleName = $role->display_name;
        $role->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Role Deleted',
            'description' => "Deleted role '{$roleName}'",
            'ip_address' => request()->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Role '{$roleName}' deleted successfully."
        ]);
    }
}

