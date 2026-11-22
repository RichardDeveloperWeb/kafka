<?php

namespace App\Support;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\ProductFormat;
use App\Enums\ReservationStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class ClientView
{
    /**
     * @return array<string, mixed>
     */
    public static function address(Address $address): array
    {
        $type = $address->getAttribute('type');

        return [
            'id' => $address->id,
            'type' => $type instanceof AddressType ? $type->value : null,
            'typeLabel' => $type instanceof AddressType ? $type->label() : null,
            'label' => $address->label,
            'recipientName' => $address->recipient_name,
            'street' => $address->street,
            'exteriorNumber' => $address->exterior_number,
            'interiorNumber' => $address->interior_number,
            'neighborhood' => $address->neighborhood,
            'city' => $address->city,
            'state' => $address->state,
            'postalCode' => $address->postal_code,
            'country' => $address->country,
            'phone' => $address->phone,
            'isDefault' => $address->is_default,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reservation(Reservation $reservation): array
    {
        $status = $reservation->getAttribute('status');
        $expiresAt = $reservation->getAttribute('expires_at');
        $product = $reservation->product;

        return [
            'id' => $reservation->id,
            'quantity' => $reservation->quantity,
            'status' => $status instanceof ReservationStatus ? $status->value : null,
            'statusLabel' => $status instanceof ReservationStatus ? $status->label() : null,
            'expiresAt' => self::dateString($expiresAt),
            'releasedAt' => self::dateString($reservation->getAttribute('released_at')),
            'usedAt' => self::dateString($reservation->getAttribute('used_at')),
            'isActive' => $status === ReservationStatus::Active && self::isFuture($expiresAt),
            'product' => [
                'id' => $product->id,
                'slug' => $product->slug,
                'title' => $product->title,
                'author' => $product->author,
                'price' => (float) $product->price,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function order(Order $order): array
    {
        $status = $order->getAttribute('status');

        return [
            'id' => $order->id,
            'status' => $status instanceof OrderStatus ? $status->value : null,
            'statusLabel' => $status instanceof OrderStatus ? $status->label() : null,
            'subtotal' => (float) $order->subtotal,
            'shippingTotal' => (float) $order->shipping_total,
            'total' => (float) $order->total,
            'shippingAddress' => $order->shipping_address,
            'placedAt' => self::dateString($order->getAttribute('placed_at')),
            'items' => $order->relationLoaded('items')
                ? $order->items->map(fn (OrderItem $item): array => self::orderItem($item))->values()->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function orderItem(OrderItem $item): array
    {
        $format = $item->getAttribute('format');

        return [
            'id' => $item->id,
            'productId' => $item->product_id,
            'title' => $item->title,
            'author' => $item->author,
            'format' => $format instanceof ProductFormat ? $format->value : null,
            'formatLabel' => $format instanceof ProductFormat ? $format->label() : null,
            'quantity' => $item->quantity,
            'unitPrice' => (float) $item->unit_price,
            'lineTotal' => (float) $item->line_total,
        ];
    }

    private static function dateString(mixed $value): ?string
    {
        if ($value instanceof CarbonInterface) {
            return $value->toISOString();
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->toISOString();
        }

        return null;
    }

    private static function isFuture(mixed $value): bool
    {
        if ($value instanceof CarbonInterface) {
            return $value->isFuture();
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->isFuture();
        }

        return false;
    }
}
