<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'int', 'exists:products,id,is_deleted,1']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->route('productId')
        ]);
    }
}
