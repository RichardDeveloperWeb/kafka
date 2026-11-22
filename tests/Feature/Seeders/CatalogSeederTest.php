<?php

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CatalogSeeder;

test('catalog seeder creates categories and products', function () {
    $this->seed(CatalogSeeder::class);

    $product = Product::query()->where('slug', 'cien-anos-de-soledad')->firstOrFail();

    expect(Category::count())->toBe(9)
        ->and(Product::count())->toBe(10)
        ->and($product->category->slug)->toBe('libros-fisicos')
        ->and($product->format)->toBe(ProductFormat::Printed)
        ->and((float) $product->price)->toBe(499.0)
        ->and($product->availability)->toBe(ProductAvailability::InStock);
});

test('catalog seeder marks one product as a preorder', function () {
    $this->seed(CatalogSeeder::class);

    $preorder = Product::query()->where('slug', 'el-aleph')->firstOrFail();

    expect($preorder->availability)->toBe(ProductAvailability::OnDemand)
        ->and((float) $preorder->deposit_amount)->toBe(100.0)
        ->and($preorder->releases_at)->not->toBeNull()
        ->and($preorder->isPreorder())->toBeTrue();
});

test('catalog seeder is idempotent', function () {
    $this->seed(CatalogSeeder::class);
    $this->seed(CatalogSeeder::class);

    expect(Category::count())->toBe(9)
        ->and(Product::count())->toBe(10);
});

test('catalog seeder marks digital and sold out products', function () {
    $this->seed(CatalogSeeder::class);

    $ebook = Product::query()->where('slug', 'la-metamorfosis-digital')->firstOrFail();
    $soldOut = Product::query()->where('slug', 'marcapaginas-de-laton')->firstOrFail();

    expect($ebook->format)->toBe(ProductFormat::Ebook)
        ->and($ebook->availability)->toBe(ProductAvailability::OutOfStock)
        ->and($soldOut->availability)->toBe(ProductAvailability::OutOfStock)
        ->and($soldOut->stock)->toBe(0);
});
