<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class CartManager
{
    public function add(User $user, Product $product, int $quantity): CartItem
    {
        $item = CartItem::query()->firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = max(1, min(($item->exists ? $item->quantity : 0) + $quantity, 99));
        $item->save();

        return $item;
    }

    public function update(User $user, CartItem $cartItem, int $quantity): void
    {
        abort_unless($cartItem->user_id === $user->id, 404);

        $cartItem->update(['quantity' => $quantity]);
    }

    public function remove(User $user, CartItem $cartItem): void
    {
        abort_unless($cartItem->user_id === $user->id, 404);

        $cartItem->delete();
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function items(User $user): Collection
    {
        return CartItem::query()
            ->where('user_id', $user->id)
            ->with('product')
            ->orderByDesc('created_at')
            ->get();
    }
}
