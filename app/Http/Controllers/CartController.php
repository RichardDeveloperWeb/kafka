<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Support\CartManager;
use App\Support\CartView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly CartManager $cart) {}

    public function index(Request $request): Response
    {
        $user = $this->authenticatedUser($request);

        return Inertia::render('client/Cart', [
            'cart' => CartView::summary($this->cart->items($user)),
        ]);
    }

    public function store(StoreCartItemRequest $request, Product $product): RedirectResponse
    {
        $this->cart->add(
            $this->authenticatedUser($request),
            $product,
            (int) $request->validated('quantity'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto se agregó a tu carrito.',
        ]);

        return to_route('cart.index');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $this->cart->update(
            $this->authenticatedUser($request),
            $cartItem,
            (int) $request->validated('quantity'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La cantidad se actualizó correctamente.',
        ]);

        return to_route('cart.index');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->cart->remove($this->authenticatedUser($request), $cartItem);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto se quitó de tu carrito.',
        ]);

        return to_route('cart.index');
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
