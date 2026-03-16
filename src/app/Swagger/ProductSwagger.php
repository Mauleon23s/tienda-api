<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/products",
    summary: "List products",
    tags: ["Products"],
    responses: [
        new OA\Response(response: 200, description: "Products list")
    ]
)]

#[OA\Get(
    path: "/api/products/{id}",
    summary: "Get product",
    tags: ["Products"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Product details")
    ]
)]

#[OA\Post(
    path: "/api/products",
    summary: "Create product",
    tags: ["Products"],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name","price","stock"],
            properties: [
                new OA\Property(property:"name",type:"string",example:"Laptop"),
                new OA\Property(property:"price",type:"number",example:1200),
                new OA\Property(property:"stock",type:"integer",example:10)
            ]
        )
    ),
    responses: [
        new OA\Response(response:201,description:"Product created")
    ]
)]

#[OA\Put(
    path: "/api/products/{id}",
    summary: "Update product",
    tags: ["Products"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name:"id",
            in:"path",
            required:true,
            schema:new OA\Schema(type:"integer")
        )
    ],
    responses:[
        new OA\Response(response:200,description:"Product updated")
    ]
)]

#[OA\Delete(
    path: "/api/products/{id}",
    summary: "Delete product",
    tags: ["Products"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(
            name:"id",
            in:"path",
            required:true,
            schema:new OA\Schema(type:"integer")
        )
    ],
    responses:[
        new OA\Response(response:200,description:"Product deleted")
    ]
)]

class ProductSwagger {}