<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends CreateProductRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'is_top' => ['boolean']
        ]);
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->route('productId')
        ]);
    }
}
