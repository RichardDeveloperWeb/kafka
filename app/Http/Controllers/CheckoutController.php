<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Address;
use App\Models\User;
use App\Support\CartManager;
use App\Support\CartView;
use App\Support\CheckoutManager;
use App\Support\ClientView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartManager $cart,
        private readonly CheckoutManager $checkout,
    ) {}

    public function create(Request $request): Response
    {
        $user = $this->authenticatedUser($request);
        $summary = CartView::summary($this->cart->items($user));

        return Inertia::render('client/Checkout', [
            'cart' => $summary,
            'addresses' => $user->addresses()
                ->orderByDesc('is_default')
                ->latest()
                ->get()
                ->map(fn (Address $address): array => ClientView::address($address))
                ->values()
                ->all(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser($request);
        $address = Address::query()->whereKey((int) $request->validated('address_id'))->firstOrFail();

        $order = $this->checkout->checkout($user, $address);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Tu pedido fue confirmado. Gracias por comprar con nosotros.',
        ]);

        return to_route('orders.show', $order);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
