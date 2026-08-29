<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'document_type' => [
                'required', 
                'in:passport,csee_certificate,acsee_certificate,diploma_certificate,transcript,nida_id,payment_receipt'
            ],
            'document' => [
                'required', 
                'file', 
                'mimes:pdf,png,jpg,jpeg', 
                'max:5120' // 5MB limit
            ],
        ];
    }
}
