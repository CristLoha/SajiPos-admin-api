<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home')->middleware('role:admin,staff,user');

    // Users — hanya admin dan staff yang boleh akses
    Route::resource('users', UserController::class)->middleware('role:admin,staff');

    // Profile — semua yang login boleh akses
    Route::get('/profile', [\App\Http\Controllers\UserController::class, 'editProfile'])->name('profile.edit')->middleware('role:admin,staff,user');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update')->middleware('role:admin,staff,user');

    // Categories
    Route::resource('categories', CategoryController::class)->middleware('role:admin,staff,user');

    // Products
    Route::resource('products', ProductController::class)->middleware('role:admin,staff,user');

    // Discounts
    Route::resource('discounts', \App\Http\Controllers\DiscountController::class)->middleware('role:admin,staff,user');

    // Campaigns
    Route::resource('campaigns', \App\Http\Controllers\CampaignController::class)->middleware('role:admin,staff,user');

    // Orders
    Route::delete('orders/destroy-all', [\App\Http\Controllers\OrderController::class, 'destroyAll'])->name('orders.destroyAll')->middleware('role:admin,staff,user');
    Route::delete('orders/bulk-delete', [\App\Http\Controllers\OrderController::class, 'bulkDelete'])->name('orders.bulkDelete')->middleware('role:admin,staff,user');
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show', 'destroy'])->middleware('role:admin,staff,user');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware('role:admin,staff,user');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingWebController::class, 'index'])->name('settings.index')->middleware('role:admin');
    Route::put('/settings', [\App\Http\Controllers\SettingWebController::class, 'update'])->name('settings.update')->middleware('role:admin');
});

// Rute Publik Struk Digital (Dengan Rate Limiting: 60 request / menit)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/struk/{receiptToken}', [\App\Http\Controllers\PublicReceiptController::class, 'show'])->name('receipt.public');
    Route::get('/struk/{receiptToken}/pdf', [\App\Http\Controllers\PublicReceiptController::class, 'downloadPdf'])->name('receipt.pdf');
});
