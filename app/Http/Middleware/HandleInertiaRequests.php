<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'site' => [
                'name' => 'Los Cuentos de Kafka',
                'legalName' => 'Los Cuentos de Kafka, S. de R.L. de C.V.',
                'tagline' => 'Lecturas que te transforman',
                'address' => 'Calle Oriente 4, frente al parque principal del centro, Orizaba, Veracruz.',
            ],
            'auth' => [
                'user' => $request->user(),
            ],
            'cartCount' => (int) ($request->user()?->cartItems()->sum('quantity') ?? 0),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
