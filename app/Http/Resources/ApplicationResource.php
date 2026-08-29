<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_number' => $this->application_number,
            'applicant' => new ApplicantResource($this->whenLoaded('applicant')),
            'programme' => new ProgrammeResource($this->whenLoaded('programme')),
            'academic_year' => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'code' => $this->academicYear->code,
                'name' => $this->academicYear->name,
            ] : null,
            'intake' => $this->intake ? [
                'id' => $this->intake->id,
                'name' => $this->intake->name,
            ] : null,
            'admission_type' => $this->admission_type,
            'admission_category' => $this->admission_category,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'digital_signature_url' => $this->digital_signature_path ? asset('storage/' . $this->digital_signature_path) : null,
            'academic_profile' => $this->whenLoaded('academicProfile'),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
            'payment' => new PaymentResource($this->whenLoaded('payment')),
            'admission_letter' => new AdmissionLetterResource($this->whenLoaded('admissionLetter')),
            'singida_admission_id' => $this->singida_admission_id,
            'singida_synced_at' => $this->singida_synced_at?->toIso8601String(),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'current_step' => $this->current_step,
            'completion_percentage' => $this->completion_percentage,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
