<?php

use App\Models\Product;
use Database\Seeders\CatalogSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('home exposes featured products and categories', function () {
    $this->seed(CatalogSeeder::class);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('featuredProducts', 4)
            ->has('categories', 9)
            ->where('featuredProducts.0.slug', 'atlas-ilustrado-del-mundo')
            ->etc());
});

test('catalog lists active products', function () {
    $this->seed(CatalogSeeder::class);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('catalog/Index')
            ->has('products.data', 12)
            ->where('products.total', 13)
            ->where('filters.category', '')
            ->where('filters.q', '')
            ->etc());
});

test('catalog can be filtered by category and search term', function () {
    $this->seed(CatalogSeeder::class);

    $this->get(route('catalog.index', ['category' => 'libros-fisicos']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.category', 'libros-fisicos')
            ->has('products.data', 4)
            ->etc());

    $this->get(route('catalog.index', ['q' => 'Kafka']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.q', 'Kafka')
            ->has('products.data', 1)
            ->where('products.data.0.slug', 'la-metamorfosis-digital')
            ->etc());
});

test('catalog shows a product and related products', function () {
    $this->seed(CatalogSeeder::class);

    $product = Product::query()->where('slug', 'cien-anos-de-soledad')->firstOrFail();

    $this->get(route('catalog.show', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('catalog/Show')
            ->where('product.slug', 'cien-anos-de-soledad')
            ->where('product.price', 499)
            ->where('product.isPreorder', false)
            ->has('addresses', 0)
            ->has('relatedProducts', 3)
            ->etc());
});

test('catalog does not show inactive products', function () {
    $this->seed(CatalogSeeder::class);

    $product = Product::query()->where('slug', 'el-aleph')->firstOrFail();
    $product->update(['is_active' => false]);

    $this->get(route('catalog.show', $product))->assertNotFound();
});

test('static public pages render', function (string $routeName, string $component) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component)->etc());
})->with([
    'about' => ['about', 'public/About'],
    'privacy' => ['privacy', 'public/Privacy'],
    'terms' => ['terms', 'public/Terms'],
    'cookies' => ['cookies', 'public/Cookies'],
]);

test('contact request requires consent and privacy acceptance', function () {
    $this->post(route('contact.store'), [
        'name' => 'Ana Pérez',
        'email' => 'ana@example.com',
        'message' => 'Quiero información sobre el club de lectura.',
    ])->assertSessionHasErrors(['consent', 'privacy_accepted']);
});

test('contact request stores the message', function () {
    $this->post(route('contact.store'), [
        'name' => 'Ana Pérez',
        'email' => 'ana@example.com',
        'phone' => '2721234567',
        'message' => 'Quiero información sobre el club de lectura.',
        'consent' => true,
        'privacy_accepted' => true,
    ])->assertRedirect(route('contact'));

    $this->assertDatabaseHas('contact_messages', [
        'email' => 'ana@example.com',
        'message' => 'Quiero información sobre el club de lectura.',
        'consent' => 1,
        'privacy_accepted' => 1,
    ]);
});
