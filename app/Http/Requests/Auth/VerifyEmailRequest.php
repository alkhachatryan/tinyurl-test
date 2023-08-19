<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsDecryptable;
use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', new IsDecryptable()]
        ];
    }
}
