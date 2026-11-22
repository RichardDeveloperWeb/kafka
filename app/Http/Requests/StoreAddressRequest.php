<?php

namespace App\Http\Requests;

use App\Enums\AddressType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(AddressType::class)],
            'label' => ['nullable', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:160'],
            'exterior_number' => ['required', 'string', 'max:30'],
            'interior_number' => ['nullable', 'string', 'max:30'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country' => ['required', 'string', 'size:2'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipo',
            'label' => 'etiqueta',
            'recipient_name' => 'nombre de quien recibe',
            'street' => 'calle',
            'exterior_number' => 'número exterior',
            'interior_number' => 'número interior',
            'neighborhood' => 'colonia',
            'city' => 'ciudad',
            'state' => 'estado',
            'postal_code' => 'código postal',
            'country' => 'país',
            'phone' => 'teléfono',
            'is_default' => 'dirección predeterminada',
        ];
    }
}
