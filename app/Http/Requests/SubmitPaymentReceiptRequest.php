<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_id' => ['required', 'exists:payments,id'],
            'receipt' => ['required', 'file', 'mimes:pdf,jpg,png,jpeg', 'max:5120'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}
