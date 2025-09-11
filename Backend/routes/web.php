<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Web\Accounts\AdminController;
use App\Http\Controllers\Api\EmailSyncStatusController;
use App\Http\Controllers\ApplicationSettingsController;
use App\Http\Controllers\Web\Accounts\ManageController;

// ===============================================
//      PART 1: ROUTE CHẠY ĐẦU TIÊN 
//  ROUTE CHẠY ĐẦU TIÊN Ở DỰ ÁN VÀ ĐƯỢC ĐẠT ĐẦU TIÊN 
//  =============================================== 
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/pJM', [HomeController::class, 'pJM'])->middleware('auth')->name('pJM');
Route::get('/product', [HomeController::class, 'product'])->middleware('auth')->name('product');
Route::get('/add-product', [HomeController::class, 'addProduct'])->middleware('auth')->name('addProduct');


// ===============================================
//      PART 2: ROUTE AUTH -- GOOGLE VÀ FB
//  ROUTE TẤT CẢ PHẦN AUTH CỦA HỆ THỐNG 
//  ===============================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Routes cho Google Login
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

// Routes cho Facebook Login
Route::get('/auth/facebook', [SocialiteController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialiteController::class, 'handleFacebookCallback']);


// ===============================================
//      PART 3: QUẢN LÝ ACCOUNTS
//  ROUTE QUẢN LÝ TẤT CẢ CÁC TÀI KHỎA CỦA HỆ THỐNG 
//  ===============================================
// Route quản lý accounts System
Route::get('/tiktok', [AccountController::class, 'showAccTT'])->middleware('auth')->name('tiktok.index'); // Tik tok

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth')->name('admin.index'); // Admin
Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->middleware('auth')->name('admin.edit');
Route::post('/admin/update/{id}', [AdminController::class, 'update'])->middleware('auth')->name('admin.update');
// Route riêng để phân quyền (nếu bạn muốn modal hoặc chức năng riêng biệt cho phân quyền)
// Nếu bạn tích hợp vào modal chỉnh sửa chính, route này không cần thiết.
Route::post('/admin/assign-roles/{id}', [AdminController::class, 'assignRoles'])->middleware('auth')->name('admin.assign_roles'); // Phân quyền roles

Route::get('/manage', [ManageController::class, 'index'])->middleware('auth')->name('manage.index'); // Quản lý tài khoản

Route::get('/show', [AccountController::class, 'show'])->middleware('auth')->name('show'); // Hàm này dùng để show accounts các nề tảng


// ===============================================
//      PART 4: QUẢN LÝ CHO PHẦN ACCOUNTS GIA ĐÌNH(NHẬY CẢM)
//  ROUTE QUẢN LÝ TẤT CẢ CÁC TÀI KHỎA CỦA HỆ THỐNG 
//  ===============================================
// Nhóm Route quản lý Accounts Family
Route::prefix('accounts')->middleware('auth')->name('accounts.')->group(function () {
    // Route quản lý Accounts Family
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/create', [AccountController::class, 'create'])->name('create');
    Route::post('/', [AccountController::class, 'store'])->name('store');
    Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('edit');
    Route::put('/{account}', [AccountController::class, 'update'])->name('update');
    Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
    Route::post('/verify-global-pin', [ApplicationSettingsController::class, 'verifyGlobalPin'])->name('globalpin.verify')->middleware('auth'); 
    Route::post('/verify-global-pin2', [ApplicationSettingsController::class, 'verifyGlobalPin2'])->name('globalpin.verify2')->middleware('auth'); 
    Route::post('/platforms/store-ajax', [AccountController::class, 'storeAjax'])->name('platforms.storeAjax')->middleware('auth');
});


// ===============================================
//      PART 5: QUẢN LÝ ACCOUNTS EMAIL 
//  ROUTE EMAIL -- HỖ TRỢ LẤY MÃ OTP & MÃ XÁC THỰC TIKTOK (CÁC NỀN TẢNG KHÁC)
//  ===============================================
// Nhóm Route cho Email
Route::prefix('emails')->middleware('auth')->name('emails.')->group(function () {
    Route::get('/', [EmailController::class, 'index'])->name('index');
    Route::get('/create', [EmailController::class, 'create'])->name('create');
    Route::post('/', [EmailController::class, 'store'])->name('store');
    Route::put('/update/{id}', [EmailController::class, 'update'])->name('update');


    Route::post('/trigger-fetch-all-emails', [EmailController::class, 'fetchAllEmails'])->name('emails.triggerFetchAll'); // Nếu nút "Làm mới hộp thư" chỉ làm mới tài khoản hiện tại, có thể không cần route này cho nút đó nữa.

    Route::get('/get-mail-account/{id}', [EmailController::class, 'getMailAccountForEdit'])->name('getMailAccountForEdit'); // Route để lấy thông tin tài khoản email cho việc chỉnh sửa

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
