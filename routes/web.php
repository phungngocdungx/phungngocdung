<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\EmailSyncStatusController;
use App\Http\Controllers\ApplicationSettingsController;
use App\Http\Controllers\Web\Accounts\AdminController;
use App\Http\Controllers\Web\Accounts\ManageController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/pJM', [HomeController::class, 'pJM'])->name('pJM');
Route::get('/product', [HomeController::class, 'product'])->name('product');
Route::get('/add-product', [HomeController::class, 'addProduct'])->name('addProduct');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route quản lý accounts System
Route::get('/tiktok', [AccountController::class, 'showAccTT'])->name('tiktok.index');// Tik tok

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');// Admin
Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
// Route riêng để phân quyền (nếu bạn muốn modal hoặc chức năng riêng biệt cho phân quyền)
// Nếu bạn tích hợp vào modal chỉnh sửa chính, route này không cần thiết.
Route::post('/admin/assign-roles/{id}', [AdminController::class, 'assignRoles'])->name('admin.assign_roles');

Route::get('/manage', [ManageController::class, 'index'])->name('manage.index');// Quản lý tài khoản


// Nhóm Route quản lý Accounts Family
Route::prefix('accounts')->name('accounts.')->group(function () {
    // Route quản lý Accounts Family
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/create', [AccountController::class, 'create'])->name('create');
    Route::post('/', [AccountController::class, 'store'])->name('store');
    Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('edit');
    Route::put('/{account}', [AccountController::class, 'update'])->name('update');
    Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
    Route::post('/verify-global-pin', [ApplicationSettingsController::class, 'verifyGlobalPin'])
        ->name('globalpin.verify') // Bạn có thể giữ tên route này hoặc đổi thành settings.verifyPin
        ->middleware('auth'); // Quan trọng: Đảm bảo người dùng đã đăng nhập
    Route::post('/platforms/store-ajax', [AccountController::class, 'storeAjax'])->name('platforms.storeAjax')->middleware('auth');
});

// Nhóm Route cho Email
Route::prefix('emails')->name('emails.')->group(function () {
    Route::get('/', [EmailController::class, 'index'])->name('index');
    Route::get('/create', [EmailController::class, 'create'])->name('create');
    Route::post('/', [EmailController::class, 'store'])->name('store');
    Route::put('/update/{id}', [EmailController::class, 'update'])->name('update');

    
    Route::post('/trigger-fetch-all-emails', [EmailController::class, 'fetchAllEmails'])->name('emails.triggerFetchAll');// Nếu nút "Làm mới hộp thư" chỉ làm mới tài khoản hiện tại, có thể không cần route này cho nút đó nữa.

    Route::get('/get-mail-account/{id}', [EmailController::class, 'getMailAccountForEdit'])->name('getMailAccountForEdit');// Route để lấy thông tin tài khoản email cho việc chỉnh sửa

    Route::post('/apps/email/account/{accountId}/fetch', [EmailController::class, 'fetchEmailsForSpecificAccount'])->name('emails.fetchForAccount');

    // Route cho api kiểm tra trạng thái đồng bộ (giữ nguyên)
    Route::get('/email-sync-status', [EmailSyncStatusController::class, 'getStatus'])->name('api.email.sync.status');


    // Routes cho việc tạo tài khoản email
    Route::get('/mail-accounts/create', [EmailController::class, 'create'])->name('emails.create');
    Route::post('/mail-accounts', [EmailController::class, 'store'])->name('emails.store');


    // Route cho API user (ví dụ)
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
