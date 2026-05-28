<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\BatchRefundController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\PurchaseController;
use App\Http\Controllers\Api\V1\StorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('v1')->group(function (): void {
    Route::post('purchases', [PurchaseController::class, 'store']);
    Route::post('batches/{batch}/refunds', [BatchRefundController::class, 'store']);
    Route::get('products/available', [ProductController::class, 'available']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('storage/remaining', [StorageController::class, 'remaining']);
});
