<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use DB;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $order = Order::create([
                'status' => 'completed'
            ]);

            $subtotal = 0;

            foreach ($request->items as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
            
                if ($product->stock < $item['quantity']) {
                    abort(400, "Insufficient stock for product {$product->id}");
                }
            
                $product->decrement('stock', $item['quantity']);
            
                $lineSubtotal = $product->price * $item['quantity'];
            
                $subtotal += $lineSubtotal;
            
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $lineSubtotal
                ]);
            }

            $tax = $subtotal * 0.16;
            $total = $subtotal + $tax;

            $receiptNumber = 'RCPT-' . str_pad(Receipt::count() + 1, 6, '0', STR_PAD_LEFT);

            $receipt = Receipt::create([
                'order_id' => $order->id,
                'receipt_number' => $receiptNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'issued_at' => now()
            ]);

            return response()->json([
                'order_id' => $order->id,
                'receipt_number' => $receipt->receipt_number,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total
            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
