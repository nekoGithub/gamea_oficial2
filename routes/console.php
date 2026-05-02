<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ssl:check-expiration')
    ->dailyAt('09:00')
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/ssl-check.log'));


Schedule::command('uploads:clean --days=7')
    ->weeklyOn(1, '18:23') // 1 = Lunes
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/uploads-clean.log'));


// ========== VERIFICACIÓN DE INTEGRIDAD COMPLETA (DIARIA 08:00 AM + TELEGRAM) ==========
Schedule::command('integridad:verificar --tipo=all --telegram')
    ->dailyAt('08:00')
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/integridad-check.log'));


// ========== VERIFICACIÓN DE SERVIDORES (CADA 6 HORAS) ==========
Schedule::command('integridad:verificar --tipo=servidores')
    ->everySixHours()
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/integridad-servidores.log'));


// ========== VERIFICACIÓN DE BASES DE DATOS (CADA 6 HORAS) ==========
Schedule::command('integridad:verificar --tipo=bases-datos')
    ->everySixHours()
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/integridad-bd.log'));


// ========== VERIFICACIÓN DE SSL CRÍTICA (CADA 12 HORAS + TELEGRAM) ==========
Schedule::command('integridad:verificar --tipo=ssl --telegram')
    ->cron('0 */12 * * *') // cada 12 horas
    ->timezone('America/La_Paz')
    ->appendOutputTo(storage_path('logs/integridad-ssl.log'));

// Resumen de servidores 6:00 AM 
Schedule::command('servidores:resumen --hora="06:00 AM"')
    ->dailyAt('10:00')
    ->timezone('America/La_Paz');

// Resumen de servidores 12:00 PM 
Schedule::command('servidores:resumen --hora="12:00 PM"')
    ->dailyAt('16:00')
    ->timezone('America/La_Paz');
