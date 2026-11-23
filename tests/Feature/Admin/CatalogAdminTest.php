<?php

use App\Enums\ProductAvailability;
use App\Models\Category;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function adminProductPayload(Category $category, array $overrides = []): array
{
    return [
        'category_id' => $category->id,
        'title' => 'La ciudad y sus muros',
        'slug' => 'la-ciudad-y-sus-muros',
        'author' => 'Ana García',
        'publisher' => 'Editorial Orizaba',
        'isbn' => '9781234567890',
        'format' => 'impreso',
        'language' => 'es',
        'pages' => 240,
        'edition_year' => 2025,
        'synopsis' => 'Una novela sobre la memoria.',
        'price' => 300,
        'stock' => 10,
        'availability' => ProductAvailability::OnDemand->value,
        'deposit_amount' => 50,
        'releases_at' => now()->addMonth()->toDateString(),
        'is_active' => true,
        ...$overrides,
    ];
}

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->actingAs(User::factory()->admin()->create());
});

test('admin can create and list products with preorder fields', function () {
    $category = Category::factory()->create();

    $this->post(route('admin.products.store'), adminProductPayload($category))
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('slug', 'la-ciudad-y-sus-muros')->firstOrFail();

    expect($product->availability)->toBe(ProductAvailability::OnDemand)
        ->and((float) $product->deposit_amount)->toBe(50.0)
        ->and($product->releases_at)->not->toBeNull()
        ->and($product->is_active)->toBeTrue();

    $this->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/Index')
            ->where('products.0.slug', $product->slug)
            ->where('products.0.isPreorder', true)
            ->where('categories.0.productsCount', 1)
            ->where('categories.0.isActive', true)
            ->etc());
});

test('admin can update a product using its slug', function () {
    $product = Product::factory()->create();

    $this->patch(
        route('admin.products.update', $product->slug),
        adminProductPayload($product->category, [
            'title' => 'El jardín de las palabras',
            'slug' => 'el-jardin-de-las-palabras',
            'availability' => ProductAvailability::InStock->value,
            'deposit_amount' => null,
            'releases_at' => null,
        ]),
    )->assertRedirect(route('admin.products.index'));

    expect($product->refresh()->title)->toBe('El jardín de las palabras')
        ->and($product->slug)->toBe('el-jardin-de-las-palabras')
        ->and($product->availability)->toBe(ProductAvailability::InStock)
        ->and($product->deposit_amount)->toBeNull()
        ->and($product->releases_at)->toBeNull();
});

test('product preorder validation rejects deposits above the price', function () {
    $category = Category::factory()->create();

    $this->post(
        route('admin.products.store'),
        adminProductPayload($category, ['deposit_amount' => 301]),
    )->assertSessionHasErrors('deposit_amount');

    expect(Product::query()->where('slug', 'la-ciudad-y-sus-muros')->exists())->toBeFalse();
});

test('admin cannot delete products with bookstore activity', function () {
    $product = Product::factory()->create();

    Reservation::factory()->for($product)->create();

    $this->delete(route('admin.products.destroy', $product->slug))
        ->assertSessionHasErrors('product');

    expect(Product::find($product->id))->not->toBeNull();

    Reservation::query()->where('product_id', $product->id)->delete();

    $this->delete(route('admin.products.destroy', $product->slug))
        ->assertRedirect(route('admin.products.index'));

    expect(Product::find($product->id))->toBeNull();
});

test('admin can manage categories and cannot delete categories with products', function () {
    $this->post(route('admin.categories.store'), [
        'name' => 'Poesía',
        'slug' => 'poesia',
        'description' => 'Versos y prosas poéticas.',
        'is_active' => true,
    ])->assertRedirect(route('admin.categories.index'));

    $category = Category::query()->where('slug', 'poesia')->firstOrFail();

    expect($category->is_active)->toBeTrue();

    $this->patch(route('admin.categories.update', $category->slug), [
        'name' => 'Poesía contemporánea',
        'slug' => 'poesia-contemporanea',
        'description' => null,
        'is_active' => false,
    ])->assertRedirect(route('admin.categories.index'));

    expect($category->refresh()->name)->toBe('Poesía contemporánea')
        ->and($category->slug)->toBe('poesia-contemporanea')
        ->and($category->is_active)->toBeFalse();

    Product::factory()->for($category)->create();

    $this->delete(route('admin.categories.destroy', $category->slug))
        ->assertSessionHasErrors('category');

    expect(Category::find($category->id))->not->toBeNull();

    Product::query()->where('category_id', $category->id)->delete();

    $this->delete(route('admin.categories.destroy', $category->slug))
        ->assertRedirect(route('admin.categories.index'));

    expect(Category::find($category->id))->toBeNull();
});
