<?php

namespace Database\Seeders;

use App\Enums\ManagerRole;
use App\Enums\UserRole;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(Permissions::ALL)
            ->map(fn (string $permission) => Permission::findOrCreate($permission, 'web'))
            ->keyBy('name');

        foreach (UserRole::cases() as $role) {
            $spatieRole = Role::findOrCreate($role->value, 'web');
            $rolePermissions = $role === UserRole::Client
                ? Permissions::CLIENT
                : Permissions::ADMIN;

            $spatieRole->syncPermissions($permissions->whereIn('name', $rolePermissions));
        }

        foreach (ManagerRole::cases() as $role) {
            $spatieRole = Role::findOrCreate($role->value, 'web');

            $spatieRole->syncPermissions($permissions->whereIn('name', $role->permissions()));
        }

        $roleNames = [
            ...array_map(fn (UserRole $role): string => $role->value, UserRole::cases()),
            ...array_map(fn (ManagerRole $role): string => $role->value, ManagerRole::cases()),
        ];

        Role::where('guard_name', 'web')
            ->whereNotIn('name', $roleNames)
            ->delete();

        Permission::where('guard_name', 'web')
            ->whereNotIn('name', Permissions::ALL)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
