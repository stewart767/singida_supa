<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeAdmissionDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && $this->user()->hasPermissionTo('make_admission_decisions');
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approve,reject,waitlist,recommend_foundation'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
