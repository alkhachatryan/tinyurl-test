<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
