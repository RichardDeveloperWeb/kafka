<?php

namespace Database\Factories;

use App\Enums\PaymentTransactionType;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PaymentTransaction> */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'order_id' => null,
            'type' => PaymentTransactionType::Deposit,
            'amount' => fake()->randomFloat(2, 20, 500),
            'processed_at' => now(),
        ];
    }

    public function forOrder(Order $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $order->id,
        ]);
    }
}
