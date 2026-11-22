<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Address> */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => AddressType::Shipping,
            'label' => null,
            'recipient_name' => fake()->name(),
            'street' => fake()->streetName(),
            'exterior_number' => fake()->buildingNumber(),
            'interior_number' => null,
            'neighborhood' => fake()->citySuffix(),
            'city' => 'Orizaba',
            'state' => 'Veracruz',
            'postal_code' => fake()->numerify('94###'),
            'country' => 'MX',
            'phone' => fake()->numerify('### ### ####'),
            'is_default' => false,
        ];
    }

    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AddressType::Billing,
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
