<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::prefix('v1')->group(function (): void {
    Route::post('purchases', [PurchaseController::class, 'store']);

    // Endpoints arriving in later steps:
    //   POST  /api/v1/batches/{batch}/refunds      (Step 6)
    //   GET   /api/v1/products/available           (Step 7)
    //   POST  /api/v1/orders                       (Step 8)
    //   GET   /api/v1/storage/remaining            (Step 9)
    //   GET   /api/v1/batches/profit               (Step 10)
});
