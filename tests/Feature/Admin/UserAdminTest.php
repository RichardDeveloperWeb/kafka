<?php

use App\Enums\ManagerRole;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function adminUserPayload(array $overrides = []): array
{
    return [
        'name' => 'María Martínez',
        'email' => 'maria@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '272 123 4567',
        'roles' => [UserRole::Client->value],
        ...$overrides,
    ];
}

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->admin = User::factory()->admin()->create();

    $this->actingAs($this->admin);
});

test('admin can create edit and delete users with roles', function () {
    $this->post(route('admin.users.store'), adminUserPayload())
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'maria@example.com')->firstOrFail();

    expect($user->hasRole(UserRole::Client->value))->toBeTrue();

    $this->patch(route('admin.users.update', $user), adminUserPayload([
        'name' => 'María Pérez',
        'roles' => [ManagerRole::Users->value],
    ]))->assertRedirect(route('admin.users.index'));

    expect($user->refresh()->name)->toBe('María Pérez')
        ->and($user->hasRole(ManagerRole::Users->value))->toBeTrue()
        ->and($user->hasRole(UserRole::Client->value))->toBeFalse();

    $this->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($user->id))->toBeNull();
});

test('admin cannot escalate privileges or manage super admins', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->post(route('admin.users.store'), adminUserPayload([
        'email' => 'nuevo-superadmin@example.com',
        'roles' => [UserRole::SuperAdmin->value],
    ]))->assertForbidden();

    $this->patch(route('admin.users.update', $superAdmin), adminUserPayload([
        'roles' => [UserRole::Client->value],
    ]))->assertForbidden();

    $this->delete(route('admin.users.destroy', $superAdmin))->assertForbidden();
    $this->delete(route('admin.users.destroy', $this->admin))->assertForbidden();

    expect(User::find($superAdmin->id))->not->toBeNull()
        ->and(User::find($this->admin->id))->not->toBeNull()
        ->and($superAdmin->refresh()->hasRole(UserRole::SuperAdmin->value))->toBeTrue();
});

test('super admin can update another super admin account', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin);

    $this->patch(route('admin.users.update', $superAdmin), adminUserPayload([
        'name' => 'Super Administrador Actualizado',
        'roles' => [UserRole::SuperAdmin->value, UserRole::Admin->value],
    ]))->assertRedirect(route('admin.users.index'));

    expect($superAdmin->refresh()->name)->toBe('Super Administrador Actualizado')
        ->and($superAdmin->hasRole(UserRole::SuperAdmin->value))->toBeTrue()
        ->and($superAdmin->hasRole(UserRole::Admin->value))->toBeTrue();
});
