<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCareerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'current_profession' => 'required|string|max:255',
            'years_experience' => 'required|integer|min:0',
            'skills' => 'required|array',
            'skills.*' => 'required|string|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'cv_file' => ($isUpdate ? 'nullable' : 'required') . '|file|mimes:pdf,doc,docx|max:5120',
            'preferred_job_categories' => 'nullable|array',
            'preferred_job_categories.*' => 'nullable|string|max:100',
            'preferred_locations' => 'required|array',
            'preferred_locations.*' => 'required|string|max:100',
            'expected_salary' => 'required|numeric|min:0',
            'availability_date' => 'required|date',
        ];
    }
}
