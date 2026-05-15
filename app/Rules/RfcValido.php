<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RfcValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rfc = mb_strtoupper((string) $value, 'UTF-8');

        if (
            preg_match('/^[A-Z&Ñ]{4}\d{6}[A-Z0-9]{3}$/u', $rfc) !== 1
            && preg_match('/^[A-Z&Ñ]{3}\d{6}[A-Z0-9]{3}$/u', $rfc) !== 1
        ) {
            $fail('Ingresa un RFC válido con homoclave.');
            return;
        }

        $datePart = strlen($rfc) === 13 ? substr($rfc, 4, 6) : substr($rfc, 3, 6);

        if (! $this->hasValidDate($datePart)) {
            $fail('El RFC contiene una fecha inválida.');
        }
    }

    private function hasValidDate(string $datePart): bool
    {
        $year = (int) substr($datePart, 0, 2);
        $month = (int) substr($datePart, 2, 2);
        $day = (int) substr($datePart, 4, 2);
        $fullYear = $year <= 30 ? 2000 + $year : 1900 + $year;

        return checkdate($month, $day, $fullYear);
    }
}
