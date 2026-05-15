<?php

namespace App\Services;

class EfirmaValidator
{
    public function validateSignedToken(string $signedToken, string $rfc): bool
    {
        return $signedToken !== '' && preg_match('/^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/u', $rfc) === 1;
    }
}
