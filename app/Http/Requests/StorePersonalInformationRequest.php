<?php

namespace App\Http\Requests;

use App\Rules\NidaNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonalInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'nida_number' => [
                'nullable',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('applicants', 'nida_number')->ignore($this->user()?->applicant?->id)
            ],
            'voter_id_number' => ['nullable', 'string', 'max:50'],
            'nida_card_number' => ['nullable', 'string', 'max:50'],
            'work_id_number' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'region' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'ward' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'next_of_kin_name' => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone' => ['nullable', 'string', 'max:20'],
            'next_of_kin_relation' => ['nullable', 'string', 'max:100'],
            'passport_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $nida = trim((string) ($this->input('nida_number') ?: $this->input('nida_card_number')));
            $voter = trim((string) $this->input('voter_id_number'));
            $work = trim((string) $this->input('work_id_number'));

            if (empty($nida) && empty($voter) && empty($work)) {
                $validator->errors()->add(
                    'nida_number',
                    'Tafadhali jaza angalau kitambulisho kimoja kati ya: Kitambulisho cha NIDA, Kitambulisho cha Kura, au Kitambulisho cha Kazi.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'next_of_kin_name' => 'parent / guardian name',
            'next_of_kin_phone' => 'parent / guardian phone',
            'next_of_kin_relation' => 'relationship',
        ];
    }

    public function messages(): array
    {
        return [
            'nida_number.unique' => 'Nambari hii ya NIDA imekwisha tumika na mwombaji mwingine / This NIDA number is already registered.',
        ];
    }
}
