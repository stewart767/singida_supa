<?php

namespace App\Services;

use App\Models\AcademicProfile;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Payment;
use App\Models\User;
use App\Models\ApplicationActivity;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationWorkflowService
{
    public function __construct(
        protected AdmissionCategoryCalculatorService $categoryCalculator,
        protected SingidaAdmissionClient $singidaClient
    ) {}

    public function createOrUpdateApplicantProfile(User $user, array $data): Applicant
    {
        return DB::transaction(function () use ($user, $data) {
            $applicant = Applicant::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'gender' => $data['gender'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'nida_number' => $data['nida_number'] ?? null,
                    'voter_id_number' => $data['voter_id_number'] ?? null,
                    'nida_card_number' => $data['nida_card_number'] ?? null,
                    'work_id_number' => $data['work_id_number'] ?? null,
                    'whatsapp_number' => $data['whatsapp_number'] ?? null,
                    'region' => $data['region'] ?? null,
                    'district' => $data['district'] ?? null,
                    'ward' => $data['ward'] ?? null,
                    'nationality' => $data['nationality'] ?? 'Tanzanian',
                    'next_of_kin_name' => $data['next_of_kin_name'] ?? null,
                    'next_of_kin_phone' => $data['next_of_kin_phone'] ?? null,
                    'next_of_kin_relation' => $data['next_of_kin_relation'] ?? null,
                    'passport_photo_path' => $data['passport_photo_path'] ?? null,
                ]
            );

            $application = Application::where('applicant_id', $applicant->id)->latest('id')->first();
            if ($application) {
                $application->update([
                    'current_step' => max($application->current_step, 3),
                    'completion_percentage' => max($application->completion_percentage, 28),
                    'last_activity_at' => now(),
                ]);
                $this->logActivity($application, 'Step 2 Completed', 'Applicant completed Personal Information.');
            }

            AuditLogService::log('applicant_profile_updated', "Updated profile for user ID {$user->id}");

            return $applicant;
        });
    }

    public function logActivity(Application $application, string $action, string $description): ApplicationActivity
    {
        return ApplicationActivity::create([
            'application_id' => $application->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    public function initializeOrGetApplication(Applicant $applicant, ?int $programmeId = null, ?int $academicYearId = null, ?int $intakeId = null, ?string $admissionType = null): Application
    {
        return DB::transaction(function () use ($applicant, $programmeId, $academicYearId, $intakeId, $admissionType) {
            // Find existing active draft/in-progress application first
            $application = Application::where('applicant_id', $applicant->id)
                ->whereNotIn('status', ['Approved', 'Rejected', 'Expired'])
                ->latest('id')
                ->first();

            if ($application) {
                $updates = [];
                if ($programmeId) $updates['programme_id'] = $programmeId;
                if ($academicYearId) $updates['academic_year_id'] = $academicYearId;
                if ($intakeId) $updates['intake_id'] = $intakeId;
                if ($admissionType) $updates['admission_type'] = $admissionType;

                if (!empty($updates)) {
                     $application->update($updates);
                }
                return $application->fresh(['payment']);
            }

            $year = date('Y');
            $count = Application::whereYear('created_at', $year)->count() + 1;
            $appNumber = 'SUPA-' . $year . '-' . str_pad((string) $count, 6, '0', STR_PAD_LEFT);

            $isPublic = false;
            if ($applicant->user) {
                $isGuestSession = false;
                try {
                    if (request()->hasSession() && request()->session()->has('guest_user_id')) {
                        $isGuestSession = request()->session()->get('guest_user_id') == $applicant->user->id;
                    }
                } catch (\Throwable $e) {}

                if ($isGuestSession || \Illuminate\Support\Str::contains($applicant->user->email, '@supa-guest.com')) {
                    $isPublic = true;
                }
            }

            $application = Application::create([
                'applicant_id' => $applicant->id,
                'application_number' => $appNumber,
                'programme_id' => $programmeId,
                'academic_year_id' => $academicYearId,
                'intake_id' => $intakeId,
                'admission_type' => $admissionType ?? 'Form Six',
                'admission_category' => 'Direct Entry',
                'status' => 'Draft',
                'is_public_submission' => $isPublic,
                'current_step' => 1,
                'completion_percentage' => 14,
                'expires_at' => now()->addDays((int) Setting::get('draft_expiration_days', 30)),
                'last_activity_at' => now(),
            ]);

            $this->logActivity($application, 'Application Started', 'Applicant initiated the admission application.');

            // Placeholder payment only — real NMB control number is requested after
            // academic info is saved (outside this DB transaction).
            $amount = 20000;
            if ($application->programme) {
                $amount = $application->programme->application_fee ?? $amount;
            }

            Payment::firstOrCreate(
                ['application_id' => $application->id],
                [
                    'control_number' => 'PENDING-'.$application->id,
                    'amount' => $amount,
                    'currency' => 'TZS',
                    'payment_status' => 'pending',
                    'singida_synced' => false,
                ]
            );

            return $application->fresh(['payment']);
        });
    }

    /**
     * Mirror the application to Singida and store the real NMB control number.
     * Falls back to a local placeholder only when Singida integration is disabled/unconfigured.
     */
    public function ensurePaymentWithSingidaControlNumber(Application $application, bool $force = false): Payment
    {
        $application->loadMissing(['applicant.user', 'programme', 'academicProfile', 'academicYear', 'payment']);

        $amount = (float) ($application->programme->application_fee
            ?? config('services.singida.default_amount', 20000));

        $payment = Payment::firstOrCreate(
            ['application_id' => $application->id],
            [
                'control_number' => 'PENDING-'.$application->id,
                'amount' => $amount,
                'currency' => 'TZS',
                'payment_status' => 'pending',
                'singida_synced' => false,
            ]
        );

        if (! $force && $this->hasRealSingidaControlNumber($payment)) {
            return $payment;
        }

        if (! $this->singidaClient->isConfigured()) {
            // Local/dev fallback when Singida is not configured.
            if (! filled($payment->control_number) || str_starts_with((string) $payment->control_number, 'PENDING-')) {
                $payment->update([
                    'control_number' => '99100'.date('Y').str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
                    'singida_synced' => false,
                ]);
            }

            return $payment->fresh();
        }

        $phone = $application->applicant?->user?->phone;
        if (! filled($phone)) {
            throw new \RuntimeException('Applicant phone number is required before requesting an NMB control number from Singida.');
        }

        try {
            $result = $this->singidaClient->syncApplication($application);

            $payment->update([
                'control_number' => $result['control_number'],
                'amount' => $result['amount'] > 0 ? $result['amount'] : $amount,
                'singida_synced' => true,
            ]);

            $application->update([
                'singida_admission_id' => $result['singida_admission_id'],
                'singida_synced_at' => now(),
            ]);

            AuditLogService::log(
                'singida_control_number_issued',
                "NMB control number {$result['control_number']} issued via Singida for {$application->application_number}"
            );

            return $payment->fresh();
        } catch (\Throwable $e) {
            report($e);
            throw $e instanceof \RuntimeException
                ? $e
                : new \RuntimeException('Failed to get NMB control number from Singida: '.$e->getMessage(), 0, $e);
        }
    }

    public function hasRealSingidaControlNumber(?Payment $payment): bool
    {
        if (! $payment || ! filled($payment->control_number)) {
            return false;
        }

        $cn = (string) $payment->control_number;

        if (str_starts_with($cn, 'PENDING-') || str_starts_with($cn, '99100')) {
            return false;
        }

        return (bool) $payment->singida_synced;
    }

    public function saveAcademicInfo(Application $application, array $academicData): AcademicProfile
    {
        return DB::transaction(function () use ($application, $academicData) {
            $admissionType = $academicData['admission_type'] ?? $application->admission_type;
            $gpa = isset($academicData['gpa']) ? (float) $academicData['gpa'] : null;
            $points = isset($academicData['acsee_points']) ? (float) $academicData['acsee_points'] : null;
            $g1 = $academicData['acsee_grade1'] ?? null;
            $g2 = $academicData['acsee_grade2'] ?? null;
            $g3 = $academicData['acsee_grade3'] ?? null;

            // Recalculate Category
            $category = $this->categoryCalculator->calculate($admissionType, $gpa, $points, $g1, $g2, $g3);

            $application->update([
                'admission_type' => $admissionType,
                'admission_category' => $category,
            ]);

            $profile = AcademicProfile::updateOrCreate(
                ['application_id' => $application->id],
                array_merge($academicData, ['admission_type' => $admissionType])
            );

            $application->update([
                'current_step' => max($application->current_step, 4),
                'completion_percentage' => max($application->completion_percentage, 42),
                'last_activity_at' => now(),
            ]);
            $this->logActivity($application, 'Step 3 Completed', 'Applicant completed Academic Profile.');

            AuditLogService::log('academic_info_saved', "Saved academic profile for application {$application->application_number}");

            return $profile;
        });
    }

    public function handleFileUpload(Application $application, string $documentType, $file): ApplicationDocument
    {
        $path = $file->store('documents/' . $application->application_number, 'public');

        $doc = ApplicationDocument::updateOrCreate(
            [
                'application_id' => $application->id,
                'document_type' => $documentType,
            ],
            [
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size_bytes' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'verification_status' => 'pending',
                'rejection_comment' => null,
            ]
        );

        $application->update([
            'current_step' => max($application->current_step, 7),
            'completion_percentage' => max($application->completion_percentage, 85),
            'last_activity_at' => now(),
        ]);
        $this->logActivity($application, 'Documents Uploaded', "Uploaded document: {$documentType}");

        return $doc;
    }

    public function submitApplication(Application $application, string $signatureData, ?array $consentData = null): Application
    {
        return DB::transaction(function () use ($application, $signatureData, $consentData) {
            // Save digital signature
            $signaturePath = 'signatures/sig_' . $application->id . '_' . time() . '.png';
            if (Str::startsWith($signatureData, 'data:image')) {
                $image = explode(',', $signatureData)[1];
                Storage::disk('public')->put($signaturePath, base64_decode($image));
            } else {
                $signaturePath = $signatureData;
            }

            $application->update([
                'status' => 'SUBMITTED',
                'digital_signature_path' => $signaturePath,
                'submitted_at' => now(),
                'current_step' => 7,
                'completion_percentage' => 100,
                'last_activity_at' => now(),
            ]);

            $this->logActivity($application, 'Application Submitted', 'Applicant submitted the final application review & signature.');

            // Save consent if provided
            if ($consentData) {
                $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
                $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

                $userAgent = $consentData['user_agent'] ?? null;
                $userAgentData = self::parseUserAgent($userAgent);

                $hashData = [
                    'application_id' => $application->id,
                    'user_id' => $application->applicant->user_id,
                    'ip_address' => $consentData['ip_address'] ?? null,
                    'user_agent' => $userAgent,
                    'consented_at' => now()->toDateTimeString(),
                    'privacy_policy_version' => $activePolicy ? $activePolicy->version : '2.1',
                    'terms_version' => $activeTerms ? $activeTerms->version : '2.1',
                    'parent_consent_given' => $consentData['parent_consent_given'] ?? false,
                    'parent_name' => $consentData['parent_name'] ?? null,
                ];
                $consentHash = hash('sha256', json_encode($hashData));

                \App\Models\ApplicationConsent::create([
                    'application_id' => $application->id,
                    'user_id' => $application->applicant->user_id,
                    'privacy_policy_id' => $activePolicy ? $activePolicy->id : null,
                    'terms_conditions_id' => $activeTerms ? $activeTerms->id : null,
                    'consent_version' => $activePolicy ? $activePolicy->version : '2.1',
                    'consent_language' => $consentData['consent_language'] ?? 'en',
                    'consent_source' => $consentData['consent_source'] ?? 'Web',
                    'device_type' => $userAgentData['device_type'],
                    'browser_name' => $userAgentData['browser_name'],
                    'operating_system' => $userAgentData['operating_system'],
                    'application_status_at_consent' => $application->status,
                    'consent_given' => true,
                    'parent_consent_given' => $consentData['parent_consent_given'] ?? false,
                    'parent_name' => $consentData['parent_name'] ?? null,
                    'parent_signature' => $consentData['parent_signature'] ?? null,
                    'parent_consented_at' => ($consentData['parent_consent_given'] ?? false) ? now() : null,
                    'ip_address' => $consentData['ip_address'] ?? null,
                    'user_agent' => $userAgent,
                    'consented_at' => now(),
                    'consent_hash' => $consentHash,
                    'created_by' => $application->applicant->user_id,
                    'updated_by' => $application->applicant->user_id,
                ]);

                AuditLogService::log(
                    'applicant_consent_accepted', 
                    "Applicant accepted Privacy Policy Version " . ($activePolicy ? $activePolicy->version : '2.1') . " and Terms Version " . ($activeTerms ? $activeTerms->version : '2.1') . " before submitting application."
                );
            }

            AuditLogService::log('application_submitted', "Application {$application->application_number} submitted by applicant");

            return $application;
        });
    }

    public static function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'Unknown',
                'browser_name' => 'Unknown',
                'operating_system' => 'Unknown',
            ];
        }

        $os = 'Unknown';
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        $browser = 'Unknown';
        if (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/opr|opera|edge|edg/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/edge|edg/i', $userAgent)) {
            $browser = 'Edge';
        }

        $device = 'Desktop';
        if (preg_match('/mobile|phone|ipod/i', $userAgent)) {
            $device = 'Mobile';
        } elseif (preg_match('/ipad|tablet/i', $userAgent)) {
            $device = 'Tablet';
        }

        return [
            'device_type' => $device,
            'browser_name' => $browser,
            'operating_system' => $os,
        ];
    }
}
