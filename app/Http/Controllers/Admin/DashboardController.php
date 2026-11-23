<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use App\Support\Admin\AdminView;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $counts = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'preorders' => Reservation::query()->where('status', ReservationStatus::Deposited)->count(),
            'orders' => Order::count(),
            'messages' => ContactMessage::query()->whereNull('read_at')->count(),
            'users' => User::count(),
        ];

        $recentPreorders = Reservation::query()
            ->with([
                'user:id,name,email',
                'product:id,slug,title',
            ])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Reservation $reservation): array => AdminView::reservation($reservation))
            ->values()
            ->all();

        $recentMessages = ContactMessage::query()
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (ContactMessage $message): array => AdminView::message($message))
            ->values()
            ->all();

        return Inertia::render('admin/Dashboard', [
            'counts' => $counts,
            'recentPreorders' => $recentPreorders,
            'recentMessages' => $recentMessages,
        ]);
    }
}
