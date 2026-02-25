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
