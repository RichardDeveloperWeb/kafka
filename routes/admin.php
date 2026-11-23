<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\UserController;
use App\Support\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('can:'.Permissions::ADMIN_INDEX)
        ->get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::middleware('can:'.Permissions::PRODUCTS_INDEX)
        ->get('productos', [ProductController::class, 'index'])
        ->name('products.index');

    Route::middleware('can:'.Permissions::PRODUCTS_CREATE)
        ->get('productos/crear', [ProductController::class, 'create'])
        ->name('products.create');

    Route::middleware('can:'.Permissions::PRODUCTS_CREATE)
        ->post('productos', [ProductController::class, 'store'])
        ->name('products.store');

    Route::middleware('can:'.Permissions::PRODUCTS_UPDATE)
        ->get('productos/{product:slug}/editar', [ProductController::class, 'edit'])
        ->name('products.edit');

    Route::middleware('can:'.Permissions::PRODUCTS_UPDATE)
        ->patch('productos/{product:slug}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::middleware('can:'.Permissions::PRODUCTS_DELETE)
        ->delete('productos/{product:slug}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    Route::middleware('can:'.Permissions::CATEGORIES_INDEX)
        ->get('categorias', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::middleware('can:'.Permissions::CATEGORIES_CREATE)
        ->get('categorias/crear', [CategoryController::class, 'create'])
        ->name('categories.create');

    Route::middleware('can:'.Permissions::CATEGORIES_CREATE)
        ->post('categorias', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::middleware('can:'.Permissions::CATEGORIES_UPDATE)
        ->get('categorias/{category:slug}/editar', [CategoryController::class, 'edit'])
        ->name('categories.edit');

    Route::middleware('can:'.Permissions::CATEGORIES_UPDATE)
        ->patch('categorias/{category:slug}', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::middleware('can:'.Permissions::CATEGORIES_DELETE)
        ->delete('categorias/{category:slug}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');

    Route::middleware('can:'.Permissions::RESERVATIONS_INDEX)
        ->get('preventas', [ReservationController::class, 'index'])
        ->name('reservations.index');

    Route::middleware('can:'.Permissions::RESERVATIONS_DELETE)
        ->delete('preventas/{reservation}', [ReservationController::class, 'destroy'])
        ->name('reservations.destroy');

    Route::middleware('can:'.Permissions::ORDERS_INDEX)
        ->get('pedidos', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::middleware('can:'.Permissions::ORDERS_SHOW)
        ->get('pedidos/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::middleware('can:'.Permissions::ORDERS_UPDATE)
        ->patch('pedidos/{order}', [OrderController::class, 'update'])
        ->name('orders.update');

    Route::middleware('can:'.Permissions::MESSAGES_INDEX)
        ->get('mensajes', [MessageController::class, 'index'])
        ->name('messages.index');

    Route::middleware('can:'.Permissions::MESSAGES_SHOW)
        ->get('mensajes/{message}', [MessageController::class, 'show'])
        ->name('messages.show');

    Route::middleware('can:'.Permissions::MESSAGES_UPDATE)
        ->patch('mensajes/{message}', [MessageController::class, 'update'])
        ->name('messages.update');

    Route::middleware('can:'.Permissions::MESSAGES_DELETE)
        ->delete('mensajes/{message}', [MessageController::class, 'destroy'])
        ->name('messages.destroy');

    Route::middleware('can:'.Permissions::USERS_INDEX)
        ->get('usuarios', [UserController::class, 'index'])
        ->name('users.index');

    Route::middleware('can:'.Permissions::USERS_CREATE)
        ->get('usuarios/crear', [UserController::class, 'create'])
        ->name('users.create');

    Route::middleware('can:'.Permissions::USERS_CREATE)
        ->post('usuarios', [UserController::class, 'store'])
        ->name('users.store');

    Route::middleware('can:'.Permissions::USERS_UPDATE)
        ->get('usuarios/{user}/editar', [UserController::class, 'edit'])
        ->name('users.edit');

    Route::middleware('can:'.Permissions::USERS_UPDATE)
        ->patch('usuarios/{user}', [UserController::class, 'update'])
        ->name('users.update');

    Route::middleware('can:'.Permissions::USERS_DELETE)
        ->delete('usuarios/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
});
