<?php

namespace App\Http\Requests;

use App\Enums\ProductAvailability;
use App\Models\CartItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $cartItem = $this->route('cartItem');

            if (! $cartItem instanceof CartItem) {
                return;
            }

            $product = $cartItem->product;
            $availability = $product->getAttribute('availability');
            $quantity = (int) $this->input('quantity', 1);

            if ($availability === ProductAvailability::InStock && $quantity > $product->stock) {
                $validator->errors()->add('quantity', 'No hay suficientes ejemplares disponibles.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'quantity' => 'cantidad',
        ];
    }
}
