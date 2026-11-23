<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Order;
use App\Support\Admin\AdminView;
use App\Support\Admin\OrderManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderManager $orders) {}

    public function index(): Response
    {
        return Inertia::render('admin/orders/Index', [
            'orders' => Order::query()
                ->with('user:id,name,email')
                ->withCount('items')
                ->latest('placed_at')
                ->get()
                ->map(fn (Order $order): array => AdminView::order($order))
                ->values()
                ->all(),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load([
            'user:id,name,email',
            'items',
        ]);

        return Inertia::render('admin/orders/Show', [
            'order' => AdminView::orderDetail($order),
            'statuses' => AdminView::orderStatuses(),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $this->orders->updateStatus($order, OrderStatus::from((string) $request->validated('status')));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El estado del pedido se actualizó correctamente.',
        ]);

        return to_route('admin.orders.show', $order);
    }
}
