<?php

namespace App\Http\Controllers;

use App\Enums\ReservationStatus;
use App\Models\Order;
use App\Models\User;
use App\Support\ClientView;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $this->authenticatedUser($request);

        return Inertia::render('Dashboard', [
            'overview' => [
                'cartCount' => (int) $user->cartItems()->sum('quantity'),
                'addresses' => $user->addresses()->count(),
                'orders' => $user->orders()->count(),
                'reservations' => $user->reservations()
                    ->where('status', ReservationStatus::Deposited)
                    ->count(),
            ],
            'recentOrders' => $user->orders()
                ->with('items')
                ->latest('placed_at')
                ->limit(3)
                ->get()
                ->map(fn (Order $order): array => ClientView::order($order))
                ->values()
                ->all(),
        ]);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
