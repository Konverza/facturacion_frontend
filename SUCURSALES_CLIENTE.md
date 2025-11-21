# Funcionalidad de Sucursales del Cliente

## Descripción General
Se ha implementado la funcionalidad para gestionar sucursales por cliente en el sistema de facturación electrónica. Esta característica permite a los negocios que lo requieran, agregar múltiples sucursales a sus clientes.

## Cambios Realizados

### 1. Base de Datos

#### Migración: `2025_11_21_152152_add_customer_branches_fields.php`
- **Campo `has_customer_branches`** en tabla `business`:
  - Tipo: `boolean`
  - Default: `false`
  - Descripción: Determina si el negocio tiene activada la funcionalidad de sucursales por cliente

- **Campo `use_branches`** en tabla `business_customers`:
  - Tipo: `boolean`
  - Default: `false`
  - Descripción: Indica si el cliente específico utiliza sucursales

- **Nueva tabla `business_customers_branches`**:
  - `id`: Identificador único
  - `business_customers_id`: FK a `business_customers`
  - `branch_code`: Código de la sucursal
  - `nombre`: Nombre de la sucursal
  - `departamento`: Código del departamento
  - `municipio`: Código del municipio
  - `complemento`: Dirección completa de la sucursal
  - `timestamps`: Fechas de creación y actualización

### 2. Modelos

#### `Business.php`
- Agregado `has_customer_branches` al `$fillable` y `$casts`

#### `BusinessCustomer.php`
- Agregado `use_branches` al `$fillable` y `$casts`
- Agregada relación `branches()` con `BusinessCustomersBranch`

#### `BusinessCustomersBranch.php` (Nuevo)
- Modelo creado para gestionar las sucursales del cliente
- Relación `belongsTo` con `BusinessCustomer`

### 3. Controlador

#### `CustomerContoller.php`
- **Método `store()`**: 
  - Ahora guarda el campo `use_branches`
  - Crea las sucursales asociadas cuando `use_branches` es `true`

- **Método `update()`**:
  - Actualiza el campo `use_branches`
  - Gestiona sucursales eliminadas mediante `deleted_branches[]`
  - Actualiza sucursales existentes mediante `existing_branches[]`
  - Crea nuevas sucursales mediante `branches[]`

- **Método `edit()`**:
  - Carga las sucursales del cliente con eager loading
  - Precarga los municipios para cada sucursal existente mediante `$branchesMunicipios`

### 4. Vistas

#### `create.blade.php`
- Checkbox para activar sucursales (solo visible si `$business->has_customer_branches` es `true`)
- Sección dinámica para agregar sucursales con formularios
- JavaScript para gestionar la adición y eliminación de sucursales
- Carga dinámica de municipios según el departamento seleccionado

#### `edit.blade.php`
- Similar a `create.blade.php` pero con las sucursales existentes precargadas
- Gestión de sucursales existentes vs nuevas
- Marcado de sucursales para eliminar mediante inputs hidden

### 5. JavaScript

Funcionalidad implementada en ambas vistas:
- **Toggle de sección de sucursales**: Muestra/oculta el contenedor según el checkbox
- **Agregar sucursal**: Genera dinámicamente un formulario para nueva sucursal usando `<select>` HTML nativos
- **Eliminar sucursal**: Remueve el formulario o marca para eliminación si es existente
- **Carga dinámica de municipios**: Obtiene municipios según departamento seleccionado mediante fetch a `business.get-municipios`
- **Gestión de índices**: Mantiene índices únicos para cada sucursal
- **Estados de carga**: Muestra "Cargando..." mientras se obtienen los municipios

**Nota**: Se utilizan `<select>` HTML nativos en lugar del componente `x-select` personalizado para evitar problemas de renderizado dinámico. Los selects nativos se cargan dinámicamente vía JavaScript con los estilos de Tailwind aplicados.

## Uso

### Para Activar en un Negocio
1. En la base de datos, actualizar el campo `has_customer_branches` a `true` en la tabla `business` para el negocio deseado:
   ```sql
   UPDATE business SET has_customer_branches = 1 WHERE id = [ID_DEL_NEGOCIO];
   ```

### Para Agregar Sucursales a un Cliente

#### Al Crear:
1. Marcar el checkbox "Este cliente tiene sucursales"
2. Click en "Agregar Sucursal"
3. Llenar los datos de cada sucursal:
   - Código de sucursal
   - Nombre
   - Departamento y Municipio
   - Dirección completa
4. Guardar el cliente

#### Al Editar:
1. Si el cliente ya tiene sucursales, se mostrarán automáticamente
2. Se pueden editar las existentes
3. Agregar nuevas con el botón "Agregar Sucursal"
4. Eliminar con el botón "X" en cada sucursal
5. Guardar cambios

## Estructura de Datos

### Request al Guardar (Create)
```php
[
    'use_branches' => true,
    'branches' => [
        0 => [
            'branch_code' => 'SUC001',
            'nombre' => 'Sucursal Centro',
            'departamento' => '06',
            'municipio' => '14',
            'complemento' => 'Calle Principal #123'
        ],
        // ... más sucursales
    ]
]
```

### Request al Actualizar (Update)
```php
[
    'use_branches' => true,
    'existing_branches' => [
        0 => [
            'id' => 1,
            'branch_code' => 'SUC001',
            // ... campos actualizados
        ]
    ],
    'branches' => [
        // Nuevas sucursales
    ],
    'deleted_branches' => [1, 2, 3] // IDs de sucursales a eliminar
]
```

## Notas Técnicas

- La eliminación de sucursales es en cascada (configurado en la migración)
- Las validaciones de departamento y municipio reutilizan los catálogos existentes (CAT-012, CAT-013)
- El código es compatible con el patrón existente del proyecto
- Se utiliza `DB::beginTransaction()` para garantizar integridad de datos

### 6. Visualización en Tabla de Clientes

#### Componente Livewire `Clients.php`
- Agregado campo `$business` para acceder a la configuración del negocio
- Método `mount()` carga el negocio actual desde la sesión
- Query actualizado con `->withCount('branches')` para contar sucursales eficientemente
- Variable `$business` pasada a la vista

#### Vista `clients.blade.php`
- Nueva columna "Sucursales" que solo se muestra si `$business->has_customer_branches` es `true`
- Muestra un badge azul con icono de edificio y el número de sucursales cuando `$customer->use_branches` es `true`
- Muestra un guion (-) cuando el cliente no usa sucursales
- Badge con diseño responsive y modo oscuro

## Próximas Mejoras Sugeridas

1. Agregar validación de códigos de sucursal únicos por cliente
2. Implementar selector de sucursal en el proceso de DTE
3. ✅ ~~Agregar columna de sucursal en el listado de clientes~~ (Implementado)
4. Exportar/importar clientes con sus sucursales
5. Agregar filtros por sucursal en reportes
6. Agregar vista detallada de sucursales desde el listado de clientes
