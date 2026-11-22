<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Gate;

test('clients receive customer permissions without admin access', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->client()->create();

    expect($user->hasRole(UserRole::Client->value))->toBeTrue()
        ->and($user->can(Permissions::CART_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::ADDRESSES_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::RESERVATIONS_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::ORDERS_VIEW_OWN))->toBeTrue()
        ->and($user->can(Permissions::CONTACT_SEND))->toBeTrue()
        ->and($user->can(Permissions::PRODUCTS_MANAGE))->toBeFalse()
        ->and($user->can(Permissions::USERS_MANAGE))->toBeFalse()
        ->and(Gate::forUser($user)->allows('access-admin-panel'))->toBeFalse();
});

test('admins receive management permissions', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->admin()->create();

    expect($user->can(Permissions::ACCESS_ADMIN_PANEL))->toBeTrue()
        ->and($user->can(Permissions::PRODUCTS_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::CATEGORIES_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::ORDERS_VIEW_ANY))->toBeTrue()
        ->and($user->can(Permissions::ORDERS_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::USERS_VIEW_ANY))->toBeTrue()
        ->and($user->can(Permissions::USERS_MANAGE))->toBeTrue()
        ->and($user->can(Permissions::MESSAGES_VIEW_ANY))->toBeTrue()
        ->and($user->can(Permissions::MESSAGES_MANAGE))->toBeTrue()
        ->and(Gate::forUser($user)->allows('access-admin-panel'))->toBeTrue();
});

test('super admins bypass every gate ability', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->superAdmin()->create();

    expect($user->hasRole(UserRole::SuperAdmin->value))->toBeTrue()
        ->and(Gate::forUser($user)->allows('access-admin-panel'))->toBeTrue()
        ->and(Gate::forUser($user)->allows('any.future.ability'))->toBeTrue();
});
