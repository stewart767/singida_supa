<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgrammeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'department' => $this->department,
            'faculty' => $this->faculty,
            'description' => $this->description,
            'entry_requirements' => $this->entry_requirements,
            'duration_years' => $this->duration_years,
            'annual_fee' => (float) $this->annual_fee,
            'monthly_fee' => (float) $this->monthly_fee,
            'application_fee' => (float) $this->application_fee,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
