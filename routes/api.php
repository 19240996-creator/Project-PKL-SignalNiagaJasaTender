<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusinessApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [BusinessApiController::class, 'dashboard']);

    Route::get('/tenders', [BusinessApiController::class, 'tenders']);
    Route::post('/tenders', [BusinessApiController::class, 'storeTender']);
    Route::get('/tenders/{tender}', [BusinessApiController::class, 'showTender']);
    Route::put('/tenders/{tender}', [BusinessApiController::class, 'updateTender']);
    Route::delete('/tenders/{tender}', [BusinessApiController::class, 'destroyTender']);

    Route::get('/contracts', [BusinessApiController::class, 'contracts']);
    Route::post('/contracts', [BusinessApiController::class, 'storeContract']);
    Route::get('/contracts/{contract}', [BusinessApiController::class, 'showContract']);
    Route::put('/contracts/{contract}', [BusinessApiController::class, 'updateContract']);

    Route::get('/products', [BusinessApiController::class, 'products']);
    Route::post('/products', [BusinessApiController::class, 'storeProduct']);
    Route::get('/products/{product}', [BusinessApiController::class, 'showProduct']);
    Route::put('/products/{product}', [BusinessApiController::class, 'updateProduct']);

    Route::get('/sales', [BusinessApiController::class, 'sales']);
    Route::post('/sales', [BusinessApiController::class, 'storeSale']);
    Route::get('/sales/{sale}', [BusinessApiController::class, 'showSale']);

    Route::get('/clients', [BusinessApiController::class, 'clients']);
    Route::get('/suppliers', [BusinessApiController::class, 'suppliers']);
    Route::get('/procurements', [BusinessApiController::class, 'procurements']);
    Route::post('/procurements', [BusinessApiController::class, 'storeProcurement']);
    Route::get('/invoices', [BusinessApiController::class, 'invoices']);
    Route::get('/payments', [BusinessApiController::class, 'payments']);
    Route::post('/payments', [BusinessApiController::class, 'storePayment']);
});
