<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ProgrammeResource;
use App\Models\AdmissionLetter;
use App\Models\Application;
use App\Models\Contact;
use App\Models\Download;
use App\Models\Event;
use App\Models\Faq;
use App\Models\News;
use App\Models\Programme;
use App\Services\ApplicationVerificationService;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
    protected ApplicationVerificationService $verificationService;

    public function __construct(ApplicationVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function programmes(): JsonResponse
    {
        $programmes = Programme::where('is_active', true)->get();

        return response()->json([
            'programmes' => ProgrammeResource::collection($programmes),
        ]);
    }

    public function trackApplication(Request $request): JsonResponse
    {
        $request->validate([
            'application_number' => ['required', 'string'],
        ]);

        $res = $this->verificationService->findApplicationAndUser($request->application_number);
        if (!$res) {
            return response()->json(['message' => 'Hakuna ombi lililopatikana. (No application found with details provided.)'], 444);
        }

        $app = $res['application'];
        $user = $res['user'];

        // Mask phone number ending in XXXX
        $phone = $user->phone;
        $maskedPhone = substr($phone, 0, 4) . '***' . substr($phone, -4);

        return response()->json([
            'found' => true,
            'application_id' => $app->id,
            'application_number' => $app->application_number,
            'programme' => $app->programme->name ?? 'N/A',
            'admission_category' => $app->admission_category,
            'status' => $app->status,
            'current_step' => $app->current_step,
            'completion_percentage' => $app->completion_percentage,
            'submitted_at' => $app->submitted_at?->toFormattedDateString(),
            'payment_status' => $app->payment->payment_status ?? 'pending',
            'has_admission_letter' => (bool) $app->admissionLetter,
            'masked_phone' => $maskedPhone,
            'user_id' => $user->id,
        ]);
    }

    public function sendResumeOtp(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ]);

        $app = Application::findOrFail($request->application_id);
        $user = \App\Models\User::findOrFail($request->user_id);

        $sent = $this->verificationService->sendVerificationOtp($user, $app);

        if ($sent) {
            return response()->json([
                'success' => true,
                'message' => 'Kodi ya siri imetumwa. (Verification OTP sent successfully.)',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Imeshindikana kutuma kodi ya siri. (Failed to send verification code.)',
        ], 500);
    }

    public function verifyResumeOtp(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'otp_code' => ['required', 'string'],
        ]);

        $app = Application::findOrFail($request->application_id);
        $user = \App\Models\User::findOrFail($request->user_id);

        $verified = $this->verificationService->verifyOtp($user, $request->otp_code, $app);

        if ($verified) {
            return response()->json([
                'success' => true,
                'message' => 'OTP imethibitishwa. (OTP verified successfully.)',
                'redirect_url' => route('applicant.wizard', ['step' => $app->current_step]),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kodi ya siri siyo sahihi au muda wake umeisha. (Invalid or expired verification code.)',
        ], 422);
    }

    public function resumeDirect(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ]);

        $app = Application::findOrFail($request->application_id);
        $user = \App\Models\User::findOrFail($request->user_id);

        // Directly log in the user
        \Illuminate\Support\Facades\Auth::login($user);

        \App\Models\ApplicationActivity::create([
            'application_id' => $app->id,
            'action' => 'Application Resumed',
            'description' => 'Applicant resumed application session directly from tracking without OTP.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Imethibitishwa. (Session resumed successfully.)',
            'redirect_url' => route('applicant.wizard', ['step' => $app->current_step ?: 5]),
        ]);
    }

    public function trackJobApplication(Request $request): JsonResponse
    {
        $request->validate([
            'application_number' => ['required', 'string'],
        ]);

        $searchQuery = trim($request->application_number);
        if (empty($searchQuery)) {
            return response()->json(['message' => 'Tafadhali weka taarifa sahihi za kutafuta.'], 422);
        }

        $user = null;

        // Try searching by job application number
        $jobApp = JobApplication::with('vacancy')->where('application_number', $searchQuery)->first();
        if ($jobApp) {
            $user = $jobApp->user;
        }

        // Try searching by NIDA
        if (!$user) {
            $jobApp = JobApplication::with('vacancy')->where('nida_number', $searchQuery)->first();
            if ($jobApp) {
                $user = $jobApp->user;
            }
        }

        // Try searching by control number (via admission payments)
        if (!$user) {
            $payment = \App\Models\Payment::where('control_number', $searchQuery)->first();
            if ($payment && $payment->application && $payment->application->applicant && $payment->application->applicant->user) {
                $user = $payment->application->applicant->user;
            }
        }

        // Normalize phone number and search user
        if (!$user) {
            $normalized = preg_replace('/[^0-9]/', '', $searchQuery);
            if (str_starts_with($normalized, '255') && strlen($normalized) > 9) {
                $normalized = '0' . substr($normalized, 3);
            }
            
            $user = \App\Models\User::where(function ($q) use ($searchQuery, $normalized) {
                $q->where('phone', $searchQuery);
                if (!empty($normalized)) {
                    $q->orWhere('phone', $normalized);
                    if (strlen($normalized) >= 5) {
                        $q->orWhere('phone', 'like', "%{$normalized}")
                          ->orWhere('phone', 'like', "%" . substr($normalized, 1));
                    }
                }
            })->first();

            if (!$user) {
                // Try searching phone directly on JobApplication
                $jobAppQuery = JobApplication::with('vacancy');
                if (!empty($normalized) && strlen($normalized) >= 5) {
                    $jobAppQuery->where(function ($q) use ($searchQuery, $normalized) {
                        $q->where('phone', $searchQuery)
                          ->orWhere('phone', 'like', "%{$normalized}");
                    });
                } else {
                    $jobAppQuery->where('phone', $searchQuery);
                }
                
                $jobApp = $jobAppQuery->latest()->first();
                if ($jobApp) {
                    $user = $jobApp->user;
                }
            }
        }

        if (!$user) {
            return response()->json(['message' => 'Hakuna maombi ya kazi yaliyopatikana. (No job application found with details provided.)'], 444);
        }

        // Fetch latest job application for the user
        $jobApp = JobApplication::with('vacancy')->where('user_id', $user->id)->latest()->first();

        if (!$jobApp) {
            return response()->json(['message' => 'Hakuna maombi ya kazi yaliyopatikana. (No job application found for this account.)'], 444);
        }

        // Mask phone number ending in XXXX
        $phone = $jobApp->phone ?: ($user->phone ?: '');
        $maskedPhone = '';
        if (strlen($phone) >= 7) {
            $maskedPhone = substr($phone, 0, 4) . '***' . substr($phone, -4);
        } else {
            $maskedPhone = '***' . substr($phone, -3);
        }

        return response()->json([
            'found' => true,
            'application_id' => $jobApp->id,
            'application_number' => $jobApp->application_number,
            'job_title' => $jobApp->vacancy->job_title ?? 'N/A',
            'vacancy_number' => $jobApp->vacancy->vacancy_number ?? 'N/A',
            'status' => $jobApp->status,
            'current_step' => $jobApp->current_step,
            'completion_percentage' => round(($jobApp->current_step / 9) * 100),
            'submitted_at' => $jobApp->submitted_at?->toFormattedDateString(),
            'masked_phone' => $maskedPhone,
            'user_id' => $user->id,
        ]);
    }

    public function resumeJobDirect(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
        ]);

        $app = JobApplication::with('vacancy')->findOrFail($request->application_id);
        $user = \App\Models\User::findOrFail($request->user_id);

        // Directly log in the user
        \Illuminate\Support\Facades\Auth::login($user);

        // Set session variables for guest applicant if they were guest
        if ($user->role === 'applicant') {
            session(['is_guest_applicant' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Imethibitishwa. (Session resumed successfully.)',
            'redirect_url' => route('public.careers.apply', ['vacancy_number' => $app->vacancy->vacancy_number]),
        ]);
    }

    public function news(): JsonResponse
    {
        return response()->json(['news' => News::latest()->get()]);
    }

    public function events(): JsonResponse
    {
        return response()->json(['events' => Event::where('is_active', true)->orderBy('event_date')->get()]);
    }

    public function faqs(): JsonResponse
    {
        return response()->json(['faqs' => Faq::orderBy('order')->get()]);
    }

    public function downloads(): JsonResponse
    {
        return response()->json(['downloads' => Download::all()]);
    }

    public function submitContact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'message' => 'Thank you for reaching out. Your message has been received.',
            'contact_id' => $contact->id,
        ], 201);
    }

    public function downloadAdmissionLetter(string $verificationCode)
    {
        $letter = AdmissionLetter::with(['application.applicant.user', 'application.programme'])
            ->where('verification_code', $verificationCode)
            ->firstOrFail();

        return view('pdf.admission-letter', compact('letter'));
    }
}
