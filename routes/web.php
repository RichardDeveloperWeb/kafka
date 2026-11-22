<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
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
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
