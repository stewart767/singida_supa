<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NidaNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Tanzanian NIDA number format: 20 digits (e.g. 19900101-12345-00001-12)
        $digitsOnly = preg_replace('/[^0-9]/', '', (string) $value);
        if (strlen($digitsOnly) !== 20) {
            $fail('The :attribute must be a valid 20-digit NIDA National Identification Number.');
        }
    }
}
