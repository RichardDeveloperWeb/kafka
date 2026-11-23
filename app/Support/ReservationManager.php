<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Enums\PaymentTransactionType;
use App\Enums\ProductFormat;
use App\Enums\ReservationStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReservationManager
{
    private const CHARGE_BATCH_SIZE = 100;

    public function reserveDeposit(User $user, Product $product, int $quantity, float $deposit, Address $address): Reservation
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $product, $quantity, $deposit, $address): Reservation {
            if ($quantity < 1 || $quantity > 99) {
                throw ValidationException::withMessages([
                    'quantity' => 'La cantidad debe estar entre 1 y 99.',
                ]);
            }

            $lockedProduct = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();

            if (! $lockedProduct->isPreorder()) {
                throw ValidationException::withMessages([
                    'product' => 'Este producto no está disponible para apartar.',
                ]);
            }

            $deposit = round($deposit, 2);
            $minimum = $lockedProduct->minimumDepositFor($quantity);
            $maximum = round((float) $lockedProduct->price * $quantity, 2);

            if ($deposit < $minimum) {
                throw ValidationException::withMessages([
                    'deposit_amount' => "El depósito mínimo es {$minimum}.",
                ]);
            }

            if ($deposit > $maximum) {
                throw ValidationException::withMessages([
                    'deposit_amount' => "El depósito no puede superar {$maximum}.",
                ]);
            }

            $reserved = 0;
            $activeReservations = Reservation::query()
                ->where('product_id', $lockedProduct->id)
                ->where('status', ReservationStatus::Deposited)
                ->lockForUpdate()
                ->get(['quantity']);

            foreach ($activeReservations as $activeReservation) {
                $reserved += (int) $activeReservation->quantity;
            }

            if ($reserved + $quantity > $lockedProduct->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'No hay suficientes lugares disponibles para apartar.',
                ]);
            }

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'product_id' => $lockedProduct->id,
                'quantity' => $quantity,
                'status' => ReservationStatus::Deposited,
                'deposit_amount' => $deposit,
                'balance_amount' => round($maximum - $deposit, 2),
                'releases_at' => $lockedProduct->getAttribute('releases_at'),
                'shipping_address' => $this->addressSnapshot($address),
            ]);

            PaymentTransaction::create([
                'reservation_id' => $reservation->id,
                'type' => PaymentTransactionType::Deposit,
                'amount' => $deposit,
                'processed_at' => now(),
            ]);

            $user->cartItems()->where('product_id', $lockedProduct->id)->delete();

            return $reservation;
        });
    }

    public function cancel(User $user, Reservation $reservation): void
    {
        abort_unless($reservation->user_id === $user->id, 404);

        $this->cancelReservation($reservation);
    }

    public function cancelAsAdmin(Reservation $reservation): void
    {
        $this->cancelReservation($reservation);
    }

    private function cancelReservation(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation): void {
            $locked = Reservation::query()->whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            $releasesAt = $locked->getAttribute('releases_at');

            if ($locked->getAttribute('status') !== ReservationStatus::Deposited) {
                throw ValidationException::withMessages([
                    'reservation' => 'Este apartado ya no se puede cancelar.',
                ]);
            }

            if (! $releasesAt instanceof CarbonInterface || $releasesAt->isPast()) {
                throw ValidationException::withMessages([
                    'reservation' => 'La fecha de salida ya pasó y el apartado ya fue cobrado.',
                ]);
            }

            $locked->update([
                'status' => ReservationStatus::Refunded,
                'refunded_at' => now(),
            ]);

            PaymentTransaction::create([
                'reservation_id' => $locked->id,
                'type' => PaymentTransactionType::Refund,
                'amount' => (float) $locked->deposit_amount,
                'processed_at' => now(),
            ]);
        });
    }

    public function chargeDue(): int
    {
        $charged = 0;
        $ids = Reservation::query()
            ->chargeable()
            ->orderBy('id')
            ->limit(self::CHARGE_BATCH_SIZE)
            ->pluck('id');

        foreach ($ids as $id) {
            DB::transaction(function () use ($id, &$charged): void {
                $reservation = Reservation::query()->whereKey($id)->lockForUpdate()->first();

                if (! $reservation instanceof Reservation || $reservation->getAttribute('status') !== ReservationStatus::Deposited) {
                    return;
                }

                $releasesAt = $reservation->getAttribute('releases_at');

                if (! $releasesAt instanceof CarbonInterface || $releasesAt->isFuture()) {
                    return;
                }

                $product = Product::query()->whereKey($reservation->product_id)->lockForUpdate()->firstOrFail();
                $format = $product->getAttribute('format');
                $subtotal = round((float) $product->price * $reservation->quantity, 2);
                $shippingTotal = $format === ProductFormat::Printed ? CartView::SHIPPING_TOTAL : 0.0;
                $total = round($subtotal + $shippingTotal, 2);
                $balance = round($total - (float) $reservation->deposit_amount, 2);

                $order = Order::create([
                    'user_id' => $reservation->user_id,
                    'status' => OrderStatus::ReadyToShip,
                    'subtotal' => $subtotal,
                    'shipping_total' => $shippingTotal,
                    'total' => $total,
                    'deposit_amount' => (float) $reservation->deposit_amount,
                    'balance_amount' => $balance,
                    'shipping_address' => $reservation->shipping_address,
                    'placed_at' => now(),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'author' => $product->author,
                    'format' => $format instanceof ProductFormat ? $format : ProductFormat::Printed,
                    'quantity' => $reservation->quantity,
                    'unit_price' => (float) $product->price,
                    'line_total' => $subtotal,
                ]);

                PaymentTransaction::create([
                    'reservation_id' => $reservation->id,
                    'order_id' => $order->id,
                    'type' => PaymentTransactionType::Balance,
                    'amount' => $balance,
                    'processed_at' => now(),
                ]);

                $reservation->update([
                    'status' => ReservationStatus::Charged,
                    'order_id' => $order->id,
                    'charged_at' => now(),
                ]);

                $product->decrement('stock', $reservation->quantity);
                $charged++;
            });
        }

        return $charged;
    }

    /**
     * @return array<string, string|null>
     */
    private function addressSnapshot(Address $address): array
    {
        return [
            'label' => $address->label,
            'recipient_name' => $address->recipient_name,
            'street' => $address->street,
            'exterior_number' => $address->exterior_number,
            'interior_number' => $address->interior_number,
            'neighborhood' => $address->neighborhood,
            'city' => $address->city,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country,
            'phone' => $address->phone,
        ];
    }
}
