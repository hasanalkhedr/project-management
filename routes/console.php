<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// VitaHR: dispatch employee account-request FCM notifications still pending
// on the shared vitahr database (admin approvals/rejections create them).
Schedule::command('vitahr:dispatch-pending-account-notifications')
    ->everyMinute()
    ->withoutOverlapping();
