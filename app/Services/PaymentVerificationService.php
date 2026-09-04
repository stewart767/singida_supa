<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
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
        throw new \DomainException('Manual payment verification is disabled. Payments must be verified automatically via the banking gateway.');
    }
}
