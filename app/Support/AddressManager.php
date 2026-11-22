<?php

namespace App\Support;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class AddressManager
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(User $user, array $attributes): Address
    {
        return DB::transaction(function () use ($user, $attributes): Address {
            $data = $this->forUser($attributes, $user);

            if ($data['is_default'] === true) {
                $this->clearDefaults($user);
            }

            return Address::create($data);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(User $user, Address $address, array $attributes): Address
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $address, $attributes): Address {
            $wasDefault = $address->is_default;
            $data = $this->forUser($attributes, $user);

            if ($data['is_default'] === true) {
                $this->clearDefaults($user, $address->id);
            }

            $address->update($data);

            if ($wasDefault && $data['is_default'] !== true) {
                $replacement = Address::query()
                    ->where('user_id', $user->id)
                    ->where('type', AddressType::Shipping)
                    ->whereKeyNot($address->id)
                    ->first();

                $replacement?->update(['is_default' => true]);
            }

            return $address->refresh();
        });
    }

    public function delete(User $user, Address $address): void
    {
        abort_unless($address->user_id === $user->id, 404);

        DB::transaction(function () use ($user, $address): void {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $replacement = Address::query()
                    ->where('user_id', $user->id)
                    ->where('type', AddressType::Shipping)
                    ->first();

                $replacement?->update(['is_default' => true]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function forUser(array $attributes, User $user): array
    {
        return [
            ...$attributes,
            'user_id' => $user->id,
            'is_default' => (bool) ($attributes['is_default'] ?? false),
        ];
    }

    private function clearDefaults(User $user, ?int $except = null): void
    {
        $query = Address::query()
            ->where('user_id', $user->id)
            ->where('is_default', true);

        if ($except !== null) {
            $query->whereKeyNot($except);
        }

        $query->update(['is_default' => false]);
    }
}
