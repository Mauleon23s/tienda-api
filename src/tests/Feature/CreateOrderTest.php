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
            'idempotency_key' => 'unique-key-1',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'idempotency_key' => 'unique-key-1',
            'status' => 'completed'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8
        ]);
    }

    public function test_order_creation_is_idempotent()
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name' => 'Pizza',
            'price' => 10,
            'stock' => 100
        ]);

        $payload = [
            'idempotency_key' => 'idempotent-key',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ];

        // First attempt
        $response1 = $this->actingAs($user, 'api')->postJson('/api/orders', $payload);
        $response1->assertStatus(201);
        $this->assertDatabaseCount('orders', 1);

        // Second attempt with same key
        $response2 = $this->actingAs($user, 'api')->postJson('/api/orders', $payload);
        $response2->assertStatus(200);
        $response2->assertJson(['is_duplicate' => true]);

        // Ensure no new order was created and stock wasn't deducted twice
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 99
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
            'idempotency_key' => 'unique-key-2',
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
        $this->mock(\App\Services\ExternalPaymentService::class , function ($mock) {
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
            'idempotency_key' => 'unique-key-3',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(502);

        $this->assertDatabaseMissing('orders', [
            'idempotency_key' => 'unique-key-3'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }
}