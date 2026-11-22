<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reservation> */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'order_id' => null,
            'quantity' => 1,
            'status' => ReservationStatus::Deposited,
            'deposit_amount' => 20,
            'balance_amount' => 80,
            'releases_at' => now()->addDays(14),
            'shipping_address' => [
                'recipient_name' => fake()->name(),
                'street' => fake()->streetAddress(),
                'city' => 'Orizaba',
                'state' => 'Veracruz',
                'postal_code' => fake()->numerify('94###'),
                'country' => 'MX',
            ],
            'charged_at' => null,
            'refunded_at' => null,
        ];
    }

    public function due(): static
    {
        return $this->state(fn (array $attributes) => [
            'releases_at' => now()->subDay(),
        ]);
    }

    public function charged(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Charged,
            'charged_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Cancelled,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Refunded,
            'refunded_at' => now(),
        ]);
    }
}
