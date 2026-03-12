<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Comandos programados que se ejecutan automáticamente.
| Para activar el scheduler, agregar al crontab del servidor:
| * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Limpiar archivos de DocSpace que no han sido accedidos en 2 días
// Se ejecuta todos los días a las 3:00 AM
Schedule::command('docspace:clean --days=2 --force')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/docspace-clean.log'));
