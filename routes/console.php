<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PROTEGE RTW scheduled commands
use Illuminate\Support\Facades\Schedule;

Schedule::command('protege:auto-flag')->dailyAt('08:00')
    ->description('Run auto-flagging business rules');

Schedule::command('protege:calculate-kpi')->monthlyOn(1, '06:00')
    ->description('Calculate monthly KPI snapshot');
