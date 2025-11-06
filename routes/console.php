<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule notification generation (runs daily at 8 AM)
Schedule::command('notifications:generate')
    ->dailyAt('08:00')
    ->withoutOverlapping();

// Schedule notification cleanup (runs weekly on Sunday at 2 AM)
Schedule::command('notifications:generate --cleanup')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping();
