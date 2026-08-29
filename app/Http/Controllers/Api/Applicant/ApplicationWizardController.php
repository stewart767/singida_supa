<?php

namespace App\Http\Controllers\Api\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicProfileRequest;
use App\Http\Requests\StorePersonalInformationRequest;
use App\Http\Requests\SubmitPaymentReceiptRequest;
use App\Http\Requests\UploadDocumentRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Payment;
use App\Repositories\Contracts\ApplicationRepositoryInterface;
use App\Services\ApplicationWorkflowService;
use App\Services\PaymentVerificationService;
use App\Services\SingidaAdmissionClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationWizardController extends Controller
{
    public function __construct(
        protected ApplicationWorkflowService $workflowService,
        protected PaymentVerificationService $paymentService,
        protected ApplicationRepositoryInterface $applicationRepo,
        protected SingidaAdmissionClient $singidaClient
    ) {}

    public function currentApplication(): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        if (!$user->applicant) {
            return response()->json(['application' => null, 'step' => 1]);
        }

        $app = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);

        return response()->json([
            'applicant_profile' => $user->applicant,
            'application' => $app ? new ApplicationResource($app) : null,
        ]);
    }

    public function savePersonalInfo(StorePersonalInformationRequest $request): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        $data = $request->validated();

        if ($request->hasFile('passport_photo')) {
            $data['passport_photo_path'] = $request->file('passport_photo')->store('passports', 'public');
        }

        $applicant = $this->workflowService->createOrUpdateApplicantProfile($user, $data);

        return response()->json([
            'message' => 'Personal information saved successfully.',
            'applicant' => $applicant,
        ]);
    }

    public function saveAcademicProfile(StoreAcademicProfileRequest $request): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        if (!$user->applicant) {
            return response()->json(['message' => 'Please complete Step 2 (Personal Information) first.'], 422);
        }

        $validated = $request->validated();

        try {
            $application = $this->workflowService->initializeOrGetApplication(
                $user->applicant,
                (int) $validated['programme_id'],
                (int) $validated['academic_year_id'],
                (int) $validated['intake_id'],
                $validated['admission_type']
            );

            $oldStep = $application->current_step;

            $this->workflowService->saveAcademicInfo($application, $validated);

            // Request real NMB control number from Singida after academic data is stored.
            $this->workflowService->ensurePaymentWithSingidaControlNumber(
                $application->fresh(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment'])
            );

            if ($oldStep >= 4) {
                $application->update([
                    'current_step' => max($application->current_step, 5),
                    'completion_percentage' => max($application->completion_percentage, 57),
                    'last_activity_at' => now(),
                ]);
                $this->workflowService->logActivity($application, 'Step 4 Completed', 'Applicant completed Programme Selection.');
            }
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Failed to sync application with Singida for an NMB control number.',
            ], 502);
        }

        $application = $application->fresh(['payment', 'academicProfile']);

        return response()->json([
            'message' => 'Academic information saved and admission category calculated.',
            'admission_category' => $application->admission_category,
            'application' => new ApplicationResource($application),
        ]);
    }

    /**
     * Explicitly request / retry NMB control number from Singida for the active application.
     */
    public function requestControlNumber(Request $request, ?Application $application = null): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();

        if (! $application && $user->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
        }

        if (! $application) {
            return response()->json(['message' => 'No active application found.'], 422);
        }

        if ((int) $application->applicant_id !== (int) $user->applicant?->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $payment = $this->workflowService->ensurePaymentWithSingidaControlNumber(
                $application->fresh(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment']),
                force: (bool) $request->boolean('force', false)
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Failed to get NMB control number from Singida.',
            ], 502);
        }

        return response()->json([
            'message' => 'NMB control number ready from Singida.',
            'payment' => [
                'id' => $payment->id,
                'control_number' => $payment->control_number,
                'amount' => $payment->amount,
                'payment_status' => $payment->payment_status,
                'singida_synced' => (bool) $payment->singida_synced,
            ],
            'application' => new ApplicationResource(
                $application->fresh(['payment', 'programme', 'academicProfile', 'academicYear'])
            ),
        ]);
    }

    public function uploadDocument(UploadDocumentRequest $request, ?Application $application = null): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        if (!$application && $user->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
        }

        if (!$application) {
            return response()->json(['message' => 'No active application found.'], 422);
        }

        // Verify that application payment has been verified by admin (status: paid)
        if ($request->document_type !== 'payment_receipt' && $application->payment?->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Tafadhali kamilisha malipo ya ada ya fomu (TZS 20,000) na usubiri uhakiki wa Admin kabla ya kuweka vyeti na nyaraka.',
            ], 403);
        }

        $document = $this->workflowService->handleFileUpload(
            $application,
            $request->document_type,
            $request->file('document')
        );

        return response()->json([
            'message' => 'Document uploaded successfully.',
            'document' => $document,
        ]);
    }

    public function submitPaymentReceipt(SubmitPaymentReceiptRequest $request): JsonResponse
    {
        $this->checkInitialConsent();
        $payment = Payment::findOrFail($request->payment_id);

        $updatedPayment = $this->paymentService->uploadReceipt($payment, $request->file('receipt'));
        if ($request->transaction_reference) {
            $updatedPayment->update(['transaction_reference' => $request->transaction_reference]);
        }

        return response()->json([
            'message' => 'Payment receipt uploaded successfully and is pending verification.',
            'payment' => $updatedPayment,
        ]);
    }

    public function checkPaymentStatus(?Application $application = null): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        if (!$application && $user?->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
        }

        if (!$application) {
            return response()->json(['message' => 'No active application found.'], 404);
        }

        $payment = $application->payment;
        if (!$payment) {
            return response()->json(['message' => 'No payment record found.'], 404);
        }

        // Active automatic payment detection via Singida gateway if not yet paid
        if ($payment->payment_status !== 'paid' && filled($payment->control_number) && !str_starts_with((string) $payment->control_number, 'PENDING-')) {
            try {
                $singidaStatus = $this->singidaClient->checkPaymentStatus(
                    $payment->control_number,
                    $application->application_number
                );

                if ($singidaStatus && (
                    ($singidaStatus['status'] ?? '') === 'paid' ||
                    ($singidaStatus['payment_status'] ?? '') === 'paid' ||
                    ($singidaStatus['is_paid'] ?? false) === true
                )) {
                    $payment->update([
                        'payment_status' => 'paid',
                        'payment_method' => $singidaStatus['payment_method'] ?? 'NMB Bank',
                        'transaction_reference' => $singidaStatus['receipt'] ?? $singidaStatus['transaction_reference'] ?? $payment->transaction_reference,
                        'verified_at' => now(),
                        'singida_synced' => true,
                    ]);

                    if (in_array($application->status, ['Draft', 'Pending Payment'], true)) {
                        $application->update(['status' => 'Under Review']);
                    }
                }
            } catch (\Throwable $e) {
                // Silently continue to return current payment status
            }
        }

        $payment->refresh();

        return response()->json([
            'message' => 'Payment status retrieved.',
            'payment' => [
                'id' => $payment->id,
                'control_number' => $payment->control_number,
                'amount' => $payment->amount,
                'status' => $payment->payment_status,
                'transaction_reference' => $payment->transaction_reference,
                'receipt_url' => $payment->receipt_path ? asset('storage/' . $payment->receipt_path) : '',
                'singida_synced' => (bool) $payment->singida_synced,
                'rejection_reason' => $payment->rejection_reason,
                'verified_at' => $payment->verified_at?->toIso8601String(),
            ],
        ]);
    }

    public function submitFinal(Request $request, ?Application $application = null): JsonResponse
    {
        $this->checkInitialConsent();
        $user = Auth::user();
        if (!$application && $user->applicant) {
            $application = $this->applicationRepo->getApplicantActiveApplication($user->applicant->id);
        }

        if (!$application) {
            return response()->json(['message' => 'No active application found to submit.'], 422);
        }

        $dob = $application->applicant->date_of_birth ?? null;
        $isUnder18 = false;
        if ($dob) {
            $isUnder18 = \Carbon\Carbon::parse($dob)->age < 18;
        }

        $rules = [
            'digital_signature' => ['required', 'string'],
            'confirm_accurate' => ['required', 'accepted'],
            'read_privacy' => ['required', 'accepted'],
            'read_terms' => ['required', 'accepted'],
            'consent_given' => ['required', 'accepted'],
            'understand_penalty' => ['required', 'accepted'],
        ];

        if ($isUnder18) {
            $rules['parent_consent_given'] = ['required', 'accepted'];
            $rules['parent_name'] = ['required', 'string', 'max:255'];
            $rules['parent_signature'] = ['required', 'string', 'max:255'];
        }

        $request->validate($rules, [
            'confirm_accurate.accepted' => 'You must accept all required declarations before submitting your application.',
            'read_privacy.accepted' => 'You must accept the Privacy Policy and Terms & Conditions before submitting your application.',
            'read_terms.accepted' => 'You must accept the Privacy Policy and Terms & Conditions before submitting your application.',
            'consent_given.accepted' => 'You must accept the Privacy Policy and Terms & Conditions before submitting your application.',
            'understand_penalty.accepted' => 'You must accept all required declarations before submitting your application.',
            'parent_consent_given.accepted' => 'Since the applicant is under 18, parent/guardian consent is required.',
            'parent_name.required' => 'Parent/guardian name is required.',
            'parent_signature.required' => 'Parent/guardian signature is required.',
        ]);

        $consentData = [
            'consent_given' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'consent_language' => app()->getLocale(),
            'consent_source' => 'Web',
            'parent_consent_given' => $isUnder18,
            'parent_name' => $isUnder18 ? $request->parent_name : null,
            'parent_signature' => $isUnder18 ? $request->parent_signature : null,
        ];

        $submittedApp = $this->workflowService->submitApplication($application, $request->digital_signature, $consentData);

        try {
            $this->workflowService->ensurePaymentWithSingidaControlNumber(
                $submittedApp->fresh(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment'])
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Application saved, but control number sync failed: '.$e->getMessage(),
                'application' => new ApplicationResource($submittedApp->fresh(['payment'])),
            ], 502);
        }

        return response()->json([
            'message' => 'Application submitted successfully! Your NMB control number has been generated.',
            'application' => new ApplicationResource($submittedApp->fresh(['payment'])),
        ]);
    }

    public function saveGuestCredentials(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $isGuestSession = false;
        try {
            if ($request->hasSession() && $request->session()->has('guest_user_id')) {
                $isGuestSession = $request->session()->get('guest_user_id') == $user->id;
            }
        } catch (\Throwable $e) {}

        if (!$user || (!$isGuestSession && !\Illuminate\Support\Str::contains($user->email, '@supa-guest.com'))) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        if ($request->filled('whatsapp_number')) {
            $cleanedWhatsApp = str_replace([' ', '-', '(', ')'], '', (string) $request->input('whatsapp_number'));
            $request->merge(['whatsapp_number' => $cleanedWhatsApp]);
        } else {
            $request->merge(['whatsapp_number' => null]);
        }

        $phoneInput = $request->input('phone');
        if ($phoneInput) {
            $normalized = \App\Services\ApplicationVerificationService::normalizePhone($phoneInput);
            
            $existingUser = \App\Models\User::where('id', '!=', $user->id)
                ->where(function ($q) use ($phoneInput, $normalized) {
                    $q->where('phone', $phoneInput);
                    if (!empty($normalized)) {
                        $q->orWhere('phone', $normalized);
                        if (strlen($normalized) >= 5) {
                            $q->orWhere('phone', 'like', "%{$normalized}")
                              ->orWhere('phone', 'like', "%" . substr($normalized, 1));
                        }
                    }
                })->first();

            if ($existingUser && $existingUser->applicant) {
                $app = Application::where('applicant_id', $existingUser->applicant->id)
                    ->whereNotIn('status', ['Approved', 'Rejected', 'Expired'])
                    ->latest()
                    ->first();

                if ($app) {
                    $allowMultiple = (bool) \App\Models\Setting::get('allow_multiple_applications', false);

                    if (!$allowMultiple) {
                        return response()->json([
                            'status' => 'duplicate',
                            'message' => 'Namba hii ya simu tayari inatumika katika ombi jingine. (This phone number is already associated with an active application.)',
                            'application_number' => $app->application_number,
                            'phone' => $existingUser->phone,
                        ]);
                    }
                }
            }
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'whatsapp_number' => ['nullable', 'string', 'regex:/^(\+?255|0)[67]\d{8}$/'],
            'consent_given' => ['required', 'accepted'],
        ], [
            'consent_given.accepted' => 'You must accept the admissions consent form to continue.',
            'whatsapp_number.regex' => 'Namba ya WhatsApp lazima iwe namba halali ya simu ya Tanzania (mfano: 0712345678 au +255712345678).',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $version = $activePolicy ? $activePolicy->version : '2.1';

        $application = null;
        if ($user->applicant) {
            $user->applicant->update([
                'whatsapp_number' => $validated['whatsapp_number'] ?? null,
                'initial_consent_given' => true,
                'initial_consent_at' => now(),
                'initial_consent_version' => $version,
            ]);

            // INITIALIZE THE APPLICATION RECORD IMMEDIATELY (Step 1)
            $application = $this->workflowService->initializeOrGetApplication($user->applicant);
        }

        return response()->json([
            'message' => 'Guest credentials updated successfully.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'whatsapp_number' => $user->applicant?->whatsapp_number ?? null,
            ],
            'application' => $application ? new ApplicationResource($application) : null,
        ]);
    }

    public function claimGuestAccount(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'No active session found.'], 422);
        }

        $minLength = \App\Models\Setting::get('password_min_length', 8);
        $requireSpecial = \App\Models\Setting::get('password_require_special', false);

        $passwordRules = ['required', 'string', 'min:' . $minLength, 'confirmed'];
        if ($requireSpecial) {
            $passwordRules[] = 'regex:/[!@#$%^&*(),.?":{}|<>]/';
        }

        $validated = $request->validate([
            'password' => $passwordRules,
        ], [
            'password.regex' => 'The password must contain at least one special character.',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        // Clear guest session
        session()->forget('guest_user_id');

        return response()->json([
            'message' => 'Account password set successfully! You can now log in using your email and password.',
        ]);
    }

    public function saveInitialConsent(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !$user->applicant) {
            return response()->json(['message' => 'Applicant profile not initialized.'], 422);
        }

        $request->validate([
            'consent_given' => ['required', 'accepted'],
        ], [
            'consent_given.accepted' => 'You must accept the admissions consent form to continue.',
        ]);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $version = $activePolicy ? $activePolicy->version : '2.1';

        $user->applicant->update([
            'initial_consent_given' => true,
            'initial_consent_at' => now(),
            'initial_consent_version' => $version,
        ]);

        return response()->json([
            'message' => 'Admissions consent recorded successfully.',
        ]);
    }

    private function checkInitialConsent()
    {
        $user = Auth::user();
        $consentRequired = \App\Models\PrivacyPolicy::where('status', 'Published')->exists() || 
                           \App\Models\TermsCondition::where('status', 'Published')->exists();

        if ($consentRequired && (!$user || !$user->applicant || !$user->applicant->initial_consent_given)) {
            abort(403, 'You must agree to the university admissions consent form to continue.');
        }
    }
}
