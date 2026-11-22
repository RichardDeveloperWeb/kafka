<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Support\ClientView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $this->authenticatedUser($request);

        return Inertia::render('client/Orders', [
            'orders' => $user->orders()
                ->with('items')
                ->latest('placed_at')
                ->get()
                ->map(fn (Order $order): array => ClientView::order($order))
                ->values()
                ->all(),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        $user = $this->authenticatedUser($request);

        abort_unless($order->user_id === $user->id, 404);

        $order->loadMissing('items');

        return Inertia::render('client/OrderShow', [
            'order' => ClientView::order($order),
        ]);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
