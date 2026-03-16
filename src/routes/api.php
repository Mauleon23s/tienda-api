<?php

require __DIR__.'/api/auth.php';

Route::middleware('auth:api')->group(function () {
    require __DIR__.'/api/products.php';
    require __DIR__.'/api/orders.php';
    require __DIR__.'/api/receipts.php';

});