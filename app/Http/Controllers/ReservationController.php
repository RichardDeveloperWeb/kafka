<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use App\Support\ClientView;
use App\Support\ReservationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationManager $reservations) {}

    public function index(Request $request): Response
    {
        $user = $this->authenticatedUser($request);
        $this->reservations->expireStale($user);

        $reservations = $user->reservations()
            ->with('product')
            ->latest()
            ->get()
            ->map(fn (Reservation $reservation): array => ClientView::reservation($reservation))
            ->values()
            ->all();

        return Inertia::render('client/Reservations', [
            'reservations' => $reservations,
            'holdMinutes' => ReservationManager::HOLD_MINUTES,
        ]);
    }

    public function store(StoreReservationRequest $request, Product $product): RedirectResponse
    {
        $user = $this->authenticatedUser($request);

        $this->reservations->reserve($user, $product, (int) $request->validated('quantity'));

        $user->cartItems()->where('product_id', $product->id)->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El producto quedó apartado por 15 minutos.',
        ]);

        return to_route('reservations.index');
    }

    public function destroy(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->reservations->cancel($this->authenticatedUser($request), $reservation);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El apartado se canceló correctamente.',
        ]);

        return to_route('reservations.index');
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
