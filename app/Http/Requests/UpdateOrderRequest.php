<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'status' => ['sometimes', 'required', Rule::in(['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled'])],
            'payment_status' => ['sometimes', 'required', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
