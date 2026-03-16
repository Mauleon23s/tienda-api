<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Tienda API",
    version: "1.0.0",
    description: "API para gestión de productos, órdenes y recibos"
)]

#[OA\Server(
    url: "http://localhost:8000",
    description: "Local server"
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]

class OpenApi {}