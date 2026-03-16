<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_with_valid_stock()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Laptop',
            'price' => 1000,
            'stock' => 10
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'status' => 'completed'
        ]);

        $this->assertDatabaseHas('receipts', [
            'order_id' => 1
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8
        ]);
    }

    public function test_fails_when_stock_is_insufficient()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Phone',
            'price' => 500,
            'stock' => 1
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ]);

        $response->assertStatus(400);
    }


    public function test_rollbacks_if_payment_service_fails()
    {
        $this->mock(\App\Services\ExternalPaymentService::class, function ($mock) {
            $mock->shouldReceive('process')
                ->andThrow(new \Exception('Payment failed'));
        });

        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Tablet',
            'price' => 300,
            'stock' => 5
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(502);

        $this->assertDatabaseMissing('orders', [
            'status' => 'completed'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }
}