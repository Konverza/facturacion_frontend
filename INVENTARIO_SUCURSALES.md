# Sistema de Inventario por Sucursales

## 📋 Resumen

Este documento describe la migración del sistema de inventario de productos, que antes estaba vinculado directamente al negocio, a un nuevo sistema basado en sucursales donde cada sucursal puede tener su propio inventario.

## 🎯 Objetivos Cumplidos

✅ **1. Productos por Sucursal**: Los productos ya no están relacionados directamente al negocio, sino a cada sucursal con inventario independiente.

✅ **2. Productos Globales**: Se pueden crear productos "globales" (campo `is_global`) que están disponibles para todas las sucursales sin control de stock.

✅ **3. Filtrado en DteProduct**: La selección de productos respeta el `default_pos_id` del usuario, mostrando solo productos de esa sucursal + globales.

✅ **4. Selector de Sucursal**: Los usuarios con `branch_selector = true` pueden seleccionar de qué sucursal tomar productos.

✅ **5. Traslados entre Sucursales**: Sistema completo de traslados con creación automática de productos en sucursales destino si no existen.

✅ **6. Comando de Migración**: Comando Artisan para migrar datos existentes al nuevo sistema.

---

## 🗂️ Estructura de Datos

### Nuevas Tablas

#### `business_product_stock`
Almacena el inventario de cada producto por sucursal.

```
- id
- business_product_id (FK → business_product)
- sucursal_id (FK → sucursals)
- stockActual
- stockMinimo
- estado_stock (disponible|por_agotarse|agotado)
- timestamps
- UNIQUE(business_product_id, sucursal_id)
```

#### `branch_transfers`
Registra traslados de productos entre sucursales.

```
- id
- business_product_id (FK → business_product)
- sucursal_origen_id (FK → sucursals)
- sucursal_destino_id (FK → sucursals)
- cantidad
- user_id (FK → users)
- notas
- estado (pendiente|completado|cancelado)
- fecha_traslado
- timestamps
```

### Modificación en `business_product`

Se agregó el campo:
- `is_global` (boolean): Si es `true`, el producto está disponible para todas las sucursales sin control de stock.

---

## 🚀 Guía de Migración

### Paso 1: Ejecutar Migración de Base de Datos

```bash
php artisan migrate
```

Esto creará las tablas `business_product_stock` y `branch_transfers`, y agregará el campo `is_global` a `business_product`.

### Paso 2: Migrar Datos Existentes

**Modo prueba (sin modificar datos):**
```bash
php artisan products:migrate-to-sucursales --dry-run
```

**Migración real:**
```bash
php artisan products:migrate-to-sucursales
```

**Migrar un negocio específico:**
```bash
php artisan products:migrate-to-sucursales --business_id=1
```

Este comando:
- Toma todos los productos existentes del negocio
- Los asocia a la sucursal principal (primera sucursal encontrada)
- Crea registros en `business_product_stock` con el stock actual
- Si no existe una sucursal, crea una "Sucursal Principal" por defecto

### Paso 3: Verificar la Migración

Revisa en la base de datos:

```sql
-- Ver productos migrados por sucursal
SELECT 
    p.codigo, 
    p.descripcion, 
    s.nombre as sucursal, 
    bps.stockActual 
FROM business_product_stock bps
JOIN business_product p ON bps.business_product_id = p.id
JOIN sucursals s ON bps.sucursal_id = s.id
WHERE p.business_id = 1;
```

---

## 📖 Uso del Sistema

### Crear Producto Global

Productos que están disponibles en todas las sucursales sin control de stock:

```php
BusinessProduct::create([
    'business_id' => 1,
    'codigo' => 'SERV001',
    'descripcion' => 'Servicio de Consultoría',
    'precioUni' => 100.00,
    'has_stock' => false,
    'is_global' => true, // ← Producto global
    // ... otros campos
]);
```

### Crear Producto por Sucursal

```php
$producto = BusinessProduct::create([
    'business_id' => 1,
    'codigo' => 'PROD001',
    'descripcion' => 'Producto con inventario',
    'precioUni' => 50.00,
    'has_stock' => true,
    'is_global' => false, // ← NO es global
]);

// Asignar stock a sucursales específicas
BranchProductStock::create([
    'business_product_id' => $producto->id,
    'sucursal_id' => 1, // Sucursal Centro
    'stockActual' => 100,
    'stockMinimo' => 10,
]);

BranchProductStock::create([
    'business_product_id' => $producto->id,
    'sucursal_id' => 2, // Sucursal Norte
    'stockActual' => 50,
    'stockMinimo' => 5,
]);
```

### Consultar Stock por Sucursal

```php
$producto = BusinessProduct::find(1);

// Obtener stock de una sucursal
$stock = $producto->getAvailableStockForBranch($sucursalId);

// Verificar si hay suficiente stock
if ($producto->hasEnoughStockInBranch($sucursalId, 10)) {
    // Hay al menos 10 unidades disponibles
}
```

### Traslado entre Sucursales

```php
use App\Models\BranchTransfer;

$traslado = BranchTransfer::create([
    'business_product_id' => 1,
    'sucursal_origen_id' => 1,  // Sucursal Centro
    'sucursal_destino_id' => 2, // Sucursal Norte
    'cantidad' => 25,
    'user_id' => auth()->id(),
    'notas' => 'Reabastecimiento mensual',
    'estado' => 'pendiente',
]);

// Ejecutar traslado (reduce en origen, aumenta en destino)
try {
    $traslado->ejecutar();
    echo "Traslado completado exitosamente";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Filtrar Productos por Sucursal

```php
// Obtener todos los productos disponibles en una sucursal
// Incluye: productos globales + productos con stock en esa sucursal
$productos = BusinessProduct::where('business_id', 1)
    ->availableInBranch($sucursalId)
    ->get();
```

---

## 🖥️ Interfaz de Usuario

### DteProduct Component (Livewire)

El componente `DteProduct` ahora:

1. **Detecta automáticamente** la sucursal del usuario basándose en su `default_pos_id`
2. **Muestra un selector** de sucursales si el usuario tiene `branch_selector = true`
3. **Filtra productos** según la sucursal seleccionada (locales + globales)
4. **Muestra badges** diferenciando productos globales vs. de sucursal
5. **Muestra stock** específico de la sucursal seleccionada

```blade
<!-- Vista actualizada -->
@if ($canSelectBranch)
    <select wire:model.live="selectedSucursalId">
        <option value="">Productos Globales</option>
        @foreach ($availableSucursales as $id => $nombre)
            <option value="{{ $id }}">{{ $nombre }}</option>
        @endforeach
    </select>
@endif
```

### Controlador de Traslados

Rutas sugeridas para `routes/business.php`:

```php
Route::prefix('traslados')->group(function () {
    Route::get('/', [BranchTransferController::class, 'index'])
        ->name('business.traslados.index');
    Route::get('/create', [BranchTransferController::class, 'create'])
        ->name('business.traslados.create');
    Route::post('/', [BranchTransferController::class, 'store'])
        ->name('business.traslados.store');
    Route::get('/{id}', [BranchTransferController::class, 'show'])
        ->name('business.traslados.show');
    Route::post('/{id}/cancel', [BranchTransferController::class, 'cancel'])
        ->name('business.traslados.cancel');
    
    // API para obtener stock
    Route::post('/api/stock', [BranchTransferController::class, 'getProductStock'])
        ->name('business.traslados.api.stock');
});
```

---

## 🔄 Migración del Código Existente

### Antes (sistema antiguo)
```php
$producto = BusinessProduct::find(1);
$stockDisponible = $producto->stockActual;
```

### Después (nuevo sistema)
```php
$producto = BusinessProduct::find(1);

// Para productos con control de stock por sucursal
$stockDisponible = $producto->getAvailableStockForBranch($sucursalId);

// Para productos globales o sin stock
if ($producto->is_global || !$producto->has_stock) {
    // Sin control de stock, siempre disponible
}
```

### Actualización de Stocks en DTEController

El método `updateStocks()` fue actualizado para:
1. Recibir el `$sucursalId` como parámetro
2. Usar los métodos `reduceStockInBranch()` / `increaseStockInBranch()`
3. Crear automáticamente registros de stock si no existen

```php
// Ejemplo de uso
$this->updateStocks(
    $codGeneracion, 
    $productos, 
    $businessId, 
    'salida',  // o 'entrada'
    $sucursalId
);
```

---

## ⚠️ Consideraciones Importantes

### 1. Productos Globales
- **NO tienen** control de stock
- Están **siempre disponibles** en todas las sucursales
- Útiles para servicios, productos digitales, o items sin inventario físico

### 2. Traslados Automáticos
- Si un producto no existe en la sucursal destino, se crea automáticamente
- El stock mínimo se hereda de la sucursal origen

### 3. Validación de Stock
El sistema valida automáticamente el stock antes de procesar un DTE:
- Solo valida productos con `has_stock = true` y `is_global = false`
- Agrupa las cantidades si el mismo producto aparece varias veces
- Muestra errores claros indicando qué productos tienen stock insuficiente

### 4. Compatibilidad con Código Antiguo
Los campos `stockActual`, `stockMinimo` y `estado_stock` en `business_product` **ya no se usan** para productos con inventario por sucursal. Se mantienen por compatibilidad pero se recomienda usar `BranchProductStock`.

---

## 🧪 Testing

### Casos de Prueba Recomendados

1. **Crear producto global y verificar disponibilidad en múltiples sucursales**
2. **Crear producto con stock en sucursal A, intentar vender desde sucursal B (debe fallar)**
3. **Trasladar productos entre sucursales y verificar actualización de stocks**
4. **Generar DTE y verificar reducción de stock en sucursal correcta**
5. **Anular DTE y verificar devolución de stock a sucursal original**
6. **Usuario con `branch_selector` puede ver todas las sucursales**
7. **Usuario sin `branch_selector` solo ve productos de su sucursal por defecto**

---

## 📞 Soporte

Si encuentras problemas durante la migración:

1. Revisa los logs en `storage/logs/laravel.log`
2. Ejecuta el comando de migración en modo `--dry-run` primero
3. Verifica que todas las sucursales tengan configurado correctamente su `codSucursal`
4. Asegúrate de que los usuarios tengan `default_pos_id` configurado

---

## 📝 Checklist de Migración

- [ ] Ejecutar `php artisan migrate`
- [ ] Probar comando en modo `--dry-run`
- [ ] Ejecutar migración de datos
- [ ] Verificar productos en `business_product_stock`
- [ ] Configurar `default_pos_id` para usuarios
- [ ] Activar `branch_selector` para usuarios que lo necesiten
- [ ] Crear productos globales según sea necesario
- [ ] Agregar rutas de traslados en `routes/business.php`
- [ ] Crear vistas para gestión de traslados
- [ ] Capacitar usuarios en nuevo sistema

---

## 🎉 Resultado Final

El sistema ahora soporta:
- ✅ Inventario independiente por sucursal
- ✅ Productos globales sin control de stock
- ✅ Selección inteligente de productos según sucursal del usuario
- ✅ Traslados entre sucursales con registro completo
- ✅ Validación automática de disponibilidad
- ✅ Historial de movimientos por sucursal
- ✅ Compatibilidad con flujo DTE existente
