<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Support\Permissions;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::inertia('nosotros', 'public/About')->name('about');
Route::inertia('aviso-de-privacidad', 'public/Privacy')->name('privacy');
Route::inertia('terminos-y-condiciones', 'public/Terms')->name('terms');
Route::inertia('politica-de-cookies', 'public/Cookies')->name('cookies');

Route::get('contacto', [ContactController::class, 'create'])->name('contact');
Route::post('contacto', [ContactController::class, 'store'])->name('contact.store');

Route::get('catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('catalogo/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('can:'.Permissions::CART_INDEX)
        ->get('carrito', [CartController::class, 'index'])
        ->name('cart.index');

    Route::middleware('can:'.Permissions::CART_CREATE)
        ->post('carrito/agregar/{product:slug}', [CartController::class, 'store'])
        ->name('cart.items.store');

    Route::middleware('can:'.Permissions::CART_UPDATE)
        ->patch('carrito/{cartItem}', [CartController::class, 'update'])
        ->name('cart.items.update');

    Route::middleware('can:'.Permissions::CART_DELETE)
        ->delete('carrito/{cartItem}', [CartController::class, 'destroy'])
        ->name('cart.items.destroy');

    Route::middleware('can:'.Permissions::RESERVATIONS_INDEX)
        ->get('apartados', [ReservationController::class, 'index'])
        ->name('reservations.index');

    Route::middleware('can:'.Permissions::RESERVATIONS_CREATE)
        ->post('apartados/{product:slug}', [ReservationController::class, 'store'])
        ->name('reservations.store');

    Route::middleware('can:'.Permissions::RESERVATIONS_DELETE)
        ->delete('apartados/{reservation}', [ReservationController::class, 'destroy'])
        ->name('reservations.destroy');

    Route::middleware('can:'.Permissions::ADDRESSES_INDEX)
        ->get('direcciones', [AddressController::class, 'index'])
        ->name('addresses.index');

    Route::middleware('can:'.Permissions::ADDRESSES_CREATE)
        ->get('direcciones/crear', [AddressController::class, 'create'])
        ->name('addresses.create');

    Route::middleware('can:'.Permissions::ADDRESSES_CREATE)
        ->post('direcciones', [AddressController::class, 'store'])
        ->name('addresses.store');

    Route::middleware('can:'.Permissions::ADDRESSES_UPDATE)
        ->get('direcciones/{address}/editar', [AddressController::class, 'edit'])
        ->name('addresses.edit');

    Route::middleware('can:'.Permissions::ADDRESSES_UPDATE)
        ->patch('direcciones/{address}', [AddressController::class, 'update'])
        ->name('addresses.update');

    Route::middleware('can:'.Permissions::ADDRESSES_DELETE)
        ->delete('direcciones/{address}', [AddressController::class, 'destroy'])
        ->name('addresses.destroy');

    Route::middleware('can:'.Permissions::CART_INDEX)
        ->get('finalizar-compra', [CheckoutController::class, 'create'])
        ->name('checkout.create');

    Route::middleware('can:'.Permissions::ORDERS_CREATE)
        ->post('finalizar-compra', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::middleware('can:'.Permissions::ORDERS_SHOW)
        ->get('pedidos/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::middleware('can:'.Permissions::ORDERS_SHOW)
        ->get('pedidos', [OrderController::class, 'index'])
        ->name('orders.index');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
