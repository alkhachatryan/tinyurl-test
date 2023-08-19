<?php

namespace App\Http\Requests\Auth;

use App\Rules\IsDecryptable;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', new IsDecryptable()],
            'password' => ['required', 'min:8', 'confirmed']
        ];
    }
}
