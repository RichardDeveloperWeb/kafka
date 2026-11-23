<?php

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(CatalogSeeder::class);
});

test('user seeder creates demo clients with orders and messages', function () {
    $this->seed(UserSeeder::class);

    $client = User::query()->where('email', 'cliente@example.com')->firstOrFail();
    $secondClient = User::query()->where('email', 'cliente2@example.com')->firstOrFail();

    $printedOrder = $client->orders()->where('status', OrderStatus::Delivered)->firstOrFail();
    $preorder = $client->orders()->where('status', OrderStatus::Confirmed)->firstOrFail();
    $digitalOrder = $secondClient->orders()->firstOrFail();

    expect($client->hasRole(UserRole::Client->value))->toBeTrue()
        ->and($secondClient->hasRole(UserRole::Client->value))->toBeTrue()
        ->and($client->orders()->count())->toBe(2)
        ->and($secondClient->orders()->count())->toBe(1)
        ->and((float) $printedOrder->total)->toBe(769.0)
        ->and((float) $preorder->total)->toBe(600.0)
        ->and((float) $preorder->deposit_amount)->toBe(140.0)
        ->and((float) $preorder->balance_amount)->toBe(460.0)
        ->and((float) $digitalOrder->total)->toBe(359.0)
        ->and((float) $digitalOrder->shipping_total)->toBe(0.0)
        ->and($preorder->items()->count())->toBe(1)
        ->and(ContactMessage::query()->where('email', 'cliente@example.com')->whereNotNull('read_at')->exists())->toBeTrue()
        ->and(ContactMessage::query()->where('email', 'cliente2@example.com')->whereNull('read_at')->exists())->toBeTrue();
});

test('user seeder is idempotent', function () {
    $this->seed(UserSeeder::class);
    $this->seed(UserSeeder::class);

    expect(User::count())->toBe(5)
        ->and(Order::count())->toBe(3)
        ->and(OrderItem::count())->toBe(5)
        ->and(ContactMessage::count())->toBe(3);
});
