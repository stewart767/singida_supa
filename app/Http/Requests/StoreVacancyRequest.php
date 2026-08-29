<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('manage_vacancies') || $this->user()->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'application_type' => $this->input('application_type') ?: 'internal',
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('vacancy') ? $this->route('vacancy')->id : null;
        $deadlineRule = 'required|date';
        if (!$id) {
            $deadlineRule .= '|after_or_equal:today';
        }

        return [
            'job_title' => 'required|string|max:255',
            'department_name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'position_id' => 'required|exists:positions,id',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'number_of_positions' => 'required|integer|min:1',
            'employment_type' => 'required|string|max:100',
            'contract_type' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'recommended_region' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'application_deadline' => $deadlineRule,
            'closing_date' => 'nullable|date|after_or_equal:application_deadline',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'required_experience' => 'required|string',
            'required_skills' => 'required|string',
            'benefits' => 'nullable|string',
            'featured_image_file' => 'nullable|image|max:2048',
            'status' => 'required|in:Draft,Published,Closed,Archived',
            'requirements' => 'nullable|array',
            'application_type' => 'nullable|in:internal,external',
            'external_url' => 'required_if:application_type,external|nullable|url|max:2048',
            'external_provider' => 'nullable|string|max:255',
        ];
    }
}
