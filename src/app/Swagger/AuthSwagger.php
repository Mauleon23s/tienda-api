<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/login",
    summary: "Login user",
    tags: ["Auth"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "test@example.com"),
                new OA\Property(property: "password", type: "string", example: "test123")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Successful login")
    ]
)]
class AuthSwagger {}