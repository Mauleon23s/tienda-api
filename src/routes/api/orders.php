<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::post('/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('/{order}', [OrderController::class, 'show']);
    Route::get('/', [OrderController::class, 'index']);

});