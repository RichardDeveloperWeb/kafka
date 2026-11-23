<?php

namespace App\Support\Admin;

use App\Enums\UserRole;
use App\Models\User;

final class UserManager
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data, User $actor, ?User $user = null): User
    {
        $roles = array_values(array_unique(array_map('strval', $data['roles'] ?? [])));
        $targetIsSuperAdmin = $user?->hasRole(UserRole::SuperAdmin->value) ?? false;
        $actorIsSuperAdmin = $actor->hasRole(UserRole::SuperAdmin->value);

        abort_if(! $actorIsSuperAdmin && $targetIsSuperAdmin, 403);
        abort_if(! $actorIsSuperAdmin && in_array(UserRole::SuperAdmin->value, $roles, true), 403);
        abort_if(
            $user?->is($actor) === true && $targetIsSuperAdmin && ! in_array(UserRole::SuperAdmin->value, $roles, true),
            403,
        );

        $attributes = [
            'name' => (string) $data['name'],
            'email' => (string) $data['email'],
            'phone' => is_string($data['phone'] ?? null) && $data['phone'] !== '' ? $data['phone'] : null,
        ];

        if (is_string($data['password'] ?? null) && $data['password'] !== '') {
            $attributes['password'] = $data['password'];
        }

        $user ??= new User;
        $user->fill($attributes);
        $user->save();
        $user->syncRoles($roles);

        return $user;
    }

    public function destroy(User $user, User $actor): void
    {
        abort_if($user->is($actor), 403);
        abort_if($user->hasRole(UserRole::SuperAdmin->value) && ! $actor->hasRole(UserRole::SuperAdmin->value), 403);

        $user->delete();
    }
}
