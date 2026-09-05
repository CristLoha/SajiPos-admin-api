<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

// Halaman Dokumentasi API untuk Frontend Developer
Route::get('/docs', function () {
    return view('pages.docs.api');
})->name('docs');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home')->middleware('role:admin,staff,user');

    // Users — hanya admin yang boleh akses kelola user
    Route::resource('users', UserController::class)->middleware('role:admin');

    // Profile — semua yang login boleh akses
    Route::get('/profile', [\App\Http\Controllers\UserController::class, 'editProfile'])->name('profile.edit')->middleware('role:admin,staff,user');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update')->middleware('role:admin,staff,user');

    // MASTER DATA (Read-Only untuk semua role termasuk Kasir)
    Route::middleware('role:admin,staff,user')->group(function () {
        Route::resource('categories', CategoryController::class)->only(['index', 'show']);
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::resource('discounts', \App\Http\Controllers\DiscountController::class)->only(['index', 'show']);
        Route::resource('campaigns', \App\Http\Controllers\CampaignController::class)->only(['index', 'show']);
    });

    // AREA KHUSUS ADMIN (Create, Edit, Delete Master Data)
    Route::middleware('role:admin')->group(function () {
        // Categories
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);

        // Products
        Route::resource('products', ProductController::class)->except(['index', 'show']);

        // Discounts
        Route::resource('discounts', \App\Http\Controllers\DiscountController::class)->except(['index', 'show']);

        // Campaigns
        Route::resource('campaigns', \App\Http\Controllers\CampaignController::class)->except(['index', 'show']);
        
        // Delete Orders
        Route::delete('orders/destroy-all', [\App\Http\Controllers\OrderController::class, 'destroyAll'])->name('orders.destroyAll');
        Route::delete('orders/bulk-delete', [\App\Http\Controllers\OrderController::class, 'bulkDelete'])->name('orders.bulkDelete');
    });

    // Orders (Read-only untuk staff, destroy untuk admin)
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show'])->middleware('role:admin,staff');
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['destroy'])->middleware('role:admin');

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
