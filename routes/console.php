<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Generar mensualidades automáticamente el 1ro de cada mes a la medianoche ─
Schedule::command('mensualidades:generar')
    ->monthlyOn(1, '00:01')
    ->withoutOverlapping()
    ->runInBackground();

// ── Crear y activar el año académico nuevo cada 1 de enero a las 00:05 GMT-5 ─
Schedule::command('app:nuevo-anio')
    ->yearlyOn(1, 1, '00:05')
    ->timezone('America/Lima')
    ->withoutOverlapping()
    ->runInBackground();
