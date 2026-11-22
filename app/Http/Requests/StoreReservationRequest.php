<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'address_id' => [
                'required',
                'integer',
                Rule::exists('addresses', 'id')->where('user_id', $this->user()?->id),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $product = $this->route('product');

            if (! $product instanceof Product) {
                return;
            }

            if (! $product->isPreorder()) {
                $validator->errors()->add('product', 'Este producto no está disponible para apartar.');

                return;
            }

            $quantity = (int) $this->input('quantity', 1);
            $deposit = round((float) $this->input('deposit_amount', 0), 2);
            $minimum = $product->minimumDepositFor($quantity);
            $maximum = round((float) $product->price * $quantity, 2);

            if ($deposit < $minimum) {
                $validator->errors()->add('deposit_amount', "El depósito mínimo es {$minimum}.");

                return;
            }

            if ($deposit > $maximum) {
                $validator->errors()->add('deposit_amount', "El depósito no puede superar {$maximum}.");
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
            'deposit_amount' => 'depósito',
            'address_id' => 'dirección de envío',
        ];
    }
}
