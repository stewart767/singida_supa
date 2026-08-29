<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Everyone can submit a job application (public career portal)
    }

    public function rules(): array
    {
        return [
            'vacancy_id' => 'required|exists:vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            
            // Personal Info
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'nida_number' => 'nullable|string|max:30',
            'region' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'nullable|string|max:100',

            // Expected Salary & Availability
            'expected_salary' => 'required|string|max:100',
            'availability' => 'required|string|max:100',

            // Arrays (JSON fields)
            'education' => 'required|array|min:1',
            'education.*.institution' => 'required|string|max:255',
            'education.*.level' => 'required|string|max:100',
            'education.*.field' => 'required|string|max:255',
            'education.*.start_year' => 'required|integer',
            'education.*.end_year' => 'nullable|integer',

            'experience' => 'nullable|array',
            'experience.*.company' => 'required|string|max:255',
            'experience.*.position' => 'required|string|max:255',
            'experience.*.start_date' => 'required|date',
            'experience.*.end_date' => 'nullable|date',
            'experience.*.description' => 'nullable|string',

            'skills' => 'required|array',
            'skills.*' => 'required|string|max:100',

            'references' => 'nullable|array',
            'references.*.name' => 'required|string|max:255',
            'references.*.phone' => 'required|string|max:30',
            'references.*.email' => 'required|email|max:255',
            'references.*.organization' => 'required|string|max:255',

            // File Uploads
            'cv_file' => 'required|file|mimes:pdf,docx,doc|max:5120',
            'cover_letter_file' => 'nullable|file|mimes:pdf,docx,doc|max:5120',
            'national_id_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
            'passport_photo_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'certificates_file' => 'nullable|array',
            'certificates_file.*' => 'file|mimes:pdf,png,jpg,jpeg|max:5120',
            'transcripts_file' => 'nullable|array',
            'transcripts_file.*' => 'file|mimes:pdf,png,jpg,jpeg|max:5120',
        ];
    }
}
