<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Crypt;

class IsDecryptable implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            Crypt::decrypt($value);
        } catch (DecryptException $e) {
            $fail('Invalid encryption');
        }
    }
}
