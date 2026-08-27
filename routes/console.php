<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan sinkronisasi order Midtrans yang pending (Setiap jam)
use Illuminate\Support\Facades\Schedule;
Schedule::command('midtrans:sync-pending')->hourly();
