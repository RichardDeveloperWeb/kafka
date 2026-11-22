<?php

namespace Database\Seeders;

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
            ->map(fn (string $permission) => Permission::findOrCreate($permission, 'web'));

        foreach (UserRole::cases() as $role) {
            $spatieRole = Role::findOrCreate($role->value, 'web');
            $rolePermissions = $role === UserRole::Client
                ? $permissions->whereIn('name', Permissions::CLIENT)
                : $permissions;

            $spatieRole->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
