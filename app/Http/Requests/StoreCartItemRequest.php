<?php

namespace App\Http\Requests;

use App\Enums\ProductAvailability;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
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
            $product = $this->route('product');

            if (! $product instanceof Product) {
                return;
            }

            $availability = $product->getAttribute('availability');
            $quantity = (int) $this->input('quantity', 1);

            if ($product->isPreorder()) {
                $validator->errors()->add('product', 'Este producto es de preventa; debes apartarlo con un depósito.');

                return;
            }

            if (! $product->is_active || $availability === ProductAvailability::OutOfStock) {
                $validator->errors()->add('product', 'Este producto no está disponible en este momento.');

                return;
            }

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
