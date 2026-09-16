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
}
