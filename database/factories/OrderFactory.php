<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 2000);
        $shippingTotal = fake()->randomFloat(2, 0, 150);

        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Confirmed,
            'subtotal' => $subtotal,
            'shipping_total' => $shippingTotal,
            'total' => $subtotal + $shippingTotal,
            'deposit_amount' => 0,
            'balance_amount' => 0,
            'shipping_address' => [
                'recipient_name' => fake()->name(),
                'street' => fake()->streetAddress(),
                'city' => 'Orizaba',
                'state' => 'Veracruz',
                'postal_code' => fake()->numerify('94###'),
                'country' => 'MX',
            ],
            'placed_at' => now(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Cancelled,
        ]);
    }
}
