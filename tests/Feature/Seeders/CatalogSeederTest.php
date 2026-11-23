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
        ->and(Product::count())->toBe(13)
        ->and($product->category->slug)->toBe('libros-fisicos')
        ->and($product->format)->toBe(ProductFormat::Printed)
        ->and((float) $product->price)->toBe(499.0)
        ->and($product->availability)->toBe(ProductAvailability::InStock);
});

test('catalog seeder marks four products as preorders', function () {
    $this->seed(CatalogSeeder::class);

    $preorders = [
        'el-aleph' => 100,
        'los-libros-de-jacob' => 140,
        'nuestra-parte-de-noche' => 160,
        'la-biblioteca-de-la-media-noche' => 80,
    ];

    expect(Product::preorder()->count())->toBe(4);

    foreach ($preorders as $slug => $deposit) {
        $preorder = Product::query()->where('slug', $slug)->firstOrFail();

        expect($preorder->availability)->toBe(ProductAvailability::OnDemand)
            ->and((float) $preorder->deposit_amount)->toBe((float) $deposit)
            ->and($preorder->releases_at)->not->toBeNull()
            ->and($preorder->isPreorder())->toBeTrue();
    }
});

test('catalog seeder is idempotent', function () {
    $this->seed(CatalogSeeder::class);
    $this->seed(CatalogSeeder::class);

    expect(Category::count())->toBe(9)
        ->and(Product::count())->toBe(13);
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
