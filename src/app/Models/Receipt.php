<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'order_id',
        'receipt_number',
        'subtotal',
        'tax',
        'total',
        'issued_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}