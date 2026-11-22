<?php

use App\Support\ReservationManager;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('preorders:charge-due', function (ReservationManager $reservations): void {
    $charged = $reservations->chargeDue();

    $this->info("Apartados cobrados: {$charged}.");
})->purpose('Charge due preorder balances and prepare shipments')
    ->hourly()
    ->withoutOverlapping(30);
