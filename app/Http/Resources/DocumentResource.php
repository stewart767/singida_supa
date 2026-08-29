<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_type' => $this->document_type,
            'original_filename' => $this->original_filename,
            'file_url' => asset('storage/' . $this->file_path),
            'file_size_bytes' => $this->file_size_bytes,
            'mime_type' => $this->mime_type,
            'verification_status' => $this->verification_status,
            'rejection_comment' => $this->rejection_comment,
            'verified_at' => $this->verified_at?->toIso8601String(),
        ];
    }
}
