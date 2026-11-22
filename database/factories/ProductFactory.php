<?php

namespace Database\Factories;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'category_id' => Category::factory(),
            'title' => Str::of($title)->replaceEnd('.', '')->toString(),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'isbn' => fake()->unique()->numerify('978##########'),
            'format' => ProductFormat::Printed,
            'language' => 'es',
            'pages' => fake()->numberBetween(80, 600),
            'edition_year' => fake()->numberBetween(2018, 2026),
            'synopsis' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 80, 1200),
            'stock' => fake()->numberBetween(1, 25),
            'availability' => ProductAvailability::InStock,
            'deposit_amount' => null,
            'releases_at' => null,
            'cover_path' => null,
            'is_active' => true,
        ];
    }

    public function ebook(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => ProductFormat::Ebook,
            'pages' => null,
        ]);
    }

    public function audiobook(): static
    {
        return $this->state(fn (array $attributes) => [
            'format' => ProductFormat::Audiobook,
            'pages' => null,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'availability' => ProductAvailability::OutOfStock,
        ]);
    }

    public function onDemand(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability' => ProductAvailability::OnDemand,
        ]);
    }

    public function preorder(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability' => ProductAvailability::OnDemand,
            'deposit_amount' => 20,
            'releases_at' => now()->addDays(14),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
