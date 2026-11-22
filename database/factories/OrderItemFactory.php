<?php

namespace Database\Factories;

use App\Enums\ProductFormat;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItem> */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = fake()->randomFloat(2, 80, 1200);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'format' => ProductFormat::Printed,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $quantity * $unitPrice,
        ];
    }
}
