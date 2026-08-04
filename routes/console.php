<?php

use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

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
Schedule::command('myads:log-cleanup')->dailyAt('04:00');

// v4.5.1: Record the last time the scheduler actually ran, so the System
// Monitor admin page can show it. This only fires from the CLI `schedule:run`
// process (i.e. your server's cron job), never during a web request, so it
// adds zero load to normal traffic.
Event::listen(ScheduledTaskFinished::class, function (): void {
    Cache::forever('system_scheduler_last_run', now());
});
