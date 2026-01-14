# Análisis de campos en `cuerpoDocumento` por tipo de DTE

## Índice
- [Grupo 1: Documentos con estructura estándar de ventas](#grupo-1-documentos-con-estructura-estándar-de-ventas)
- [Grupo 2: Factura de Exportación](#grupo-2-factura-de-exportación)
- [Grupo 3: Sujeto Excluido](#grupo-3-sujeto-excluido)
- [Grupo 4: Comprobante de Retención](#grupo-4-comprobante-de-retención)
- [Grupo 5: Comprobante de Donación](#grupo-5-comprobante-de-donación)
- [Grupo 6: Comprobante de Liquidación](#grupo-6-comprobante-de-liquidación)
- [Grupo 7: Documento Contable de Liquidación](#grupo-7-documento-contable-de-liquidación)
- [Resumen para implementación](#resumen-para-implementación)

---

## Grupo 1: Documentos con estructura estándar de ventas
**DTEs: 01 (Factura), 03 (CCF), 05 (Nota Crédito), 06 (Nota Débito), 04 (Nota Remisión)**

### Campos comunes en todos:

| Campo | Tipo | Nullable | Min/Max Items | Observaciones |
|-------|------|----------|---------------|---------------|
| `numItem` | integer | No | min: 1, max: 2000 | Número de ítem secuencial |
| `tipoItem` | integer | No | enum: [1,2,3,4] | Tipo de ítem (4 = servicio) |
| `numeroDocumento` | string | **Varía** | maxLength: 36 | **01/03/04**: nullable, **05/06**: No nullable |
| `codigo` | string | Sí (null) | maxLength: 25 | Código del producto/servicio |
| `codTributo` | string | Sí (null) | enum específicos | Solo para tipoItem=4 |
| `descripcion` | string | **Varía** | maxLength: 1000 | **01/03/04**: No nullable, **05/06**: nullable |
| `cantidad` | number | No | exclusiveMinimum: 0 | Cantidad del producto/servicio |
| `uniMedida` | integer | No | min: 1, max: 99 | Unidad de medida |
| `precioUni` | number | No | multipleOf: 0.00000001 | Precio unitario |
| `montoDescu` | number | No | minimum: 0 | Descuento por ítem |
| `ventaNoSuj` | number | No | minimum: 0 | Ventas no sujetas |
| `ventaExenta` | number | No | minimum: 0 | Ventas exentas |
| `ventaGravada` | number | No | minimum: 0 | Ventas gravadas |
| `tributos` | array | Sí (null) | minItems: 1 | Códigos de tributos |

### Campos exclusivos por documento:

#### Factura (01) y CCF (03):
| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `psv` | number | No | Precio sugerido de venta |
| `noGravado` | number | No | Cargos/Abonos que no afectan base imponible |

#### Solo Factura (01):
| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `ivaItem` | number | No | IVA calculado por ítem |

#### Nota Remisión (04):
No tiene campos exclusivos adicionales.

### Condiciones especiales:

```javascript
// Condición 1: Si tipoItem = 4 (Servicio)
if (tipoItem === 4) {
    uniMedida = 99;  // Forzado
    codTributo = "string";  // No null
    tributos = ["20"];  // Array con solo código 20
}

// Condición 2: Si ventaGravada = 0
if (ventaGravada === 0) {
    tributos = null;  // Debe ser null
    // Solo en 01: ivaItem = 0
}

// Condición 3: Si tipoItem != 4
if (tipoItem !== 4) {
    codTributo = null;
    tributos = ["C3", "59", "71", "D1", "C5", "C6", "C7", "C8", ...];  // Lista amplia
}
```

---

## Grupo 2: Factura de Exportación
**DTE: 11 (FEX)**

### Estructura del cuerpoDocumento:

| Campo | Tipo | Nullable | Min/Max | Observaciones |
|-------|------|----------|---------|---------------|
| `numItem` | integer | No | min: 1, max: 2000 | Número de ítem |
| `codigo` | string | Sí (null) | maxLength: **200** | ⚠️ Mayor que otros DTEs (25) |
| `descripcion` | string | No | maxLength: 1000 | Descripción del producto |
| `cantidad` | number | No | exclusiveMinimum: 0 | Cantidad |
| `uniMedida` | integer | No | min: 1, max: 99 | Unidad de medida |
| `precioUni` | number | No | multipleOf: 0.00000001 | Precio unitario |
| `montoDescu` | number | No | minimum: 0 | Descuento por ítem |
| `ventaGravada` | number | No | minimum: 0 | Ventas gravadas |
| `tributos` | array | Sí (null) | items: "C3" | Solo tributo C3 permitido |
| `noGravado` | number | No | Permite negativos | Cargos/Abonos |

### Campos que NO tiene:
- `tipoItem`
- `numeroDocumento`
- `codTributo`
- `ventaNoSuj`
- `ventaExenta`

### Condición especial:
```javascript
// Si noGravado = 0
if (noGravado === 0) {
    tributos = ["C3"];  // Solo código C3
}
```

---

## Grupo 3: Sujeto Excluido
**DTE: 14 (FSE)**

### Estructura del cuerpoDocumento:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `numItem` | integer | No | min: 1, max: 2000 |
| `tipoItem` | integer | No | enum: [1,2,3] ⚠️ Sin opción 4 |
| `cantidad` | number | No | exclusiveMinimum: 0 |
| `codigo` | string | Sí (null) | maxLength: 25 |
| `uniMedida` | integer | No | enum: [57 valores específicos] |
| `descripcion` | string | No | maxLength: 1000 |
| `precioUni` | number | No | minimum: 0 |
| `montoDescu` | number | No | minimum: 0 |
| `compra` | number | No | ⚠️ Campo único - Reemplaza "ventas" |

### Campos que NO tiene:
- `numeroDocumento`
- `codTributo`
- `ventaNoSuj`
- `ventaExenta`
- `ventaGravada`
- `tributos`

### Valores permitidos de uniMedida:
```javascript
[1,2,3,4,5,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44,45,46,47,49,50,51,52,53,54,55,56,57,58,59,99]
```

---

## Grupo 4: Comprobante de Retención
**DTE: 07 (CR)**

⚠️ **Estructura completamente diferente** - Enfocado en retenciones de IVA

### Estructura del cuerpoDocumento:

| Campo | Tipo | Nullable | Min/Max | Observaciones |
|-------|------|----------|---------|---------------|
| `numItem` | integer | No | min: 1, max: **500** | Menor límite que otros |
| `tipoDte` | string | No | enum: ["14","03","01"] | Tipo de DTE relacionado |
| `tipoDoc` | integer | No | enum: [1,2] | 1=Manual, 2=Electrónico |
| `numDocumento` | string | No | Pattern variable | Depende de tipoDoc |
| `fechaEmision` | string | No | format: date | Fecha del documento original |
| `montoSujetoGrav` | number | No | minimum: 1 | Monto sujeto a retención |
| `codigoRetencionMH` | string | No | enum: ["22","C4","C9"] | Código oficial MH |
| `ivaRetenido` | number | No | minimum: 0.01 | IVA que se retiene |
| `descripcion` | string | No | maxLength: 1000 | Descripción del documento |

### Condiciones por tipoDoc:
```javascript
// tipoDoc = 1 (Manual)
if (tipoDoc === 1) {
    numDocumento = /^[A-Z0-9]{1,20}$/;  // Alfanumérico
}

// tipoDoc = 2 (Electrónico)
if (tipoDoc === 2) {
    numDocumento = /^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/;  // UUID
}
```

---

## Grupo 5: Comprobante de Donación
**DTE: 15 (CD)**

### Estructura del cuerpoDocumento:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `numItem` | integer | No | min: 1, max: 2000 |
| `tipoDonacion` | integer | No | enum: [1,2,3] (1=Dinero, 2=Bien, 3=Servicio) |
| `cantidad` | number | No | exclusiveMinimum: 0 |
| `codigo` | string | Sí (null) | maxLength: 25 |
| `uniMedida` | integer | No | enum: [57 valores] |
| `descripcion` | string | No | maxLength: 1000 |
| `depreciacion` | number | No | minimum: 0 |
| `valorUni` | number | No | minimum: 0 |
| `valor` | number | No | Valor total de la donación |

### Condiciones por tipoDonacion:
```javascript
// tipoDonacion = 1 o 3 (Dinero o Servicio)
if (tipoDonacion === 1 || tipoDonacion === 3) {
    depreciacion = 0;  // Forzado a 0
    uniMedida = 99;  // Forzado a 99
}

// tipoDonacion = 2 (Bien)
if (tipoDonacion === 2) {
    depreciacion >= 0;  // Puede tener depreciación
    uniMedida = cualquierValorPermitido;
}
```

---

## Grupo 6: Comprobante de Liquidación
**DTE: 08 (CL)**

⚠️ **Estructura especial** - Referencia a otros DTEs con totales agregados

### Estructura del cuerpoDocumento:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `numItem` | integer | No | min: 1, max: **500** |
| `tipoDte` | string | No | enum: ["01","03","05","06","11"] |
| `tipoGeneracion` | integer | No | enum: [1,2] |
| `numeroDocumento` | string | No | Pattern depende de tipoGeneracion |
| `fechaGeneracion` | string | No | format: date |
| `ventaNoSuj` | number | No | ⚠️ Permite negativos |
| `ventaExenta` | number | No | ⚠️ Permite negativos |
| `ventaGravada` | number | No | ⚠️ Permite negativos |
| `exportaciones` | number | No | ⚠️ Campo único - Permite negativos |
| `tributos` | array | Sí (null) | items: ["20","C3","59","71","D1","C8","D5","D4"] |
| `ivaItem` | number | No | ⚠️ Permite negativos |
| `obsItem` | string | No | maxLength: **3000** (más largo) |

### Condiciones:
```javascript
// tipoGeneracion = 1 (Manual)
if (tipoGeneracion === 1) {
    numeroDocumento = /^[A-Z0-9]{1,20}$/;
}

// tipoGeneracion = 2 (Electrónico)
if (tipoGeneracion === 2) {
    numeroDocumento = /^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/;
}
```

---

## Grupo 7: Documento Contable de Liquidación
**DTE: 09 (DCL)**

⚠️ **Estructura única** - No es un array, sino un **objeto único**

### Estructura del cuerpoDocumento (objeto, no array):

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `periodoLiquidacionFechaInicio` | string | No | format: date |
| `periodoLiquidacionFechaFin` | string | No | format: date |
| `codLiquidacion` | string | Sí (null) | maxLength: 30 |
| `cantidadDoc` | number | Sí (null) | Cantidad de documentos |
| `valorOperaciones` | number | No | Valor base de operaciones |
| `montoSinPercepcion` | number | No | minimum: 0 |
| `descripSinPercepcion` | string | Sí (null) | maxLength: 100 |
| `subTotal` | number | No | exclusiveMinimum: 0 |
| `iva` | number | No | IVA de las operaciones |
| `montoSujetoPercepcion` | number | No | Base para cálculo de percepción |
| `ivaPercibido` | number | No | Percepción del 2% |
| `comision` | number | No | minimum: 0 |
| `porcentComision` | string | Sí (null) | maxLength: 100 |
| `ivaComision` | number | No | minimum: 0 |
| `liquidoApagar` | number | No | Total final a pagar |
| `totalLetras` | string | No | maxLength: 200, minLength: 8 |
| `observaciones` | string | Sí (null) | maxLength: 200 |

### Cálculos:
```javascript
subTotal = valorOperaciones - montoSinPercepcion
montoSujetoPercepcion = subTotal - iva
ivaPercibido = montoSujetoPercepcion * 0.02
liquidoApagar = subTotal + iva + ivaPercibido - comision - ivaComision
```

---

## Resumen para implementación

### Campos universales (presentes en 01, 03, 04, 05, 06):

```python
CAMPOS_COMUNES_VENTAS = {
    "numItem": {"type": "integer", "nullable": False, "required": True},
    "tipoItem": {"type": "integer", "nullable": False, "required": True},
    "codigo": {"type": "string", "nullable": True, "required": True},
    "descripcion": {"type": "string", "nullable": False, "required": True},  # Excepto 05, 06
    "cantidad": {"type": "number", "nullable": False, "required": True},
    "uniMedida": {"type": "integer", "nullable": False, "required": True},
    "precioUni": {"type": "number", "nullable": False, "required": True},
    "montoDescu": {"type": "number", "nullable": False, "required": True},
    "ventaNoSuj": {"type": "number", "nullable": False, "required": True},
    "ventaExenta": {"type": "number", "nullable": False, "required": True},
    "ventaGravada": {"type": "number", "nullable": False, "required": True},
    "tributos": {"type": "array", "nullable": True, "required": True}  # null si ventaGravada = 0
}
```

### Lógica condicional para columnas por tipoDte:

```python
def get_campos_por_tipoDte(tipoDte: str) -> list:
    """
    Retorna los campos específicos según el tipo de DTE
    """
    campos = []
    
    # Grupo 1: Documentos estándar de ventas
    if tipoDte in ["01", "03", "04", "05", "06"]:
        campos = [
            "numItem", "tipoItem", "numeroDocumento", "codigo", "codTributo",
            "descripcion", "cantidad", "uniMedida", "precioUni", "montoDescu",
            "ventaNoSuj", "ventaExenta", "ventaGravada", "tributos"
        ]
        
        # Campos adicionales para Factura y CCF
        if tipoDte in ["01", "03"]:
            campos.extend(["psv", "noGravado"])
            
        # Campo adicional solo para Factura
        if tipoDte == "01":
            campos.append("ivaItem")
    
    # Factura de Exportación
    elif tipoDte == "11":
        campos = [
            "numItem", "codigo", "descripcion", "cantidad", "uniMedida",
            "precioUni", "montoDescu", "ventaGravada", "tributos", "noGravado"
        ]
    
    # Sujeto Excluido
    elif tipoDte == "14":
        campos = [
            "numItem", "tipoItem", "cantidad", "codigo", "uniMedida",
            "descripcion", "precioUni", "montoDescu", "compra"
        ]
    
    # Comprobante de Retención
    elif tipoDte == "07":
        campos = [
            "numItem", "tipoDte", "tipoDoc", "numDocumento", "fechaEmision",
            "montoSujetoGrav", "codigoRetencionMH", "ivaRetenido", "descripcion"
        ]
    
    # Comprobante de Donación
    elif tipoDte == "15":
        campos = [
            "numItem", "tipoDonacion", "cantidad", "codigo", "uniMedida",
            "descripcion", "depreciacion", "valorUni", "valor"
        ]
    
    # Comprobante de Liquidación
    elif tipoDte == "08":
        campos = [
            "numItem", "tipoDte", "tipoGeneracion", "numeroDocumento",
            "fechaGeneracion", "ventaNoSuj", "ventaExenta", "ventaGravada",
            "exportaciones", "tributos", "ivaItem", "obsItem"
        ]
    
    # Documento Contable de Liquidación
    elif tipoDte == "09":
        # ⚠️ Este no es un array sino un objeto único
        campos = [
            "periodoLiquidacionFechaInicio", "periodoLiquidacionFechaFin",
            "codLiquidacion", "cantidadDoc", "valorOperaciones",
            "montoSinPercepcion", "descripSinPercepcion", "subTotal", "iva",
            "montoSujetoPercepcion", "ivaPercibido", "comision",
            "porcentComision", "ivaComision", "liquidoApagar",
            "totalLetras", "observaciones"
        ]
    
    return campos
```

### Validaciones condicionales:

```python
def validar_campo_nullable(tipoDte: str, campo: str, valor: any) -> bool:
    """
    Valida si un campo puede ser null según el tipo de DTE
    """
    reglas_nullable = {
        "numeroDocumento": {
            "nullable_en": ["01", "03", "04"],
            "required_en": ["05", "06"]
        },
        "descripcion": {
            "nullable_en": ["05", "06"],
            "required_en": ["01", "03", "04", "11", "14", "07", "15"]
        },
        "tributos": {
            "nullable_en": ["01", "03", "04", "05", "06", "11", "08"],
            "condicion": "ventaGravada == 0"
        }
    }
    
    if campo in reglas_nullable:
        regla = reglas_nullable[campo]
        if tipoDte in regla.get("nullable_en", []):
            return True
        if tipoDte in regla.get("required_en", []):
            return valor is not None
    
    return False


def aplicar_reglas_tipoItem(item: dict) -> dict:
    """
    Aplica reglas especiales basadas en tipoItem
    """
    if item.get("tipoItem") == 4:
        # Servicio: forzar valores específicos
        item["uniMedida"] = 99
        item["codTributo"] = "A8"  # O el que corresponda
        item["tributos"] = ["20"]
    else:
        # Productos: codTributo debe ser null
        item["codTributo"] = None
    
    # Si no hay ventas gravadas, tributos debe ser null
    if item.get("ventaGravada", 0) == 0:
        item["tributos"] = None
    
    return item
```

### Ejemplo de uso en generación de tabla SQL:

```python
def generar_columnas_tabla(tipoDte: str) -> str:
    """
    Genera definición SQL para tabla dinámica según tipoDte
    """
    campos = get_campos_por_tipoDte(tipoDte)
    
    columnas_sql = []
    
    for campo in campos:
        tipo_sql = "DECIMAL(19,8)" if campo in ["precioUni", "cantidad", "ventaGravada"] else "VARCHAR(1000)"
        nullable = "NULL" if validar_campo_nullable(tipoDte, campo, None) else "NOT NULL"
        
        columnas_sql.append(f"{campo} {tipo_sql} {nullable}")
    
    return ",\n".join(columnas_sql)


# Ejemplo de uso:
print(generar_columnas_tabla("01"))  # Factura Electrónica
print(generar_columnas_tabla("14"))  # Sujeto Excluido
```

---

## Matriz de compatibilidad de campos

| Campo | 01 | 03 | 04 | 05 | 06 | 07 | 08 | 09 | 11 | 14 | 15 |
|-------|----|----|----|----|----|----|----|----|----|----|-----|
| numItem | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| tipoItem | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| numeroDocumento | ✅? | ✅? | ✅? | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| codigo | ✅? | ✅? | ✅? | ✅? | ✅? | ❌ | ❌ | ❌ | ✅? | ✅? | ✅? |
| codTributo | ✅? | ✅? | ✅? | ✅? | ✅? | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| descripcion | ✅ | ✅ | ✅ | ✅? | ✅? | ✅ | ❌ | ❌ | ✅ | ✅ | ✅ |
| cantidad | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| uniMedida | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| precioUni | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ |
| montoDescu | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ |
| ventaNoSuj | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| ventaExenta | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| ventaGravada | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ | ❌ | ❌ |
| tributos | ✅? | ✅? | ✅? | ✅? | ✅? | ❌ | ✅? | ❌ | ✅? | ❌ | ❌ |
| psv | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| noGravado | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| ivaItem | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| compra | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| tipoDonacion | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| depreciacion | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| valorUni | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| valor | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

**Leyenda:**
- ✅ = Campo presente y requerido (NOT NULL)
- ✅? = Campo presente pero nullable
- ❌ = Campo no existe en este tipo de DTE

---

## Notas importantes para implementación

1. **DTE 09 es especial**: No tiene array `cuerpoDocumento`, sino un objeto único.

2. **Valores negativos permitidos solo en DTE 08**: Todos los campos monetarios en Comprobante de Liquidación pueden ser negativos.

3. **maxLength de `codigo` varía**:
   - DTEs normales: 25 caracteres
   - DTE 11 (Exportación): **200 caracteres**

4. **maxItems varía**:
   - DTEs normales: 2000 items
   - DTEs 07, 08: **500 items**

5. **Validación de `tributos`**:
   - Si `ventaGravada > 0`: debe tener al menos 1 tributo
   - Si `ventaGravada = 0`: debe ser `null`

6. **Pattern de `numeroDocumento` condicional** (DTEs 07, 08):
   - Si `tipoDoc/tipoGeneracion = 1`: Alfanumérico manual
   - Si `tipoDoc/tipoGeneracion = 2`: UUID formato estándar
