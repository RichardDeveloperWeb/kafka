<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Support\Admin\AdminView;
use App\Support\ReservationManager;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationManager $reservations) {}

    public function index(): Response
    {
        return Inertia::render('admin/reservations/Index', [
            'reservations' => Reservation::query()
                ->with([
                    'user:id,name,email',
                    'product:id,slug,title',
                ])
                ->latest()
                ->get()
                ->map(fn (Reservation $reservation): array => AdminView::reservation($reservation))
                ->values()
                ->all(),
        ]);
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->reservations->cancelAsAdmin($reservation);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La preventa se canceló y el depósito fue reembolsado.',
        ]);

        return to_route('admin.reservations.index');
    }
}
