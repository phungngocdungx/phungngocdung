<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('queue:work --once')->everyMinute();
    })
    ->create();
