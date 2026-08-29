<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_positions') || $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'employment_type' => 'required|string|max:100',
            'reports_to_position_id' => 'nullable|exists:positions,id',
            'salary_grade' => 'nullable|string|max:100',
            'status' => 'required|in:active,archived',
        ];
    }
}
