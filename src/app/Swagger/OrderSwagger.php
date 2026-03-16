<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;


#[OA\Get(
    path: "/api/orders/{id}",
    summary: "Get order details",
    tags: ["Orders"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer"),
            example: 1
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Order details",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "status", type: "string", example: "created"),
                    new OA\Property(property: "total", type: "number", format: "float", example: 450.50)
                ]
            )
        ),
        new OA\Response(response: 404, description: "Order not found")
    ]
)]

#[OA\Post(
    path: "/api/orders",
    summary: "Create order",
    description: "Creates a new order and generates a receipt",
    tags: ["Orders"],
    security: [["bearerAuth" => []]],

    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["idempotency_key", "items"],
            properties: [
                new OA\Property(property: "idempotency_key", type: "string", example: "uuid-1234-5678"),
                new OA\Property(
                    property: "items",
                    type: "array",
                    items: new OA\Items(
                        new OA\Schema(
                            required: ["product_id", "quantity"],
                            properties: [
                                new OA\Property(property: "product_id", type: "integer", example: 1),
                                new OA\Property(property: "quantity", type: "integer", example: 2)
                            ]
                        )
                    )
                )
            ]
        )
    ),

    responses: [
        new OA\Response(
            response: 201,
            description: "Order created successfully",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "order_id", type: "integer", example: 1),
                    new OA\Property(property: "receipt_number", type: "string", example: "RCPT-000001"),
                    new OA\Property(property: "total", type: "number", format: "float", example: 450.50)
                ]
            )
        ),
        new OA\Response(
            response: 200,
            description: "Duplicate order returned (Idempotency)",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "order_id", type: "integer", example: 1),
                    new OA\Property(property: "receipt_number", type: "string", example: "RCPT-000001"),
                    new OA\Property(property: "total", type: "number", format: "float", example: 450.50),
                    new OA\Property(property: "is_duplicate", type: "boolean", example: true)
                ]
            )
        ),
        new OA\Response(response: 400, description: "Invalid order or insufficient stock"),
        new OA\Response(response: 401, description: "Unauthorized"),
        new OA\Response(response: 502, description: "Payment service failure")
    ]
)]

#[OA\Post(
    path: "/api/orders/{id}/cancel",
    summary: "Cancel order",
    tags: ["Orders"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer"),
            example: 1
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Order cancelled")
    ]
)]

class OrderSwagger {}