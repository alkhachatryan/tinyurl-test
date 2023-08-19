<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReadProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'int', 'exists:products,id,is_deleted,false']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->route('productId')
        ]);
    }

    /**
     * Customizing the failed validation error to show 404 instead of 422.
    */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => 'Not found',
            'data' => $validator->errors(),
        ], 404));
    }
}
