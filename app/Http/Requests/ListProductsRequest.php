<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListProductsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sort_by' => ['required_with:sort_order', 'in:name,description,price'],
            'sort_order' => ['required_with:sort_by', 'in:asc,desc'],
            'limit' => ['integer', 'min:0']
        ];
    }
}
