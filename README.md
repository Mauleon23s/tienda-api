# Tienda API

API REST para la gestión de productos, órdenes de compra y generación de
recibos.

Este proyecto fue desarrollado como prueba técnica para demostrar manejo
de:

-   Laravel
-   Docker
-   Control de stock
-   Manejo de concurrencia
-   Integración con servicios externos
-   Generación de recibos en PDF
-   Documentación de API con Swagger
-   Autenticación JWT
-   Tests automatizados

------------------------------------------------------------------------

# Tecnologías utilizadas

-   PHP 8.2
-   Laravel 11
-   MySQL
-   Docker / Docker Compose
-   JWT Authentication
-   Swagger (OpenAPI)
-   DOMPDF
-   PHPUnit

------------------------------------------------------------------------

# Requisitos

Para ejecutar el proyecto únicamente necesitas:

-   Docker
-   Docker Compose

No es necesario instalar PHP ni Composer localmente.

------------------------------------------------------------------------

# Instalación

## 1. Clonar el repositorio

``` bash
git clone https://github.com/Mauleon23s/tienda-api.git
cd tienda-api
```

## 2. Levantar los contenedores

``` bash
docker compose down (detener otras instancias opcional)
docker compose up -d --build
```

## 3. Instalar dependencias

``` bash
docker compose exec app composer install
```

## 4. Configurar variables de entorno

``` bash
cp src/.env.example src/.env
```

## 5. Generar clave de aplicación

``` bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
```

## 6. Ejecutar migraciones

``` bash
docker compose exec app php artisan migrate
```

## 7. Ejecutar seeders

``` bash
docker compose exec app php artisan db:seed
```

Usuario de prueba:

email: test@example.com\
password: test123

------------------------------------------------------------------------

# Documentación de la API (Swagger)

La documentación interactiva de la API está disponible en:

http://localhost:8000/api/documentation

Desde Swagger es posible:

-   Ver todos los endpoints
-   Revisar ejemplos de request y response
-   Probar los endpoints directamente
-   Autenticarse con JWT usando **Authorize**

------------------------------------------------------------------------

# Autenticación

Endpoint de login:

POST /api/login

Body:

``` json
{
  "email": "test@example.com",
  "password": "test123"
}
```

Enviar el token en los endpoints protegidos:

Authorization: Bearer {token}

------------------------------------------------------------------------

# Endpoints principales

## Productos

GET /api/products\
POST /api/products\
GET /api/products/{id}\
PUT /api/products/{id}\
DELETE /api/products/{id}

## Órdenes

POST /api/orders\
GET /api/orders\
GET /api/orders/{id}\
POST /api/orders/{id}/cancel

## Recibos

GET /api/receipts/{id}\
GET /api/receipts/{id}/pdf

------------------------------------------------------------------------

# Flujo de compra

1.  El cliente envía una orden con múltiples productos.
2.  Se valida disponibilidad de stock.
3.  Se bloquean filas de productos usando `lockForUpdate()`.
4.  Se descuenta el stock correspondiente.
5.  Se calcula subtotal, impuestos y total.
6.  Se procesa el pago mediante un servicio externo simulado.
7.  Se genera un recibo.
8.  Se devuelve la información de la orden.

------------------------------------------------------------------------

# Control de concurrencia

Para evitar inconsistencias en el inventario se utilizan:

-   Transacciones de base de datos (`DB::transaction()`)
-   Bloqueo de filas (`lockForUpdate()`)

Esto evita que múltiples compras simultáneas consuman el mismo stock.

------------------------------------------------------------------------

# Integración con servicio externo

El servicio `ExternalPaymentService` simula una pasarela de pago externa
que puede fallar aleatoriamente.

El sistema implementa una política de **reintentos** antes de abortar la
operación.

Si el servicio falla, la transacción completa se revierte.

------------------------------------------------------------------------

# Tests automatizados

Se incluye una suite mínima de tests para los casos críticos:

-   Creación de orden con stock suficiente
-   Error cuando el stock es insuficiente
-   Rollback de la transacción cuando falla el proveedor de pagos

Ejecutar los tests:

``` bash
docker compose exec app php artisan test
```

Ejemplo de resultado:

PASS Tests`\Feature`{=tex}`\CreateOrderTest`{=tex} ✓ can create order
with valid stock\
✓ fails when stock is insufficient\
✓ rollbacks if payment service fails

------------------------------------------------------------------------

# Autor

Daniel Sánchez

------------------------------------------------------------------------

