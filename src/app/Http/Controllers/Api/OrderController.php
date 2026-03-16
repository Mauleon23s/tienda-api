<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ExternalPaymentService;
use Illuminate\Support\Facades\DB;
use App\Models\Receipt;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return OrderResource::collection(
            Order::with('items.product', 'receipt')->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // 1. Verificar idempotencia
        $existingOrder = Order::where('idempotency_key', $request->idempotency_key)->first();
        if ($existingOrder) {
            $existingOrder->load('receipt');
            return response()->json([
                'order_id' => $existingOrder->id,
                'receipt_number' => $existingOrder->receipt?->receipt_number,
                'subtotal' => $existingOrder->receipt?->subtotal,
                'tax' => $existingOrder->receipt?->tax,
                'total' => $existingOrder->receipt?->total,
                'is_duplicate' => true
            ]);
        }

        return DB::transaction(function () use ($request) {

            $order = Order::create([
                'idempotency_key' => $request->idempotency_key,
                'status' => 'completed'
            ]);

            $subtotal = 0;

            // 2. Ordenar items por product_id para prevenir deadlocks
            $items = collect($request->items)->sortBy('product_id')->values()->all();

            foreach ($items as $item) {

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

            $paymentService = app(ExternalPaymentService::class);

            for ($i = 0; $i < 3; $i++) {

                try {

                    $paymentService->process($total);

                    break;

                }
                catch (Exception $e) {

                    if ($i === 2) {
                        abort(502, 'Payment service unavailable after retries');
                    }

                    sleep(1);
                }
            }

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
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with([
            'items',
            'receipt'
        ])->findOrFail($id);

        return new OrderResource($order);
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

    public function cancel(Order $order)
    {
        if ($order->status === 'cancelled') {
            abort(400, 'Order already cancelled');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled'
            ]);
        });

        return response()->json([
            'message' => 'Order cancelled successfully'
        ]);
    }
}
