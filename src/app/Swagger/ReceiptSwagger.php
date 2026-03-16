<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/receipts/{id}",
    summary: "Get receipt",
    tags: ["Receipts"],
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
            description: "Receipt details"
        )
    ]
)]

#[OA\Get(
    path: "/api/receipts/{id}/pdf",
    summary: "Download receipt PDF",
    description: "Generates and downloads the receipt as a PDF document",
    tags: ["Receipts"],
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
            description: "PDF file"
        )
    ]
)]

class ReceiptSwagger {}