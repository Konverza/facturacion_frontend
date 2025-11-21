# Sucursales del Cliente en Crédito Fiscal

## Descripción General

Esta funcionalidad permite agregar información de sucursales del cliente en documentos de Crédito Fiscal (tipo 03), incluyendo los datos en el apéndice del DTE según las especificaciones de Hacienda.

## Características Implementadas

### 1. Selección de Sucursal al Emitir DTE

Cuando se selecciona un cliente que tiene habilitada la característica de sucursales (`use_branches = true`), el sistema:

- Muestra automáticamente un selector de sucursales en el formulario de "Datos del Receptor"
- Permite elegir la sucursal específica a la que se dirige el documento
- La sección solo se muestra si el cliente tiene sucursales configuradas

### 2. Campo de Orden de Compra

Se agregó un campo opcional "Número de Orden de Compra" en la sección de datos del receptor, que permite registrar el número de orden de compra asociado al crédito fiscal.

### 3. Apéndice del DTE

Los datos de sucursal y orden de compra se incluyen automáticamente en el campo `apendice` del JSON del DTE con la siguiente estructura:

```json
{
  "apendice": [
    {
      "campo": "hasBranches",
      "etiqueta": "Tiene Sucursal",
      "valor": "1"
    },
    {
      "campo": "codigoSucursal",
      "etiqueta": "Sucursal Código",
      "valor": "SUC-001"
    },
    {
      "campo": "nombreSucursal",
      "etiqueta": "Sucursal Cliente",
      "valor": "Sucursal Centro"
    },
    {
      "campo": "departamentoSucursal",
      "etiqueta": "Departamento Sucursal",
      "valor": "06"
    },
    {
      "campo": "municipioSucursal",
      "etiqueta": "Municipio Sucursal",
      "valor": "14"
    },
    {
      "campo": "complementoSucursal",
      "etiqueta": "Dirección Sucursal",
      "valor": "Calle Principal #123"
    },
    {
      "campo": "OrdenCompra",
      "etiqueta": "Número de Orden de Compra",
      "valor": "OC-2025-001"
    }
  ]
}
```

## Archivos Modificados

### Backend

1. **app/Http/Controllers/Business/CustomerContoller.php**
   - Método `show()`: Carga las sucursales del cliente usando `with('branches')` y las envía en la respuesta JSON

2. **app/Http/Controllers/Business/DTEController.php**
   - Método `processDTE()`: Guarda en sesión los datos de sucursal seleccionada y orden de compra
   - Método `buildDTE()`: Construye el apéndice con los datos de sucursal y orden de compra

### Frontend

3. **resources/views/business/dtes/comprobante_credito_fiscal.blade.php**
   - Agregado campo "Número de Orden de Compra"
   - Agregada sección oculta para selección de sucursal del cliente (se muestra dinámicamente)

4. **resources/js/dte.js**
   - Actualizado evento `selected-customer` para:
     - Detectar si el cliente tiene sucursales
     - Poblar el select de sucursales con los datos recibidos
     - Mostrar/ocultar la sección de sucursales según corresponda

## Flujo de Uso

1. **Usuario crea un Crédito Fiscal**
   - Accede a la creación de crédito fiscal

2. **Selecciona un cliente**
   - Hace clic en "Seleccionar cliente existente"
   - Elige un cliente de la lista

3. **Sistema carga datos del cliente**
   - Si el cliente tiene `use_branches = true` y tiene sucursales registradas:
     - Se muestra la sección "Sucursal del Cliente"
     - El select se puebla con las sucursales disponibles

4. **Usuario completa el formulario**
   - (Opcional) Selecciona una sucursal
   - (Opcional) Ingresa número de orden de compra
   - Completa los demás datos del DTE

5. **Usuario envía el DTE**
   - El sistema guarda en sesión:
     - `dte['customer_branch']`: Datos completos de la sucursal seleccionada
     - `dte['orden_compra']`: Número de orden de compra
   - Al construir el JSON del DTE, agrega el apéndice con los datos

6. **DTE se envía a Hacienda**
   - El campo `apendice` incluye toda la información de sucursal y orden de compra
   - Hacienda procesa el documento normalmente

## Estructura de Datos en Sesión

```php
session('dte') = [
    // ... otros campos del DTE ...
    'customer_branch' => [
        'id' => 1,
        'branch_code' => 'SUC-001',
        'nombre' => 'Sucursal Centro',
        'departamento' => '06',
        'municipio' => '14',
        'complemento' => 'Calle Principal #123'
    ],
    'orden_compra' => 'OC-2025-001'
];
```

## Consideraciones Técnicas

- El apéndice solo se incluye si hay datos para agregar (sucursal o orden de compra)
- Si el negocio no tiene `has_customer_branches = true`, no se agrega el campo "hasBranches"
- El campo "OrdenCompra" solo se incluye si se ingresó un valor
- Los datos de sucursal se validan antes de guardar en sesión (debe existir el ID)
- La sección de sucursales se oculta/muestra dinámicamente con JavaScript

## Validaciones

- ✅ La sucursal debe existir en la base de datos
- ✅ La sucursal debe pertenecer al cliente seleccionado
- ✅ El campo orden de compra es opcional (no requiere validación)
- ✅ Si no hay sucursal seleccionada, el apéndice se construye sin esos campos

## Compatibilidad

Esta funcionalidad es compatible con:
- Laravel 11
- Livewire 3
- API Octopus para envío de DTEs
- Estructura actual de sesión DTE

## Mejoras Futuras

1. Agregar esta funcionalidad a otros tipos de DTE (Factura, Nota de Crédito, etc.)
2. Permitir crear sucursales directamente desde el formulario de DTE
3. Recordar la última sucursal seleccionada por cliente
4. Validar que la orden de compra no esté duplicada
5. Agregar más campos al apéndice según necesidades del negocio
