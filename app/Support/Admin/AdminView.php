<?php

namespace App\Support\Admin;

use App\Enums\ManagerRole;
use App\Enums\OrderStatus;
use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class AdminView
{
    /**
     * @return array<string, mixed>
     */
    public static function product(Product $product): array
    {
        $availability = $product->getAttribute('availability');
        $format = $product->getAttribute('format');

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'title' => $product->title,
            'author' => $product->author,
            'publisher' => $product->publisher,
            'isbn' => $product->isbn,
            'format' => $format instanceof ProductFormat ? $format->value : null,
            'formatLabel' => $format instanceof ProductFormat ? $format->label() : null,
            'language' => $product->language,
            'pages' => $product->pages,
            'editionYear' => $product->edition_year,
            'synopsis' => $product->synopsis,
            'price' => (float) $product->price,
            'stock' => $product->stock,
            'availability' => $availability instanceof ProductAvailability ? $availability->value : null,
            'availabilityLabel' => $availability instanceof ProductAvailability ? $availability->label() : null,
            'depositAmount' => $product->deposit_amount === null ? null : (float) $product->deposit_amount,
            'releasesAt' => self::dateString($product->getAttribute('releases_at')),
            'categoryId' => $product->category_id,
            'categoryName' => $product->category?->name,
            'isActive' => $product->is_active,
            'isPreorder' => $product->isPreorder(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function category(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'productsCount' => $category->relationLoaded('products')
                ? $category->products->count()
                : (int) ($category->getAttribute('products_count') ?? 0),
            'isActive' => (bool) $category->getAttribute('is_active'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function reservation(Reservation $reservation): array
    {
        $status = $reservation->getAttribute('status');
        $shippingAddress = $reservation->getAttribute('shipping_address');

        return [
            'id' => $reservation->id,
            'status' => $status instanceof ReservationStatus ? $status->value : null,
            'statusLabel' => $status instanceof ReservationStatus ? $status->label() : null,
            'quantity' => $reservation->quantity,
            'depositAmount' => (float) $reservation->deposit_amount,
            'balanceAmount' => (float) $reservation->balance_amount,
            'releasesAt' => self::dateString($reservation->getAttribute('releases_at')),
            'chargedAt' => self::dateString($reservation->getAttribute('charged_at')),
            'refundedAt' => self::dateString($reservation->getAttribute('refunded_at')),
            'user' => $reservation->user ? [
                'id' => $reservation->user->id,
                'name' => $reservation->user->name,
                'email' => $reservation->user->email,
            ] : null,
            'product' => $reservation->product ? [
                'id' => $reservation->product->id,
                'slug' => $reservation->product->slug,
                'title' => $reservation->product->title,
            ] : null,
            'shippingAddress' => is_array($shippingAddress) ? $shippingAddress : null,
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
            'total' => (float) $order->total,
            'depositAmount' => (float) $order->deposit_amount,
            'balanceAmount' => (float) $order->balance_amount,
            'placedAt' => self::dateString($order->getAttribute('placed_at')),
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ] : null,
            'itemsCount' => $order->relationLoaded('items') ? $order->items->count() : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function orderDetail(Order $order): array
    {
        $shippingAddress = $order->getAttribute('shipping_address');

        return [
            ...self::order($order),
            'subtotal' => (float) $order->subtotal,
            'shippingTotal' => (float) $order->shipping_total,
            'shippingAddress' => is_array($shippingAddress) ? $shippingAddress : null,
            'items' => $order->relationLoaded('items')
                ? $order->items->map(function ($item): array {
                    $format = $item->getAttribute('format');

                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'author' => $item->author,
                        'formatLabel' => $format instanceof ProductFormat
                            ? $format->label()
                            : ProductFormat::tryFrom((string) $format)?->label(),
                        'quantity' => $item->quantity,
                        'unitPrice' => (float) $item->unit_price,
                        'lineTotal' => (float) $item->line_total,
                    ];
                })->values()->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function message(ContactMessage $message): array
    {
        return [
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'phone' => $message->phone,
            'message' => $message->message,
            'readAt' => self::dateString($message->getAttribute('read_at')),
            'createdAt' => self::dateString($message->getAttribute('created_at')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'roles' => $user->getRoleNames()->values()->all(),
            'createdAt' => self::dateString($user->getAttribute('created_at')),
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function productFormats(): array
    {
        return array_map(
            fn (ProductFormat $format): array => ['value' => $format->value, 'label' => $format->label()],
            ProductFormat::cases(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function productAvailabilities(): array
    {
        return array_map(
            fn (ProductAvailability $availability): array => ['value' => $availability->value, 'label' => $availability->label()],
            ProductAvailability::cases(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function orderStatuses(): array
    {
        return array_map(
            fn (OrderStatus $status): array => ['value' => $status->value, 'label' => $status->label()],
            OrderStatus::cases(),
        );
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function roles(): array
    {
        return [
            ...array_map(
                fn (UserRole $role): array => ['value' => $role->value, 'label' => $role->label()],
                UserRole::cases(),
            ),
            ...array_map(
                fn (ManagerRole $role): array => ['value' => $role->value, 'label' => $role->label()],
                ManagerRole::cases(),
            ),
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
}
