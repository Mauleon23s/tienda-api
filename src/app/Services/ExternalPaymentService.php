<?php

namespace App\Services;

use Exception;

class ExternalPaymentService
{
    public function process(float $amount): bool
    {
        // Simula fallo aleatorio 20% del tiempo
        if (rand(1, 10) <= 2) {
            throw new Exception('Payment gateway unavailable');
        }
        return true;
    }
}