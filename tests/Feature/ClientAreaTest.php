<?php

use App\Enums\ReservationStatus;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->client = User::factory()->client()->create();
});

test('guest and users without client permissions cannot access the client area', function () {
    $this->get(route('cart.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get(route('cart.index'))
        ->assertForbidden();
});

test('dashboard exposes the client overview', function () {
    $this->actingAs($this->client)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('overview.cartCount', 0)
            ->where('overview.reservations', 0)
            ->where('overview.orders', 0)
            ->has('recentOrders', 0)
            ->etc());
});

test('client can add update view and remove cart items', function () {
    $product = Product::factory()->create([
        'price' => 250,
        'stock' => 5,
    ]);

    $this->actingAs($this->client)
        ->post(route('cart.items.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $item = $this->client->cartItems()->firstOrFail();

    expect($item->quantity)->toBe(2);

    $this->patch(route('cart.items.update', $item), ['quantity' => 3])
        ->assertRedirect(route('cart.index'));

    expect($item->refresh()->quantity)->toBe(3);

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('client/Cart')
            ->where('cart.count', 3)
            ->where('cart.items.0.lineTotal', 750)
            ->where('cart.shippingTotal', 80)
            ->where('cart.total', 830)
            ->etc());

    $this->delete(route('cart.items.destroy', $item))
        ->assertRedirect(route('cart.index'));

    expect($this->client->cartItems()->count())->toBe(0);
});

test('cart rejects unavailable products and invalid quantities', function () {
    $product = Product::factory()->create(['stock' => 2]);
    $outOfStock = Product::factory()->outOfStock()->create();

    $this->actingAs($this->client)
        ->post(route('cart.items.store', $product), ['quantity' => 0])
        ->assertSessionHasErrors('quantity');

    $this->post(route('cart.items.store', $product), ['quantity' => 3])
        ->assertSessionHasErrors('quantity');

    $this->post(route('cart.items.store', $outOfStock), ['quantity' => 1])
        ->assertSessionHasErrors('product');
});

test('client can reserve a product and cancel the reservation', function () {
    $product = Product::factory()->create(['stock' => 4]);

    $this->actingAs($this->client)
        ->post(route('cart.items.store', $product), ['quantity' => 1]);

    $this->post(route('reservations.store', $product), ['quantity' => 2])
        ->assertRedirect(route('reservations.index'));

    $reservation = $this->client->reservations()->firstOrFail();

    expect($reservation->status)->toBe(ReservationStatus::Active)
        ->and($this->client->cartItems()->count())->toBe(0);

    $this->get(route('reservations.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('client/Reservations')
            ->where('holdMinutes', 15)
            ->where('reservations.0.status', 'activa')
            ->where('reservations.0.isActive', true)
            ->etc());

    Reservation::factory()
        ->for($this->client)
        ->for($product)
        ->expired()
        ->create();

    $this->get(route('reservations.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('reservations', 2)
            ->where('reservations.1.status', 'expirada')
            ->where('reservations.1.isActive', false)
            ->etc());

    $this->delete(route('reservations.destroy', $reservation))
        ->assertRedirect(route('reservations.index'));

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Cancelled);
});

test('client can manage addresses with a single default', function () {
    $original = Address::factory()->for($this->client)->default()->create();

    $this->actingAs($this->client)
        ->get(route('addresses.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('client/Addresses')
            ->has('addresses', 1)
            ->where('addresses.0.isDefault', true)
            ->etc());

    $payload = [
        'type' => 'envio',
        'label' => 'Oficina',
        'recipient_name' => 'Omar Pérez',
        'street' => 'Calle Oriente 4',
        'exterior_number' => '12',
        'interior_number' => null,
        'neighborhood' => 'Centro',
        'city' => 'Orizaba',
        'state' => 'Veracruz',
        'postal_code' => '94300',
        'country' => 'MX',
        'phone' => '272 123 4567',
        'is_default' => true,
    ];

    $this->post(route('addresses.store'), $payload)
        ->assertRedirect(route('addresses.index'));

    expect($this->client->addresses()->where('is_default', true)->count())->toBe(1);

    $address = $this->client->addresses()->latest('id')->firstOrFail();

    expect($address->label)->toBe('Oficina')
        ->and($original->refresh()->is_default)->toBeFalse();

    $this->patch(route('addresses.update', $address), [
        ...$payload,
        'label' => 'Casa',
        'is_default' => false,
    ])->assertRedirect(route('addresses.index'));

    expect($address->refresh()->label)->toBe('Casa')
        ->and($this->client->addresses()->where('is_default', true)->count())->toBe(1);

    $this->delete(route('addresses.destroy', $address))
        ->assertRedirect(route('addresses.index'));

    expect($this->client->addresses()->count())->toBe(1);
});

test('checkout creates an order snapshot clears the cart and consumes reservations', function () {
    $product = Product::factory()->create([
        'price' => 100,
        'stock' => 5,
    ]);

    $this->actingAs($this->client)
        ->post(route('cart.items.store', $product), ['quantity' => 2]);

    $reservation = Reservation::factory()
        ->for($this->client)
        ->for($product)
        ->create(['quantity' => 1]);

    $address = Address::factory()->for($this->client)->default()->create();

    $response = $this->post(route('checkout.store'), ['address_id' => $address->id]);

    $order = $this->client->orders()->firstOrFail();

    $response->assertRedirect(route('orders.show', $order));

    expect((float) $order->subtotal)->toBe(200.0)
        ->and((float) $order->shipping_total)->toBe(80.0)
        ->and((float) $order->total)->toBe(280.0)
        ->and($order->shipping_address['city'])->toBe('Orizaba')
        ->and($order->items()->count())->toBe(1)
        ->and($this->client->cartItems()->count())->toBe(0)
        ->and($reservation->refresh()->status)->toBe(ReservationStatus::Used)
        ->and($product->refresh()->stock)->toBe(3);
});

test('checkout requires an owned address and a non empty cart', function () {
    $this->actingAs($this->client)
        ->get(route('checkout.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('client/Checkout')
            ->where('cart.count', 0)
            ->has('addresses', 0)
            ->etc());

    $this->post(route('checkout.store'), ['address_id' => 999])
        ->assertSessionHasErrors('address_id');

    $address = Address::factory()->for($this->client)->create();
    $otherAddress = Address::factory()->create();

    $this->post(route('checkout.store'), ['address_id' => $otherAddress->id])
        ->assertSessionHasErrors('address_id');

    $product = Product::factory()->create(['price' => 100, 'stock' => 2]);

    $this->post(route('cart.items.store', $product), ['quantity' => 1]);
    $this->post(route('checkout.store'), ['address_id' => $address->id])
        ->assertRedirect();

    expect($this->client->orders()->count())->toBe(1);
});

test('client only sees their own orders and cannot access other users records', function () {
    $product = Product::factory()->create(['stock' => 3]);
    $otherClient = User::factory()->client()->create();

    $mine = Order::factory()->for($this->client)->create();
    $otherOrder = Order::factory()->for($otherClient)->create();
    $otherCartItem = CartItem::factory()->for($otherClient)->for($product)->create();
    $otherAddress = Address::factory()->for($otherClient)->create();
    $otherReservation = Reservation::factory()->for($otherClient)->for($product)->create();

    $this->actingAs($this->client)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('client/Orders')
            ->has('orders', 1)
            ->where('orders.0.id', $mine->id)
            ->etc());

    $this->get(route('orders.show', $otherOrder))->assertNotFound();
    $this->patch(route('cart.items.update', $otherCartItem), ['quantity' => 2])->assertNotFound();
    $this->patch(route('addresses.update', $otherAddress), [
        'type' => 'envio',
        'recipient_name' => 'Omar Pérez',
        'street' => 'Calle Oriente 4',
        'exterior_number' => '12',
        'city' => 'Orizaba',
        'state' => 'Veracruz',
        'postal_code' => '94300',
        'country' => 'MX',
    ])->assertNotFound();
    $this->delete(route('reservations.destroy', $otherReservation))->assertNotFound();
});
