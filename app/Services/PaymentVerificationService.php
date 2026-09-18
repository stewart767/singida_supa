<?php

namespace App\Services;

use App\Models\ApplicationActivity;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class PaymentVerificationService
{
    public function uploadReceipt(Payment $payment, $receiptFile): Payment
    {
        $path = $receiptFile->store('receipts', 'public');
        $payment->update([
            'receipt_path' => $path,
            'payment_status' => 'pending',
        ]);

        return $payment;
    }

    public function verifyPayment(Payment $payment, User $staff, string $status, ?string $rejectionReason = null): Payment
    {
        if (!$staff->isSuperAdmin()) {
            throw new AuthorizationException('Only Superadmin has authorization to approve or verify payments.');
        }

        $normalizedStatus = strtolower($status);
        if (!in_array($normalizedStatus, ['paid', 'verified', 'rejected', 'pending', 'cancelled'], true)) {
            throw new \InvalidArgumentException("Invalid payment status: {$status}");
        }

        return DB::transaction(function () use ($payment, $staff, $normalizedStatus, $rejectionReason) {
            $isPaid = in_array($normalizedStatus, ['paid', 'verified'], true);
            $effectiveStatus = $isPaid ? 'paid' : $normalizedStatus;

            $payment->update([
                'payment_status' => $effectiveStatus,
                'verified_by' => $staff->id,
                'verified_at' => now(),
                'rejection_reason' => $effectiveStatus === 'rejected' ? $rejectionReason : null,
            ]);

            $application = $payment->application;
            if ($application && $isPaid) {
                $updates = [];
                if (in_array($application->status, ['Draft', 'Pending Payment', 'PAYMENT_PENDING'], true)) {
                    $updates['status'] = 'IN_PROGRESS';
                    $updates['current_step'] = max($application->current_step, 6);
                    $updates['completion_percentage'] = max($application->completion_percentage, 71);
                    $updates['last_activity_at'] = now();
                }

                if (!empty($updates)) {
                    $application->update($updates);
                }

                ApplicationActivity::create([
                    'application_id' => $application->id,
                    'action' => 'Payment Approved',
                    'description' => "Payment of TZS " . number_format($payment->amount) . " (Control #: {$payment->control_number}) was approved and verified by Superadmin {$staff->name}.",
                ]);
            } elseif ($application && $effectiveStatus === 'rejected') {
                ApplicationActivity::create([
                    'application_id' => $application->id,
                    'action' => 'Payment Rejected',
                    'description' => "Payment (Control #: {$payment->control_number}) was marked as rejected by Superadmin {$staff->name}." . ($rejectionReason ? " Reason: {$rejectionReason}" : ""),
                ]);
            }

            AuditLog::create([
                'user_id' => $staff->id,
                'action' => 'Payment Approved / Verified',
                'description' => "Superadmin {$staff->name} changed payment status for Control #: {$payment->control_number} to '{$effectiveStatus}'",
                'ip_address' => request()?->ip() ?? '127.0.0.1',
            ]);

            return $payment->fresh(['application.applicant.user', 'application.programme', 'verifier']);
        });
    }

    /**
     * Retrieve all duplicate control numbers with detailed breakdown of affected applications.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDuplicateControlNumbersSummary(): \Illuminate\Support\Collection
    {
        $duplicateControlNumbers = DB::table('payments')
            ->select('control_number', DB::raw('COUNT(*) as total_count'))
            ->whereNotNull('control_number')
            ->where('control_number', '!=', '')
            ->where('control_number', 'not like', 'PENDING-%')
            ->groupBy('control_number')
            ->having('total_count', '>', 1)
            ->pluck('control_number');

        if ($duplicateControlNumbers->isEmpty()) {
            return collect([]);
        }

        $payments = Payment::with([
            'application.applicant.user',
            'application.programme',
            'application.academicYear',
            'verifier'
        ])
        ->whereIn('control_number', $duplicateControlNumbers)
        ->orderBy('control_number')
        ->orderBy('created_at')
        ->get();

        return $payments->groupBy('control_number')->map(function ($group, $controlNumber) {
            $paidPayments = $group->filter(fn($p) => strtolower((string) $p->payment_status) === 'paid');
            $unpaidPayments = $group->filter(fn($p) => strtolower((string) $p->payment_status) !== 'paid');

            $paidCount = $paidPayments->count();
            $unpaidCount = $unpaidPayments->count();

            $statusDescription = 'Multiple Unpaid';
            if ($paidCount === 1 && $unpaidCount >= 1) {
                $statusDescription = '1 Paid (Retain), ' . $unpaidCount . ' Unpaid (Needs Regeneration)';
            } elseif ($paidCount > 1) {
                $statusDescription = 'Conflict: Multiple Paid Records (' . $paidCount . ' paid)';
            } elseif ($paidCount === 0) {
                $statusDescription = 'All Unpaid (' . $unpaidCount . ' records share this number)';
            }

            return [
                'control_number' => $controlNumber,
                'total_count' => $group->count(),
                'paid_count' => $paidCount,
                'unpaid_count' => $unpaidCount,
                'status_description' => $statusDescription,
                'has_paid' => $paidCount > 0,
                'payments' => $group,
            ];
        })->values();
    }

    /**
     * Regenerate or assign a fresh, non-colliding control number for an applicant/application.
     */
    public function regenerateControlNumber(
        Payment $payment,
        User $staff,
        ?string $customControlNumber = null,
        ?string $reason = null,
        bool $force = false
    ): Payment {
        if (!$staff->isSuperAdmin()) {
            throw new AuthorizationException('Only Superadmin has authorization to refresh or regenerate control numbers.');
        }

        $payment->loadMissing(['application.applicant.user', 'application.programme']);

        // Protect paid records unless explicitly forced
        if (strtolower((string) $payment->payment_status) === 'paid' && !$force) {
            throw new \InvalidArgumentException('Cannot regenerate control number for an applicant whose payment is already marked as PAID. Please verify or reject payment first.');
        }

        $oldControlNumber = $payment->control_number;

        // Determine new unique control number
        $newControlNumber = null;
        if (!empty(trim((string) $customControlNumber))) {
            $cleanCustom = trim((string) $customControlNumber);
            // Check uniqueness if custom provided
            $exists = Payment::where('control_number', $cleanCustom)->where('id', '!=', $payment->id)->exists();
            if ($exists) {
                throw new \InvalidArgumentException("The specified control number {$cleanCustom} is already in use by another application.");
            }
            $newControlNumber = $cleanCustom;
        } else {
            // Attempt Singida gateway sync if configured, or fallback to collision-free local generator
            $singidaClient = app(SingidaAdmissionClient::class);
            if ($singidaClient->isConfigured() && $payment->application) {
                try {
                    $result = $singidaClient->syncApplication($payment->application);
                    $candidate = $result['control_number'] ?? null;
                    if ($candidate && !Payment::where('control_number', $candidate)->where('id', '!=', $payment->id)->exists()) {
                        $newControlNumber = $candidate;
                    }
                } catch (\Throwable $e) {
                    // Fallback to local collision-free generation
                }
            }

            if (empty($newControlNumber)) {
                $newControlNumber = ApplicationWorkflowService::generateUniqueControlNumber();
            }
        }

        return DB::transaction(function () use ($payment, $staff, $oldControlNumber, $newControlNumber, $reason) {
            $payment->update([
                'control_number' => $newControlNumber,
                'payment_status' => 'pending',
                'receipt_path' => null,
                'transaction_reference' => null,
                'verified_by' => null,
                'verified_at' => null,
                'rejection_reason' => null,
                'singida_synced' => false,
            ]);

            $application = $payment->application;
            $applicantName = $application?->applicant?->user?->name ?? 'Applicant';
            $appNumber = $application?->application_number ?? "ID: {$payment->application_id}";

            $reasonText = $reason ? " Reason: {$reason}" : " (Duplicate control number resolution)";

            if ($application) {
                ApplicationActivity::create([
                    'application_id' => $application->id,
                    'action' => 'Control Number Regenerated',
                    'description' => "Control number regenerated from '{$oldControlNumber}' to '{$newControlNumber}' by Superadmin {$staff->name}.{$reasonText}",
                ]);
            }

            AuditLog::create([
                'user_id' => $staff->id,
                'action' => 'Control Number Regenerated',
                'description' => "Superadmin {$staff->name} regenerated control number for {$applicantName} ({$appNumber}) from '{$oldControlNumber}' to '{$newControlNumber}'.{$reasonText}",
                'ip_address' => request()?->ip() ?? '127.0.0.1',
            ]);

            return $payment->fresh(['application.applicant.user', 'application.programme', 'verifier']);
        });
    }
}
