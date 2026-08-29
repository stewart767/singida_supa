<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionLetterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_number' => $this->admission_number,
            'verification_code' => $this->verification_code,
            'qr_code_hash' => $this->qr_code_hash,
            'reporting_date' => $this->reporting_date?->toDateString(),
            'generated_at' => $this->generated_at?->toIso8601String(),
            'pdf_url' => route('api.admission-letter.download', ['verificationCode' => $this->verification_code]),
        ];
    }
}
