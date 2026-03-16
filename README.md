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

------------------------------------------------------------------------

# Tecnologías utilizadas

-   PHP 8.2
-   Laravel 11
-   MySQL
-   Docker / Docker Compose
-   JWT Authentication
-   Swagger (OpenAPI)
-   DOMPDF

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
git clone <repo-url>
cd tienda-backend
```

## 2. Levantar los contenedores

``` bash
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
-   Probar requests directamente
-   Revisar parámetros y ejemplos
-   Autenticarse con JWT

### Autenticación en Swagger

1.  Ejecutar:

POST /api/login

Body:

``` json
{
  "email": "test@example.com",
  "password": "test123"
}
```

2.  Copiar el token.

3.  Presionar **Authorize** en Swagger y usar:

Authorization: Bearer {token}

------------------------------------------------------------------------

# Autenticación

La API utiliza JWT.

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

1.  Cliente envía una orden con múltiples productos.
2.  Se valida stock disponible.
3.  Se bloquean registros con lockForUpdate().
4.  Se descuenta stock.
5.  Se calcula subtotal, impuestos y total.
6.  Se procesa pago mediante servicio externo simulado.
7.  Se genera recibo.
8.  Se devuelve la información de la orden.

------------------------------------------------------------------------

# Control de concurrencia

Se utiliza:

Product::lockForUpdate()

para evitar que múltiples compras simultáneas consuman el mismo stock.

------------------------------------------------------------------------

# Simulación de servicio externo

ExternalPaymentService simula una pasarela de pago que puede fallar
aleatoriamente.

El sistema implementa reintentos para manejar fallos temporales.

------------------------------------------------------------------------

# Generación de recibos

Después de crear una orden se genera automáticamente un recibo con:

-   número de recibo
-   subtotal
-   impuestos
-   total
-   fecha de emisión

Los recibos pueden consultarse en JSON o descargarse como PDF.

------------------------------------------------------------------------

# Autor

Daniel Sánchez

------------------------------------------------------------------------
