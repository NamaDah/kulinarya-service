<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'code')->ignore($this->route('product')),
            ],
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|url',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'boolean',
            'stock' => 'nullable|integer|min:0',
        ];
    }
}
