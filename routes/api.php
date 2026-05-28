<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes registered here are loaded by the bootstrap/app.php `api`
| group with the `/api` URI prefix.
|
| Endpoints will be wired up in later steps:
|   POST  /api/purchases
|   POST  /api/batches/{batch}/refunds
|   GET   /api/products/available
|   POST  /api/orders
|   GET   /api/storage/remaining
|   GET   /api/batches/profit
|
*/

Route::get('/ping', fn () => ['ok' => true, 'service' => 'b2b-trading-api']);
