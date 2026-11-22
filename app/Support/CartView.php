<?php

namespace App\Support;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

final class CartView
{
    public const SHIPPING_TOTAL = 80.0;

    /**
     * @param  Collection<int, CartItem>  $items
     * @return array{items: array<int, array<string, mixed>>, count: int, subtotal: float, shippingTotal: float, total: float, requiresShipping: bool}
     */
    public static function summary(Collection $items): array
    {
        $items->loadMissing('product');

        $views = $items
            ->map(fn (CartItem $item): array => self::item($item))
            ->values()
            ->all();

        $subtotal = round(array_sum(array_column($views, 'lineTotal')), 2);
        $requiresShipping = array_column($views, 'requiresShipping') === [] ? false : in_array(true, array_column($views, 'requiresShipping'), true);
        $shippingTotal = $requiresShipping ? self::SHIPPING_TOTAL : 0.0;

        return [
            'items' => $views,
            'count' => (int) array_sum(array_column($views, 'quantity')),
            'subtotal' => $subtotal,
            'shippingTotal' => round($shippingTotal, 2),
            'total' => round($subtotal + $shippingTotal, 2),
            'requiresShipping' => $requiresShipping,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function item(CartItem $item): array
    {
        $product = $item->product;
        $price = (float) $product->price;
        $quantity = $item->quantity;
        $format = $product->getAttribute('format');
        $availability = $product->getAttribute('availability');
        $formatValue = $format instanceof ProductFormat ? $format->value : null;
        $availabilityValue = $availability instanceof ProductAvailability ? $availability->value : null;

        return [
            'id' => $item->id,
            'quantity' => $quantity,
            'productId' => $product->id,
            'slug' => $product->slug,
            'title' => $product->title,
            'author' => $product->author,
            'price' => $price,
            'lineTotal' => round($price * $quantity, 2),
            'stock' => $product->stock,
            'format' => $formatValue,
            'formatLabel' => $format instanceof ProductFormat ? $format->label() : null,
            'availability' => $availabilityValue,
            'availabilityLabel' => $availability instanceof ProductAvailability ? $availability->label() : null,
            'requiresShipping' => $formatValue === ProductFormat::Printed->value,
        ];
    }
}
