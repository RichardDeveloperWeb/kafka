<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckoutManager
{
    public function __construct(
        private readonly CartManager $cartManager,
        private readonly ReservationManager $reservationManager,
    ) {}

    public function checkout(User $user, Address $address): Order
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $address): Order {
            $items = $this->cartManager->items($user);

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Tu carrito está vacío.',
                ]);
            }

            $subtotal = 0.0;
            $requiresShipping = false;
            $lineTotals = [];

            foreach ($items as $item) {
                $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $availability = $product->getAttribute('availability');
                $format = $product->getAttribute('format');

                if (! $product->is_active || $availability === ProductAvailability::OutOfStock) {
                    throw ValidationException::withMessages([
                        'cart' => 'Uno de los productos de tu carrito ya no está disponible.',
                    ]);
                }

                if ($availability === ProductAvailability::InStock && $item->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'Uno de los productos de tu carrito no tiene suficiente existencia.',
                    ]);
                }

                $lineTotal = round((float) $product->price * $item->quantity, 2);
                $lineTotals[$item->id] = $lineTotal;
                $subtotal += $lineTotal;

                if ($format === ProductFormat::Printed) {
                    $requiresShipping = true;
                }
            }

            $subtotal = round($subtotal, 2);
            $shippingTotal = $requiresShipping ? CartView::SHIPPING_TOTAL : 0.0;

            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::Confirmed,
                'subtotal' => $subtotal,
                'shipping_total' => $shippingTotal,
                'total' => round($subtotal + $shippingTotal, 2),
                'shipping_address' => $this->addressSnapshot($address),
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $format = $product->getAttribute('format');
                $availability = $product->getAttribute('availability');

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'author' => $product->author,
                    'format' => $format instanceof ProductFormat ? $format : ProductFormat::Printed,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $product->price,
                    'line_total' => $lineTotals[$item->id],
                ]);

                if ($availability === ProductAvailability::InStock) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            $this->reservationManager->consume($user, $items);

            CartItem::query()->where('user_id', $user->id)->delete();

            return $order;
        });
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
