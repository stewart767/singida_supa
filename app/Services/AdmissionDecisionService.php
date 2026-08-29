<?php

namespace App\Services;

use App\Models\AdmissionLetter;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmissionDecisionService
{
    public function makeDecision(Application $application, User $staff, string $decision, ?string $reason = null): Application
    {
        return DB::transaction(function () use ($application, $staff, $decision, $reason) {
            $statusMap = [
                'approve' => 'Approved',
                'reject' => 'Rejected',
                'waitlist' => 'Waitlist',
                'recommend_foundation' => 'Under Review',
            ];

            $newStatus = $statusMap[$decision] ?? 'Under Review';

            if ($decision === 'recommend_foundation') {
                $application->update([
                    'admission_category' => 'Foundation',
                    'rejection_reason' => 'Recommended for Foundation Programme',
                ]);
            } else {
                $application->update([
                    'status' => $newStatus,
                    'rejection_reason' => $reason,
                    'reviewed_by' => $staff->id,
                    'reviewed_at' => now(),
                ]);
            }

            if ($newStatus === 'Approved' && !$application->admissionLetter) {
                $this->generateAdmissionLetter($application, $staff);
            }

            AuditLogService::log('admission_decision', "Application {$application->application_number} decided as {$newStatus} by staff ID {$staff->id}");

            return $application;
        });
    }

    public function bulkApprove(array $applicationIds, User $staff): int
    {
        $approvedCount = 0;
        foreach ($applicationIds as $id) {
            $app = Application::find($id);
            if ($app && in_array($app->status, ['Under Review', 'Verified', 'Waitlist'])) {
                $this->makeDecision($app, $staff, 'approve');
                $approvedCount++;
            }
        }
        return $approvedCount;
    }

    protected function generateAdmissionLetter(Application $application, User $staff): AdmissionLetter
    {
        $seqNumber = str_pad((string) (AdmissionLetter::count() + 1), 4, '0', STR_PAD_LEFT);
        $admNumber = 'SUPA/ADM/' . date('Y') . '/' . $seqNumber;
        $verificationCode = strtoupper(Str::random(16));

        return AdmissionLetter::create([
            'application_id' => $application->id,
            'admission_number' => $admNumber,
            'verification_code' => $verificationCode,
            'qr_code_hash' => hash('sha256', $admNumber . '|' . $verificationCode),
            'reporting_date' => now()->addMonths(1),
            'generated_by' => $staff->id,
            'generated_at' => now(),
        ]);
    }
}
