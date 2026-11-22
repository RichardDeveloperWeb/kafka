<?php

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\ProductFormat;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;

test('product relationships and casts resolve through the database', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    $user = User::factory()->create();
    $cartItem = CartItem::factory()->for($user)->for($product)->create(['quantity' => 2]);

    expect($product->category->is($category))->toBeTrue()
        ->and($product->format)->toBe(ProductFormat::Printed)
        ->and($product->cartItems->first()->is($cartItem))->toBeTrue()
        ->and($user->cartItems->first()->is($cartItem))->toBeTrue()
        ->and($cartItem->quantity)->toBe(2);
});

test('order snapshots keep their items and status', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->for($user)->create();
    OrderItem::factory()->for($order)->for($product)->count(2)->create();

    expect($order->status)->toBe(OrderStatus::Confirmed)
        ->and($order->user->is($user))->toBeTrue()
        ->and($order->items)->toHaveCount(2)
        ->and($order->shipping_address['city'])->toBe('Orizaba')
        ->and($order->items->first()->product->is($product))->toBeTrue();
});

test('preorder products and reservations expose their scopes', function () {
    $user = User::factory()->create();
    $preorder = Product::factory()->preorder()->create();
    $stocked = Product::factory()->create();
    $deposited = Reservation::factory()->for($user)->for($preorder)->create();
    $due = Reservation::factory()->for($user)->for($preorder)->due()->create();
    $charged = Reservation::factory()->charged()->create();

    expect($preorder->isPreorder())->toBeTrue()
        ->and($stocked->isPreorder())->toBeFalse()
        ->and(Product::preorder()->pluck('id')->all())->toContain($preorder->id)
        ->and(Product::preorder()->pluck('id')->all())->not->toContain($stocked->id)
        ->and(Reservation::pendingRelease()->pluck('id')->all())->toContain($deposited->id)
        ->and(Reservation::pendingRelease()->pluck('id')->all())->not->toContain($due->id)
        ->and(Reservation::chargeable()->pluck('id')->all())->toContain($due->id)
        ->and(Reservation::chargeable()->pluck('id')->all())->not->toContain($charged->id);
});

test('address casts and default scope resolve through the database', function () {
    $user = User::factory()->create();
    $address = Address::factory()->for($user)->default()->create();

    expect($address->type)->toBe(AddressType::Shipping)
        ->and($address->is_default)->toBeTrue()
        ->and($address->user->is($user))->toBeTrue()
        ->and(Address::default()->pluck('id')->all())->toContain($address->id);
});
