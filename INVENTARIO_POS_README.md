# Sistema de Inventario por Punto de Venta

## Descripción General

Este sistema permite gestionar inventario independiente para cada punto de venta (POS), además del inventario por sucursal existente. Es ideal para negocios que operan con camiones o vendedores móviles que necesitan asignar y devolver inventario de manera controlada.

## Características Implementadas

### 1. **Inventario por Punto de Venta**
- Cada punto de venta puede manejar su propio inventario de manera independiente
- Control de stock en tiempo real por POS
- Estados de stock: disponible, por agotarse, agotado
- Stock mínimo configurable por producto y POS

### 2. **Tipos de Traslados**
El sistema soporta tres tipos de traslados:

#### a) Sucursal → Punto de Venta (branch_to_pos)
- Asignación de productos desde la sucursal al punto de venta
- Ejemplo: Asignar 100 garrafones del almacén central al Camión 1

#### b) Punto de Venta → Sucursal (pos_to_branch)
- Devolución de productos no vendidos
- Ejemplo: Al finalizar el día, devolver 15 garrafones del Camión 1 al almacén

#### c) Punto de Venta → Punto de Venta (pos_to_pos)
- Traslado directo entre puntos de venta
- Ejemplo: Transferir 20 garrafones del Camión 1 al Camión 2

### 3. **Control de Stock Automático**
- Reducción automática del stock origen al realizar un traslado
- Incremento automático del stock destino
- Actualización de estados de stock (disponible/por agotarse/agotado)
- Registro de movimientos en el historial

### 4. **Restricciones y Validaciones**
- No se pueden trasladar productos sin stock suficiente
- No se pueden trasladar productos globales (disponibles en todas las sucursales)
- Solo productos con control de stock habilitado
- Validación de permisos por usuario

## Estructura de Base de Datos

### Tablas Nuevas

#### `pos_product_stock`
Almacena el inventario de cada producto por punto de venta.

```sql
- id
- business_product_id (FK)
- punto_venta_id (FK)
- stockActual (decimal)
- stockMinimo (decimal)
- estado_stock (enum: disponible, por_agotarse, agotado)
- timestamps
```

#### `pos_transfers`
Registra todos los traslados entre sucursales y puntos de venta.

```sql
- id
- business_product_id (FK)
- sucursal_origen_id (nullable FK)
- punto_venta_origen_id (nullable FK)
- sucursal_destino_id (nullable FK)
- punto_venta_destino_id (nullable FK)
- tipo_traslado (enum: branch_to_pos, pos_to_branch, pos_to_pos)
- cantidad (decimal)
- user_id (FK)
- notas (text)
- estado (enum: pendiente, completado, cancelado)
- fecha_traslado (timestamp)
- timestamps
```

### Campos Agregados

#### Tabla `business`
- `pos_inventory_enabled` (boolean): Habilita el sistema de inventario por POS para el negocio

#### Tabla `punto_ventas`
- `has_independent_inventory` (boolean): Indica si este POS maneja inventario independiente

## Modelos PHP

### Nuevos Modelos

1. **PosProductStock** (`app/Models/PosProductStock.php`)
   - Gestiona el stock de productos por punto de venta
   - Métodos: `reducirStock()`, `aumentarStock()`, `updateStockEstado()`

2. **PosTransfer** (`app/Models/PosTransfer.php`)
   - Gestiona los traslados
   - Métodos: `ejecutar()`, `ejecutarBranchToPos()`, `ejecutarPosToBranch()`, `ejecutarPosToPos()`

### Modelos Actualizados

1. **Business** - Agregado campo `pos_inventory_enabled`
2. **PuntoVenta** - Agregado campo `has_independent_inventory` y relaciones con stock
3. **BusinessProduct** - Agregados métodos para gestión de stock por POS

## Controladores

### `PosInventoryController`
Maneja la visualización y gestión del inventario por POS:
- `index()`: Dashboard principal con vista de sucursales y POS
- `show($puntoVentaId)`: Ver stock de un POS específico
- `getStock(Request)`: AJAX - Obtener stock en tiempo real
- `toggleInventory($puntoVentaId)`: Habilitar/deshabilitar inventario independiente
- `compareStock($sucursalId)`: Comparar stock entre sucursal y sus POS

### `PosTransferController`
Maneja los traslados:
- `index()`: Listar historial de traslados con filtros
- `create()`: Formulario para crear traslado
- `store(Request)`: Procesar y ejecutar traslado
- `show($id)`: Ver detalles de un traslado
- `cancel($id)`: Cancelar traslado pendiente
- `getAvailableProducts(Request)`: AJAX - Productos disponibles según origen
- `getProductStock(Request)`: AJAX - Stock disponible de un producto

## Rutas

Todas las rutas están bajo el prefijo `business/inventory/`:

```php
// Dashboard de inventario por POS
GET  /inventory/pos
GET  /inventory/pos/{puntoVentaId}
GET  /inventory/pos/{puntoVentaId}/assign
POST /inventory/pos/{puntoVentaId}/toggle
GET  /inventory/stock/get
GET  /inventory/compare/{sucursalId}

// Traslados
GET  /inventory/transfers
GET  /inventory/transfers/create
POST /inventory/transfers
GET  /inventory/transfers/{id}
POST /inventory/transfers/{id}/cancel

// AJAX endpoints
GET /inventory/transfers/products/available
GET /inventory/transfers/products/stock
```

## Vistas Blade

### Principales
- `business/inventory/pos-inventory/index.blade.php` - Dashboard principal
- `business/inventory/pos-transfers/create.blade.php` - Crear traslado
- `business/inventory/pos-transfers/index.blade.php` - Historial de traslados

### Parciales AJAX
- `layouts/partials/ajax/business/inventory/stock-table.blade.php` - Tabla de stock

## Flujo de Uso

### 1. Configuración Inicial

1. **Habilitar inventario por POS en el negocio:**
   ```php
   $business->pos_inventory_enabled = true;
   $business->save();
   ```

2. **Configurar puntos de venta con inventario independiente:**
   ```php
   $puntoVenta->has_independent_inventory = true;
   $puntoVenta->save();
   ```

### 2. Escenario de Uso: Negocio de Garrafones

**Situación Inicial:**
- Sucursal 01: 1000 garrafones en inventario
- 6 camiones (puntos de venta) sin stock asignado

**Paso 1: Asignar Inventario a los Camiones**

1. Ir a "Inventario por Punto de Venta"
2. Seleccionar "Nuevo Traslado"
3. Tipo: "Sucursal → Punto de Venta"
4. Origen: Sucursal 01
5. Destino: Camión 1
6. Producto: Garrafón de agua
7. Cantidad: 100
8. Confirmar traslado

Repetir para los 6 camiones.

**Estado Después:**
- Sucursal 01: 400 garrafones
- Camión 1-6: 100 garrafones cada uno

**Paso 2: Al Final del Día - Devolver Inventario**

Si el Camión 1 vendió 85 garrafones y le quedan 15:

1. Ir a "Nuevo Traslado"
2. Tipo: "Punto de Venta → Sucursal"
3. Origen: Camión 1
4. Destino: Sucursal 01
5. Producto: Garrafón de agua
6. Cantidad: 15
7. Confirmar traslado

**Estado Final:**
- Sucursal 01: 415 garrafones
- Camión 1: 0 garrafones

### 3. Consultar Stock en Tiempo Real

El usuario con permiso `branch_selector` puede:

1. Ver dashboard de inventario
2. Ver stock de cada sucursal
3. Ver stock de cada punto de venta
4. Comparar stock entre sucursal y sus POS
5. Ver historial completo de traslados

## Permisos

### Usuario con `branch_selector = true`
- Ver inventario en tiempo real de sucursales y POS
- Crear y gestionar traslados
- Ver historial de movimientos
- Comparar stocks

### Usuario sin `branch_selector`
- Solo puede ver y gestionar el inventario de su POS asignado

## JavaScript y AJAX

El sistema utiliza JavaScript vanilla con Axios para:

- Carga dinámica de stock en tiempo real
- Actualización de selectores de productos según origen
- Validación de stock disponible antes de traslado
- Actualización automática de contadores

Archivos principales:
- Los scripts están embebidos en las vistas usando `@push('scripts')`
- Interacción con endpoints AJAX del controlador

## Validaciones Implementadas

1. **Stock Suficiente:** No se puede trasladar más cantidad de la disponible
2. **Productos con Control:** Solo productos con `has_stock = true`
3. **No Productos Globales:** Los productos globales no se pueden trasladar
4. **Destinos Válidos:** No se puede trasladar a un POS sin inventario habilitado
5. **Permisos de Usuario:** Validación de permisos por negocio y sucursal

## Historial y Auditoría

Todos los traslados quedan registrados en:

1. **Tabla `pos_transfers`:** Registro completo del traslado
2. **Tabla `business_product_movements`:** Movimiento de inventario con tipo "traslado"

Campos auditables:
- Usuario que realizó el traslado
- Fecha y hora exacta
- Origen y destino
- Cantidad trasladada
- Notas adicionales
- Estado del traslado

## Mejoras Futuras Sugeridas

1. **Reportes PDF/Excel** de traslados por período
2. **Notificaciones automáticas** cuando un POS está por agotarse
3. **Traslados programados** para automatizar asignaciones diarias
4. **Dashboard con gráficos** de movimientos por POS
5. **App móvil** para que vendedores reporten stock en tiempo real
6. **Firma digital** para confirmar recepciones de traslados
7. **Geolocalización** para validar traslados desde ubicación del POS

## Solución de Problemas

### Error: "Stock insuficiente"
- Verificar stock disponible en origen
- Revisar si hay traslados pendientes no completados

### Error: "Producto no pertenece a este negocio"
- Verificar que el producto esté correctamente asociado al business_id

### POS no aparece en listado
- Verificar que `has_independent_inventory = true`
- Verificar que el negocio tenga `pos_inventory_enabled = true`

### Stock no se actualiza
- Verificar que el traslado tenga estado "completado"
- Revisar logs en `storage/logs/laravel.log`

## Testing

Para probar el sistema:

1. Ejecutar migraciones: `php artisan migrate`
2. Crear datos de prueba:
   ```php
   $business->pos_inventory_enabled = true;
   $business->save();
   
   $puntoVenta->has_independent_inventory = true;
   $puntoVenta->save();
   ```
3. Asignar productos con stock a una sucursal
4. Realizar traslados de prueba entre sucursal y POS
5. Verificar actualización de stock en ambos extremos

## Soporte

Para preguntas o problemas relacionados con este sistema, contactar al equipo de desarrollo.

---

**Versión:** 1.0  
**Fecha:** 12 de diciembre de 2025  
**Autor:** Sistema de Facturación Electrónica
