# Documento técnico

## Modelo de datos

El sistema utiliza las siguientes entidades principales:

Products  
Almacena los productos disponibles en el catálogo.

Campos principales:
- id
- name
- price
- stock

Orders  
Representa una compra realizada por un cliente.

Campos principales:
- id
- status
- created_at

OrderItems  
Contiene las líneas de cada orden.

Campos principales:
- id
- order_id
- product_id
- quantity
- price
- subtotal

Receipts  
Representa el recibo generado tras completar una orden.

Campos principales:
- id
- order_id
- receipt_number
- subtotal
- tax
- total
- issued_at

---

## Estrategia de control de stock y Concurrencia

Para evitar inconsistencias en el stock cuando múltiples clientes realizan compras simultáneamente, se implementó un control basado en:

1.  **Transacciones de base de datos**: Todas las operaciones de creación de órdenes se ejecutan dentro de `DB::transaction()`. Esto garantiza que si el pago falla o ocurre un error, toda la operación se revierte.
2.  **Bloqueo de filas (Pessimistic Locking)**: Se utiliza `lockForUpdate()` sobre los registros de productos durante la transacción.
3.  **Prevención de Deadlocks**: El sistema ordena los productos por ID antes de aplicar los bloqueos. Esto asegura un orden consistente de adquisición de recursos, eliminando el riesgo de deadlocks circulares bajo alta concurrencia.

---

## Idempotencia

Se ha implementado una estrategia de idempotencia para manejar reintentos de red de forma segura:

-   El cliente debe enviar un encabezado o propiedad `idempotency_key` (preferiblemente un UUID).
-   Si el servidor recibe una petición con una clave que ya fue procesada exitosamente, devuelve la respuesta cacheada o los datos del pedido original con un flag `is_duplicate: true`.
-   Esto previene el cobro doble y la duplicación de pedidos ante fallos de conexión.

---

## Integración con servicios externos

El servicio de pago se simula mediante:
ExternalPaymentService
Este servicio representa una pasarela de pago externa que puede fallar aleatoriamente.
Para manejar fallos temporales se implementó una política de reintentos.
El sistema intenta procesar el pago hasta 3 veces antes de fallar la operación.
Si el servicio externo falla en todos los intentos, la transacción completa se revierte y la orden no se crea.

---

## Trade-offs

Las decisiones de diseño priorizan consistencia de datos sobre rendimiento extremo.
El uso de transacciones y locks garantiza integridad del stock pero puede introducir bloqueos breves bajo alta concurrencia.
Para sistemas de mayor escala se podrían considerar alternativas como:

- colas de procesamiento
- reservas de inventario temporales
- arquitectura basada en eventos