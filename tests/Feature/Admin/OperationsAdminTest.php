<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentTransactionType;
use App\Enums\ReservationStatus;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $this->actingAs(User::factory()->admin()->create());
});

test('admin can cancel a preorder and refund the deposit', function () {
    $client = User::factory()->client()->create();
    $reservation = Reservation::factory()->for($client)->create();

    $this->delete(route('admin.reservations.destroy', $reservation))
        ->assertRedirect(route('admin.reservations.index'));

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Refunded)
        ->and($reservation->refunded_at)->not->toBeNull()
        ->and(PaymentTransaction::query()
            ->where('reservation_id', $reservation->id)
            ->where('type', PaymentTransactionType::Refund)
            ->count())->toBe(1);

    $this->delete(route('admin.reservations.destroy', $reservation))
        ->assertSessionHasErrors('reservation');
});

test('admin cannot refund a preorder after its release date', function () {
    $reservation = Reservation::factory()->due()->create();

    $this->delete(route('admin.reservations.destroy', $reservation))
        ->assertSessionHasErrors('reservation');

    expect($reservation->refresh()->status)->toBe(ReservationStatus::Deposited);
});

test('admin can update order status but cannot reopen finalized orders', function () {
    $order = Order::factory()->create();

    $this->patch(route('admin.orders.update', $order), ['status' => OrderStatus::Shipped->value])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($order->refresh()->status)->toBe(OrderStatus::Shipped);

    $order->update(['status' => OrderStatus::Delivered]);

    $this->patch(route('admin.orders.update', $order), ['status' => OrderStatus::Preparing->value])
        ->assertSessionHasErrors('status');

    expect($order->refresh()->status)->toBe(OrderStatus::Delivered);
});

test('admin can read and delete contact messages', function () {
    $message = ContactMessage::factory()->create();

    $this->get(route('admin.messages.show', $message))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/messages/Show')
            ->where('message.id', $message->id)
            ->where('message.readAt', null)
            ->etc());

    $this->patch(route('admin.messages.update', $message), [
        'read_at' => now()->toISOString(),
    ])->assertRedirect(route('admin.messages.show', $message));

    expect($message->refresh()->read_at)->not->toBeNull();

    $this->patch(route('admin.messages.update', $message), [
        'read_at' => null,
    ])->assertRedirect(route('admin.messages.show', $message));

    expect($message->refresh()->read_at)->toBeNull();

    $this->delete(route('admin.messages.destroy', $message))
        ->assertRedirect(route('admin.messages.index'));

    expect(ContactMessage::find($message->id))->toBeNull();
});
