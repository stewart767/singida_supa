<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CseeNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $strValue = (string) $value;

        // For local development or testing, allow simple numeric strings (e.g. 7777888)
        if (app()->environment('local', 'testing')) {
            if (preg_match('/^\d+$/', $strValue)) {
                return;
            }
        }

        // Support formats like S0101/0001/2022, S0101.0001.2022, S0101-0001-2022, or S010100012022
        if (!preg_match('/^[SP]\d{4}[\/\.-]?\d{4}[\/\.-]?\d{4}$/i', $strValue)) {
            $fail('The :attribute must follow standard NECTA index format (e.g., S0101/0001/2022).');
        }
    }
}
