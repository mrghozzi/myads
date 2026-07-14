<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::call(function () {
    (new \App\Http\Controllers\SitemapController())->generate();
})->daily();

Schedule::command('myads:archive-impressions')->daily();
Schedule::command('myads:custom-ads-settle')->dailyAt('00:20');
Schedule::command('blocks:process-expired')->hourly();

// v4.4.4: Automated database and storage maintenance
Schedule::command('myads:db-cleanup --force')->dailyAt('03:00');
Schedule::command('myads:prune-storage')->dailyAt('03:30');
