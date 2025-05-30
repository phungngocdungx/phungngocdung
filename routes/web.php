<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\EmailSyncStatusController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/pJM', [HomeController::class, 'pJM'])->name('pJM');
Route::get('/product', [HomeController::class, 'product'])->name('product');
Route::get('/add-product', [HomeController::class, 'addProduct'])->name('addProduct');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
// Route gửi yêu cầu đồng bộ emails
Route::get('/trigger-fetch-emails', [EmailController::class, 'fetchAllEmails'])->name('emails.triggerfetch');
// Route cho api gửi đồng bộ emails
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route gửi đồng bộ emails
Route::get('/email-sync-status', [EmailSyncStatusController::class, 'getStatus'])->name('api.email.sync.status');
//  Route để chạy queue:work mỗi phút
Route::get('/schedule-run', function () {
    if (request('token') !== env('CRON_TOKEN')) {
        abort(403);
    }

    Artisan::call('schedule:run');
    return 'Schedule executed';
});