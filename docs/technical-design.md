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

## Estrategia de control de stock

Para evitar inconsistencias en el stock cuando múltiples clientes realizan compras simultáneamente, se implementó un control basado en:
Transacciones de base de datos.
Todas las operaciones de creación de órdenes se ejecutan dentro de:
DB::transaction()

Esto garantiza que:
- si el pago falla
- si ocurre un error en la operación

toda la transacción se revierte.
Además, se utiliza:
lockForUpdate() sobre los registros de productos.
Esto bloquea las filas correspondientes durante la transacción evitando que dos órdenes simultáneas consuman el mismo stock.

---

## Manejo de concurrencia

La concurrencia se controla mediante:
Row-level locking con lockForUpdate()
Esto asegura que múltiples procesos concurrentes no puedan modificar el mismo registro de producto hasta que finalice la transacción.

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