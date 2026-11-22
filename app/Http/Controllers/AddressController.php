<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Models\User;
use App\Support\AddressManager;
use App\Support\ClientView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    public function __construct(private readonly AddressManager $addresses) {}

    public function index(Request $request): Response
    {
        $user = $this->authenticatedUser($request);

        return Inertia::render('client/Addresses', [
            'addresses' => $user->addresses()
                ->orderByDesc('is_default')
                ->latest()
                ->get()
                ->map(fn (Address $address): array => ClientView::address($address))
                ->values()
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('client/AddressForm', [
            'address' => null,
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $this->addresses->create(
            $this->authenticatedUser($request),
            $request->validated(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La dirección se guardó correctamente.',
        ]);

        return to_route('addresses.index');
    }

    public function edit(Request $request, Address $address): Response
    {
        $this->authorizeOwner($this->authenticatedUser($request), $address);

        return Inertia::render('client/AddressForm', [
            'address' => ClientView::address($address),
        ]);
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $this->addresses->update(
            $this->authenticatedUser($request),
            $address,
            $request->validated(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La dirección se actualizó correctamente.',
        ]);

        return to_route('addresses.index');
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        $this->addresses->delete($this->authenticatedUser($request), $address);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'La dirección se eliminó correctamente.',
        ]);

        return to_route('addresses.index');
    }

    private function authorizeOwner(User $user, Address $address): void
    {
        abort_unless($address->user_id === $user->id, 404);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
