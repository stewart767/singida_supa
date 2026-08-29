<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'control_number' => $this->control_number,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'payment_status' => $this->payment_status,
            'receipt_url' => $this->receipt_path ? asset('storage/' . $this->receipt_path) : null,
            'transaction_reference' => $this->transaction_reference,
            'payment_method' => $this->payment_method,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'rejection_reason' => $this->rejection_reason,
            'singida_synced' => (bool) $this->singida_synced,
        ];
    }
}
