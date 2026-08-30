<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgrammeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && $this->user()->hasPermissionTo('manage_programmes');
    }

    public function rules(): array
    {
        $id = $this->route('programme')?->id ?? null;

        return [
            'code' => ['required', 'string', 'max:20', 'unique:programmes,code,' . $id],
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'entry_requirements' => ['nullable', 'string'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:6'],
            'annual_fee' => ['required', 'numeric', 'min:0'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'application_fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
