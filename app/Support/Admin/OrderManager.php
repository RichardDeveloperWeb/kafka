<?php

namespace App\Support\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\ValidationException;

final class OrderManager
{
    public function updateStatus(Order $order, OrderStatus $status): void
    {
        $current = $order->getAttribute('status');

        if ($current instanceof OrderStatus
            && in_array($current, [OrderStatus::Delivered, OrderStatus::Cancelled], true)
            && $current !== $status
        ) {
            throw ValidationException::withMessages([
                'status' => 'Este pedido ya está finalizado y no cambia de estado.',
            ]);
        }

        $order->update(['status' => $status]);
    }
}
