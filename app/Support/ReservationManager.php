<?php

namespace App\Support;

use App\Enums\ProductAvailability;
use App\Enums\ReservationStatus;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReservationManager
{
    public const HOLD_MINUTES = 15;

    public function expireStale(?User $user = null): int
    {
        $query = Reservation::query()
            ->where('status', ReservationStatus::Active)
            ->where('expires_at', '<=', now());

        if ($user !== null) {
            $query->where('user_id', $user->id);
        }

        return $query->update([
            'status' => ReservationStatus::Expired,
            'released_at' => now(),
        ]);
    }

    public function reserve(User $user, Product $product, int $quantity): Reservation
    {
        return DB::transaction(function () use ($user, $product, $quantity): Reservation {
            $lockedProduct = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
            $availability = $lockedProduct->getAttribute('availability');

            if (! $lockedProduct->is_active || $availability === ProductAvailability::OutOfStock) {
                throw ValidationException::withMessages([
                    'product' => 'Este producto no está disponible en este momento.',
                ]);
            }

            $reserved = (int) Reservation::query()
                ->active()
                ->where('product_id', $lockedProduct->id)
                ->sum('quantity');

            if ($availability === ProductAvailability::InStock && $reserved + $quantity > $lockedProduct->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'No hay suficientes ejemplares para apartar.',
                ]);
            }

            return Reservation::create([
                'user_id' => $user->id,
                'product_id' => $lockedProduct->id,
                'quantity' => $quantity,
                'status' => ReservationStatus::Active,
                'expires_at' => now()->addMinutes(self::HOLD_MINUTES),
            ]);
        });
    }

    public function cancel(User $user, Reservation $reservation): void
    {
        abort_unless($reservation->user_id === $user->id, 404);

        $status = $reservation->getAttribute('status');

        if (! $status instanceof ReservationStatus || $status !== ReservationStatus::Active) {
            return;
        }

        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'released_at' => now(),
        ]);
    }

    /**
     * @param  Collection<int, CartItem>  $items
     */
    public function consume(User $user, Collection $items): void
    {
        foreach ($items as $item) {
            Reservation::query()
                ->where('user_id', $user->id)
                ->where('product_id', $item->product_id)
                ->active()
                ->get()
                ->each(fn (Reservation $reservation) => $reservation->update([
                    'status' => ReservationStatus::Used,
                    'used_at' => now(),
                ]));
        }
    }
}
