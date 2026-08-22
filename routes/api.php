<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Routes untuk Discounts & Campaigns (bisa diakses Flutter)
    Route::get('discounts/available', [\App\Http\Controllers\Api\DiscountController::class, 'available']);
    Route::get('discounts/check-code', [\App\Http\Controllers\Api\DiscountController::class, 'checkCode']);
    Route::apiResource('discounts', \App\Http\Controllers\Api\DiscountController::class)->names([
        'index' => 'api.discounts.index',
        'store' => 'api.discounts.store',
        'show' => 'api.discounts.show',
        'update' => 'api.discounts.update',
        'destroy' => 'api.discounts.destroy',
    ]);
    Route::get('campaigns', [\App\Http\Controllers\Api\CampaignController::class, 'index']);
    Route::get('campaigns/{id}', [\App\Http\Controllers\Api\CampaignController::class, 'show']);

    // API Routes untuk Orders (Checkout & Riwayat transaksi dari Flutter)
    Route::post('orders/hitung-total', [\App\Http\Controllers\Api\OrderController::class, 'calculateTotal']);
    Route::post('orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::get('orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
    Route::delete('orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'destroy']);

    // API Routes untuk Kategori & Produk (untuk ditarik & dikelola ke aplikasi kasir Flutter)
    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class)->names([
        'index' => 'api.categories.index',
        'store' => 'api.categories.store',
        'show' => 'api.categories.show',
        'update' => 'api.categories.update',
        'destroy' => 'api.categories.destroy',
    ]);
    Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)->names([
        'index' => 'api.products.index',
        'store' => 'api.products.store',
        'show' => 'api.products.show',
        'update' => 'api.products.update',
        'destroy' => 'api.products.destroy',
    ]);

    // API Routes untuk Analytics (Menu Terlaris)
    Route::get('menu-terlaris', [\App\Http\Controllers\Api\MenuAnalyticsController::class, 'getTopMenus']);
    Route::get('menu-terlaris/harian', [\App\Http\Controllers\Api\MenuAnalyticsController::class, 'getTopMenusDaily']);

    // API Routes untuk Laporan Kasir
    Route::get('cashier/reports/transactions', [\App\Http\Controllers\Api\ReportController::class, 'transactions']);

    // API Routes untuk Pengaturan (Profil Toko & Perhitungan Biaya)
    Route::get('settings/store', [\App\Http\Controllers\SettingController::class, 'getShopProfile']);
    Route::put('settings/store', [\App\Http\Controllers\SettingController::class, 'updateShopProfile']);
    Route::get('settings/cost-calculation', [\App\Http\Controllers\SettingController::class, 'getCostCalculation']);
    Route::put('settings/cost-calculation', [\App\Http\Controllers\SettingController::class, 'updateCostCalculation']);
});

// Midtrans Webhook (tidak butuh auth sanctum)
Route::post('/midtrans/webhook', [\App\Http\Controllers\Api\MidtransWebhookController::class, 'handle']);
