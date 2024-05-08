<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUpdateRequest extends FormRequest
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
            'client_name' => 'required|max:255|min:1|string',
            'description' => 'required|max:600|min:1|string',
            'products' => 'required|array|min:1',
            'perPage' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'products.*.product_name' => 'nullable|max:255|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.product_price' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'active' => 'nullable',
            'product_id.*' => 'required|exists:carts,id',
        ];
    }
}
