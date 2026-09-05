<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Routes untuk Discounts & Campaigns (Public Access / User Biasa)
    Route::get('discounts/available', [\App\Http\Controllers\Api\DiscountController::class, 'available']);
    Route::get('discounts/check-code', [\App\Http\Controllers\Api\DiscountController::class, 'checkCode']);
    Route::apiResource('discounts', \App\Http\Controllers\Api\DiscountController::class)->only(['index', 'show'])->names([
        'index' => 'api.discounts.index',
        'show' => 'api.discounts.show',
    ]);

    Route::get('campaigns', [\App\Http\Controllers\Api\CampaignController::class, 'index']);
    Route::get('campaigns/{id}', [\App\Http\Controllers\Api\CampaignController::class, 'show']);

    // API Routes untuk Orders (Bisa dilakukan kasir atau user)
    Route::get('payment-channels', [\App\Http\Controllers\Api\OrderController::class, 'getPaymentChannels']);
    Route::post('orders/hitung-total', [\App\Http\Controllers\Api\OrderController::class, 'calculateTotal']);
    Route::post('orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::get('orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('orders/{id}/check-status', [\App\Http\Controllers\Api\OrderController::class, 'checkStatus']);
    Route::get('orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
    Route::delete('orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'destroy']);

    // API Routes untuk Kategori & Produk (Hanya baca untuk umum)
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class)->only(['index', 'show'])->names([
        'index' => 'api.categories.index',
        'show' => 'api.categories.show',
    ]);
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)->only(['index', 'show'])->names([
        'index' => 'api.products.index',
        'show' => 'api.products.show',
    ]);

    // API Routes untuk Analytics (Menu Terlaris)
    Route::get('menu-terlaris', [\App\Http\Controllers\Api\MenuAnalyticsController::class, 'getTopMenus']);
    Route::get('menu-terlaris/harian', [\App\Http\Controllers\Api\MenuAnalyticsController::class, 'getTopMenusDaily']);


    // AREA KHUSUS ADMIN (Ubah Master Data)
    Route::middleware('role:admin')->group(function () {
        
        // Modifikasi Master Data
        Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class)->except(['index', 'show'])->names([
            'store' => 'api.categories.store',
            'update' => 'api.categories.update',
            'destroy' => 'api.categories.destroy',
        ]);
        
        Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)->except(['index', 'show'])->names([
            'store' => 'api.products.store',
            'update' => 'api.products.update',
            'destroy' => 'api.products.destroy',
        ]);
        
        Route::apiResource('discounts', \App\Http\Controllers\Api\DiscountController::class)->except(['index', 'show'])->names([
            'store' => 'api.discounts.store',
            'update' => 'api.discounts.update',
            'destroy' => 'api.discounts.destroy',
        ]);

        // Modifikasi Akses & Persetujuan Kasir
        Route::get('users/cashiers', [\App\Http\Controllers\Api\UserController::class, 'getCashiers']);
        Route::post('users/{id}/confirm', [\App\Http\Controllers\Api\UserController::class, 'confirmCashier']);

    });

    // AREA KHUSUS ADMIN & STAFF (Laporan & Pengaturan)
    Route::middleware('role:admin,staff')->group(function () {
        // API Routes untuk Laporan Kasir
        Route::get('cashier/reports/transactions', [\App\Http\Controllers\Api\ReportController::class, 'transactions']);

        // API Routes untuk Pengaturan (Profil Toko & Perhitungan Biaya)
        Route::get('settings/store', [\App\Http\Controllers\SettingController::class, 'getShopProfile']);
        Route::put('settings/store', [\App\Http\Controllers\SettingController::class, 'updateShopProfile']);
        Route::get('settings/cost-calculation', [\App\Http\Controllers\SettingController::class, 'getCostCalculation']);
        Route::put('settings/cost-calculation', [\App\Http\Controllers\SettingController::class, 'updateCostCalculation']);
    });

});

// Xendit Webhook (tidak butuh auth sanctum)
Route::post('/xendit/webhook', [\App\Http\Controllers\Api\XenditWebhookController::class, 'handle']);
