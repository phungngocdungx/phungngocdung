<?php

use App\Jobs\FetchMailJob;
use App\Models\MailAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Lên lịch chạy mỗi 15 phút
Schedule::call(function () {
    $accounts = MailAccount::all();
    foreach ($accounts as $account) {
        FetchMailJob::dispatch($account);
    }
    Log::info('Scheduled email fetch dispatched for all accounts.');
})->everyFifteenMinutes();