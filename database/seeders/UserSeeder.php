<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => UserRole::Client,
            ],
            [
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'Super Administrador',
                'email' => 'superadmin@example.com',
                'role' => UserRole::SuperAdmin,
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate([
                'email' => $data['email'],
            ], [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'phone' => '272 123 4567',
                'email_verified_at' => now(),
            ]);

            $user->syncRoles([$data['role']->value]);
        }
    }
}
