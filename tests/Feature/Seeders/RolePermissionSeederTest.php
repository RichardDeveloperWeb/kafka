<?php

use App\Enums\ManagerRole;
use App\Enums\UserRole;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('clients receive customer permissions without admin access', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->client()->create();

    expect($user->hasRole(UserRole::Client->value))->toBeTrue();

    foreach (Permissions::CLIENT as $permission) {
        expect($user->can($permission))->toBeTrue();
    }

    expect($user->can(Permissions::USERS_INDEX))->toBeFalse();
    expect($user->can(Permissions::PRODUCTS_CREATE))->toBeFalse();
    expect($user->can(Permissions::MESSAGES_INDEX))->toBeFalse();
    expect(Gate::forUser($user)->allows('admin.index'))->toBeFalse();
});

test('admins receive management permissions', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->admin()->create();

    expect($user->hasRole(UserRole::Admin->value))->toBeTrue();

    foreach (Permissions::ADMIN as $permission) {
        expect($user->can($permission))->toBeTrue();
    }

    foreach (ManagerRole::cases() as $managerRole) {
        expect($user->hasRole($managerRole->value))->toBeFalse();
    }

    expect(Gate::forUser($user)->allows('admin.index'))->toBeTrue();
});

test('manager roles group only their resource permissions', function () {
    $this->seed(RolePermissionSeeder::class);

    foreach (ManagerRole::cases() as $managerRole) {
        $user = User::factory()->withManagerRole($managerRole)->create();

        expect($user->hasRole($managerRole->value))->toBeTrue();

        foreach ($managerRole->permissions() as $permission) {
            expect($user->can($permission))->toBeTrue();
        }

        $unrelatedPermission = match ($managerRole) {
            ManagerRole::Users => Permissions::PRODUCTS_INDEX,
            ManagerRole::Products => Permissions::USERS_INDEX,
            ManagerRole::Categories => Permissions::ORDERS_INDEX,
            ManagerRole::Orders => Permissions::USERS_INDEX,
            ManagerRole::Messages => Permissions::ORDERS_INDEX,
            ManagerRole::Cart => Permissions::ADDRESSES_INDEX,
            ManagerRole::Addresses => Permissions::CART_INDEX,
            ManagerRole::Reservations => Permissions::USERS_INDEX,
        };

        expect($user->can($unrelatedPermission))->toBeFalse();
        expect(Gate::forUser($user)->allows('admin.index'))->toBeFalse();
    }
});

test('seeder removes obsolete permissions and roles', function () {
    $this->seed(RolePermissionSeeder::class);

    Permission::findOrCreate('legacy.manage', 'web');
    Role::findOrCreate('legacy.role', 'web');

    $this->seed(RolePermissionSeeder::class);

    expect(Permission::where('name', 'legacy.manage')->exists())->toBeFalse()
        ->and(Role::where('name', 'legacy.role')->exists())->toBeFalse();
});

test('super admins bypass every gate ability', function () {
    $this->seed(RolePermissionSeeder::class);

    $user = User::factory()->superAdmin()->create();

    expect($user->hasRole(UserRole::SuperAdmin->value))->toBeTrue()
        ->and(Gate::forUser($user)->allows('admin.index'))->toBeTrue()
        ->and(Gate::forUser($user)->allows('any.future.ability'))->toBeTrue();
});
