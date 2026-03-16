<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
Route::prefix('receipts')->group(function () {
    Route::get('/{receipt}', [ReceiptController::class,'show']);
    Route::get('/{receipt}/pdf', [ReceiptController::class,'pdf']);
});