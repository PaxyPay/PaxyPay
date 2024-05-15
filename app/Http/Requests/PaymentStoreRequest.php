<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
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
            'client_name' => 'required|max:255|string',
            'description' => 'required|max:600|string',          
            'products' => 'required|array|min:1',
            'perPage' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'products.*.product_name' => 'nullable|max:255|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.product_price' => 'required|numeric',
            'due_date' => 'nullable|date',
            'active' => 'nullable',
            'total_price' => 'min:1|numeric'
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalPrice = 0;

            foreach ($this->input('products') as $product) {
                $totalPrice += $product['quantity'] * $product['product_price'];
            }

            if ($totalPrice < 0) {
                $validator->errors()->add('total_price', __('messages.prezzo_totale_negativo'));
            }
        });
    }
}
