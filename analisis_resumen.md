---

# Análisis de la sección `resumen` por tipo de DTE

## Índice - Resumen
- [Grupo 1: Documentos con resumen estándar de ventas](#grupo-1-resumen-documentos-con-estructura-estándar-de-ventas)
- [Grupo 2: Factura de Exportación](#grupo-2-resumen-factura-de-exportación)
- [Grupo 3: Sujeto Excluido](#grupo-3-resumen-sujeto-excluido)
- [Grupo 4: Comprobante de Retención](#grupo-4-resumen-comprobante-de-retención)
- [Grupo 5: Comprobante de Donación](#grupo-5-resumen-comprobante-de-donación)
- [Grupo 6: Comprobante de Liquidación](#grupo-6-resumen-comprobante-de-liquidación)
- [Grupo 7: Documento Contable de Liquidación](#grupo-7-resumen-documento-contable-de-liquidación)
- [Resumen para implementación - Sección resumen](#resumen-para-implementación---sección-resumen)

---

## Grupo 1 (Resumen): Documentos con estructura estándar de ventas
**DTEs: 01 (Factura), 03 (CCF), 05 (NC), 06 (ND), 04 (NR)**

### Campos comunes base en todos:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `totalNoSuj` | number | No | Total operaciones no sujetas, minimum: 0 |
| `totalExenta` | number | No | Total operaciones exentas, minimum: 0 |
| `totalGravada` | number | No | Total operaciones gravadas, minimum: 0 |
| `subTotalVentas` | number | No | Suma sin impuestos |
| `descuNoSuj` | number | No | Descuento a ventas no sujetas, minimum: 0 |
| `descuExenta` | number | No | Descuento a ventas exentas, minimum: 0 |
| `descuGravada` | number | No | Descuento a ventas gravadas, minimum: 0 |
| `totalDescu` | number | No | Total descuentos, minimum: 0 |
| `tributos` | array | Sí (null) | Array de objetos {codigo, descripcion, valor} |
| `subTotal` | number | No | Sub-total calculado |
| `ivaPerci1` | number | No | IVA Percibido (solo 03, 05, 06) |
| `ivaRete1` | number | No | IVA Retenido |
| `reteRenta` | number | No | Retención de Renta |
| `montoTotalOperacion` | number | No | Monto total (05, 06, 04) |
| `totalLetras` | string | No | Total en letras, maxLength: 200 |
| `condicionOperacion` | number | No | enum: [1,2,3] (1=Contado, 2=Crédito, 3=Otro) |

### Campos exclusivos por documento:

#### Factura (01) y CCF (03):
| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `porcentajeDescuento` | number | No | Porcentaje de descuento global, max: 100 |
| `totalNoGravado` | number | No | Cargos/abonos, permite negativos |
| `totalPagar` | number | No | Total a pagar final |
| `saldoFavor` | number | No | Saldo a favor (solo 03), maximum: 0 |
| `totalIva` | number | No | Total IVA (solo 01) |
| `pagos` | array | Sí (null) | Formas de pago |
| `numPagoElectronico` | string | Sí (null) | Número de pago electrónico |

#### Nota Débito (06):
Incluye `numPagoElectronico` (string, nullable).

#### Nota Remisión (04):
| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `porcentajeDescuento` | number | Sí (null) | Porcentaje de descuento |

**No incluye:** `ivaPerci1`, `ivaRete1`, `reteRenta`, `totalPagar`, `pagos`, `numPagoElectronico`

### Estructura del objeto `tributos`:

```json
{
  "codigo": "20",          // string, 2 chars
  "descripcion": "IVA",    // string, 2-150 chars (300 en CL)
  "valor": 13.00           // number, minimum: 0
}
```

### Estructura del objeto `pagos` (01, 03):

```json
{
  "codigo": "01",          // string, pattern: ^(0[1-9]||1[0-4]||99)$
  "montoPago": 100.00,     // number, minimum: 0
  "referencia": "REF123",  // string nullable, maxLength: 50
  "plazo": "01",           // string nullable, pattern: ^0[1-3]$ (01=Días, 02=Meses, 03=Años)
  "periodo": 30            // number nullable
}
```

### Condiciones especiales:

```javascript
// Condición 1: Si totalGravada = 0
if (totalGravada === 0) {
    ivaPerci1 = 0;  // En CCF, NC, ND
    ivaRete1 = 0;
}

// Condición 2: Si condicionOperacion = 2 (Crédito)
if (condicionOperacion === 2) {
    pagos.plazo = "string";    // Requerido (no null)
    pagos.periodo = number;    // Requerido (no null)
}

// Condición 3: Si totalPagar = 0 (solo CCF)
if (totalPagar === 0) {
    condicionOperacion = 1;  // Forzado a contado
}
```

---

## Grupo 2 (Resumen): Factura de Exportación
**DTE: 11 (FEX)**

### Estructura del resumen:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `totalGravada` | number | No | Total operaciones gravadas |
| `descuento` | number | No | Descuento global, minimum: 0 |
| `porcentajeDescuento` | number | No | Porcentaje descuento, max: 100 |
| `totalDescu` | number | No | Total descuentos |
| `seguro` | number | Sí (null) | Costo de seguro |
| `flete` | number | Sí (null) | Costo de flete |
| `montoTotalOperacion` | number | No | Monto total, exclusiveMinimum: 0 |
| `totalNoGravado` | number | No | Permite negativos |
| `totalPagar` | number | No | Total a pagar |
| `totalLetras` | string | No | Total en letras, maxLength: 200 |
| `condicionOperacion` | number | No | enum: [1,2,3] |
| `pagos` | array | Sí (null) | Formas de pago |
| `codIncoterms` | string | Sí (null) | Código INCOTERMS |
| `descIncoterms` | string | Sí (null) | Descripción INCOTERMS, maxLength: 150 |
| `numPagoElectronico` | string | Sí (null) | Número de pago electrónico |
| `observaciones` | string | Sí (null) | Observaciones, maxLength: 500 |

### Campos que NO tiene:
- `totalNoSuj`, `totalExenta`
- `descuNoSuj`, `descuExenta`, `descuGravada`
- `subTotalVentas`, `subTotal`
- `tributos`
- `ivaPerci1`, `ivaRete1`, `reteRenta`

### Estructura de `pagos`:
Misma que Grupo 1.

---

## Grupo 3 (Resumen): Sujeto Excluido
**DTE: 14 (FSE)**

### Estructura del resumen:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `totalCompra` | number | No | Total de operaciones (no "ventas") |
| `descu` | number | No | Descuento global, minimum: 0 |
| `totalDescu` | number | Sí (null) | Total descuentos |
| `subTotal` | number | No | Sub-total |
| `ivaRete1` | number | No | IVA Retenido |
| `reteRenta` | number | No | Retención Renta |
| `totalPagar` | number | No | Total a pagar |
| `totalLetras` | string | No | Total en letras |
| `condicionOperacion` | number | No | enum: [1,2,3] |
| `pagos` | array | Sí (null) | Formas de pago |
| `observaciones` | string | Sí (null) | Observaciones, maxLength: 3000 |

### Campos que NO tiene:
- `totalNoSuj`, `totalExenta`, `totalGravada`
- `descuNoSuj`, `descuExenta`, `descuGravada`
- `tributos`
- `ivaPerci1`
- `montoTotalOperacion`

### Estructura de `pagos`:
Similar al Grupo 1, con `plazo` como enum en lugar de pattern:
```json
{
  "codigo": "01",
  "montoPago": 100.00,
  "referencia": "REF123",
  "plazo": "01",        // enum: [null, "01", "02", "03"]
  "periodo": 30
}
```

### Condición especial:
```javascript
// Si condicionOperacion = 2 (Crédito)
if (condicionOperacion === 2) {
    pagos.plazo = "string";    // Requerido
    pagos.periodo = number;    // Requerido
}
```

---

## Grupo 4 (Resumen): Comprobante de Retención
**DTE: 07 (CR)**

⚠️ **Resumen muy simplificado** - Solo 3 campos

### Estructura del resumen:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `totalSujetoRetencion` | number | No | Total monto sujeto a retención |
| `totalIVAretenido` | number | No | Total IVA retenido |
| `totalIVAretenidoLetras` | string | No | Total en letras, maxLength: 200 |

### Campos que NO tiene:
Todo lo demás (ventas, descuentos, tributos, pagos, etc.)

---

## Grupo 5 (Resumen): Comprobante de Donación
**DTE: 15 (CD)**

⚠️ **Resumen simplificado** - Solo 3 campos

### Estructura del resumen:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `valorTotal` | number | No | Total de la donación, minimum: 0 |
| `totalLetras` | string | No | Total en letras, maxLength: 200 |
| `pagos` | array | Sí (null) | Formas de pago |

### Estructura de `pagos` (simplificada):
```json
{
  "codigo": "01",          // string nullable, pattern: ^(0[1-9]||1[0-4]||99)$
  "montoPago": 100.00,     // number, minimum: 0
  "referencia": "REF123"   // string nullable, maxLength: 50
}
```

**No incluye:** `plazo`, `periodo`

### Campos que NO tiene:
Ventas, descuentos, tributos, retenciones, etc.

---

## Grupo 6 (Resumen): Comprobante de Liquidación
**DTE: 08 (CL)**

⚠️ **Permite valores negativos** en todos los campos monetarios

### Estructura del resumen:

| Campo | Tipo | Nullable | Observaciones |
|-------|------|----------|---------------|
| `totalNoSuj` | number | No | Permite negativos |
| `totalExenta` | number | No | Permite negativos |
| `totalGravada` | number | No | Permite negativos |
| `totalExportacion` | number | No | Campo único, permite negativos |
| `subTotalVentas` | number | No | Permite negativos |
| `tributos` | array | Sí (null) | Códigos específicos |
| `montoTotalOperacion` | number | No | Permite negativos |
| `ivaPerci` | number | No | IVA Percibido liquidado, permite negativos |
| `total` | number | No | Total a pagar, permite negativos |
| `totalLetras` | string | No | Total en letras |
| `condicionOperacion` | number | No | enum: [1,2,3] |

### Estructura de `tributos`:
```json
{
  "codigo": "20",          // enum específico: ["20","C3","59","71","D1","C8","D5","D4"]
  "descripcion": "IVA",    // string, 2-300 chars (más largo que otros)
  "valor": 13.00           // number, permite negativos
}
```

### Condición especial:
```javascript
// Si total = 0
if (total === 0) {
    condicionOperacion = 1;  // Forzado a contado
}
```

### Campos que NO tiene:
- `descuNoSuj`, `descuExenta`, `descuGravada`
- `totalDescu`, `porcentajeDescuento`
- `ivaRete1`, `reteRenta`
- `pagos`

---

## Grupo 7 (Resumen): Documento Contable de Liquidación
**DTE: 09 (DCL)**

⚠️ **NO TIENE SECCIÓN `resumen`** - Toda la información está en `cuerpoDocumento`

---

## Resumen para implementación - Sección resumen

### Campos universales (presentes en 01, 03, 04, 05, 06):

```python
CAMPOS_RESUMEN_COMUNES = {
    "totalNoSuj": {"type": "number", "nullable": False, "min": 0},
    "totalExenta": {"type": "number", "nullable": False, "min": 0},
    "totalGravada": {"type": "number", "nullable": False, "min": 0},
    "subTotalVentas": {"type": "number", "nullable": False, "min": 0},
    "descuNoSuj": {"type": "number", "nullable": False, "min": 0},
    "descuExenta": {"type": "number", "nullable": False, "min": 0},
    "descuGravada": {"type": "number", "nullable": False, "min": 0},
    "totalDescu": {"type": "number", "nullable": False, "min": 0},
    "tributos": {"type": "array", "nullable": True},
    "subTotal": {"type": "number", "nullable": False, "min": 0},
    "montoTotalOperacion": {"type": "number", "nullable": False},
    "totalLetras": {"type": "string", "nullable": False, "maxLength": 200},
    "condicionOperacion": {"type": "number", "nullable": False, "enum": [1,2,3]}
}
```

### Lógica condicional para resumen por tipoDte:

```python
def get_campos_resumen_por_tipoDte(tipoDte: str) -> dict:
    """
    Retorna los campos de resumen según el tipo de DTE
    """
    
    # Grupo 1: Documentos estándar de ventas
    if tipoDte in ["01", "03", "05", "06", "04"]:
        campos_base = [
            "totalNoSuj", "totalExenta", "totalGravada", "subTotalVentas",
            "descuNoSuj", "descuExenta", "descuGravada", "totalDescu",
            "tributos", "subTotal", "totalLetras", "condicionOperacion"
        ]
        
        # Campos de retenciones (no en 04)
        if tipoDte != "04":
            campos_base.extend(["ivaPerci1", "ivaRete1", "reteRenta"])
        
        # Campos específicos por tipo
        if tipoDte in ["01", "03"]:
            campos_base.extend([
                "porcentajeDescuento", "totalNoGravado", "totalPagar", 
                "pagos", "numPagoElectronico"
            ])
            
            if tipoDte == "01":
                campos_base.append("totalIva")
                campos_base.append("saldoFavor")
            elif tipoDte == "03":
                campos_base.append("saldoFavor")
        
        if tipoDte in ["05", "06"]:
            campos_base.append("montoTotalOperacion")
            
        if tipoDte == "06":
            campos_base.append("numPagoElectronico")
        
        if tipoDte == "04":
            campos_base.extend(["porcentajeDescuento", "montoTotalOperacion"])
        
        return campos_base
    
    # Factura de Exportación
    elif tipoDte == "11":
        return [
            "totalGravada", "descuento", "porcentajeDescuento", "totalDescu",
            "seguro", "flete", "montoTotalOperacion", "totalNoGravado",
            "totalPagar", "totalLetras", "condicionOperacion", "pagos",
            "codIncoterms", "descIncoterms", "numPagoElectronico", "observaciones"
        ]
    
    # Sujeto Excluido
    elif tipoDte == "14":
        return [
            "totalCompra", "descu", "totalDescu", "subTotal",
            "ivaRete1", "reteRenta", "totalPagar", "totalLetras",
            "condicionOperacion", "pagos", "observaciones"
        ]
    
    # Comprobante de Retención
    elif tipoDte == "07":
        return [
            "totalSujetoRetencion", "totalIVAretenido", "totalIVAretenidoLetras"
        ]
    
    # Comprobante de Donación
    elif tipoDte == "15":
        return ["valorTotal", "totalLetras", "pagos"]
    
    # Comprobante de Liquidación
    elif tipoDte == "08":
        return [
            "totalNoSuj", "totalExenta", "totalGravada", "totalExportacion",
            "subTotalVentas", "tributos", "montoTotalOperacion", "ivaPerci",
            "total", "totalLetras", "condicionOperacion"
        ]
    
    # Documento Contable de Liquidación
    elif tipoDte == "09":
        return []  # No tiene sección resumen
    
    return []


def validar_valores_negativos(tipoDte: str, campo: str, valor: float) -> bool:
    """
    Valida si un campo puede tener valores negativos según el tipo de DTE
    """
    # Solo DTE 08 permite negativos en campos monetarios
    if tipoDte == "08":
        campos_permite_negativos = [
            "totalNoSuj", "totalExenta", "totalGravada", "totalExportacion",
            "subTotalVentas", "montoTotalOperacion", "ivaPerci", "total"
        ]
        if campo in campos_permite_negativos:
            return True
    
    # totalNoGravado permite negativos en 01, 03, 11
    if campo == "totalNoGravado" and tipoDte in ["01", "03", "11"]:
        return True
    
    return False


def validar_condicion_operacion(tipoDte: str, condicionOperacion: int, 
                                totalPagar: float = None, pagos: list = None) -> bool:
    """
    Valida condiciones especiales de operación según el tipo de DTE
    """
    # Si totalPagar = 0, condicionOperacion debe ser 1 (contado)
    if tipoDte in ["03", "08"] and totalPagar == 0:
        if condicionOperacion != 1:
            raise ValueError("totalPagar = 0 requiere condicionOperacion = 1")
    
    # Si condicionOperacion = 2 (crédito), pagos debe tener plazo y periodo
    if condicionOperacion == 2:
        if pagos:
            for pago in pagos:
                if pago.get("plazo") is None or pago.get("periodo") is None:
                    raise ValueError("condicionOperacion = 2 requiere plazo y periodo en pagos")
    
    return True


def validar_tributos_totalGravada(totalGravada: float, ivaPerci1: float, 
                                  ivaRete1: float, tipoDte: str) -> bool:
    """
    Valida que si totalGravada = 0, los IVAs también sean 0
    """
    if totalGravada == 0 and tipoDte in ["03", "05", "06"]:
        if ivaPerci1 != 0 or ivaRete1 != 0:
            raise ValueError("totalGravada = 0 requiere ivaPerci1 = 0 e ivaRete1 = 0")
    
    return True
```

### Ejemplo de uso en generación de modelo:

```python
from pydantic import BaseModel, Field, validator
from typing import Optional, List

class Pago(BaseModel):
    codigo: str = Field(..., pattern=r"^(0[1-9]||1[0-4]||99)$")
    montoPago: float = Field(..., ge=0)
    referencia: Optional[str] = Field(None, max_length=50)
    plazo: Optional[str] = Field(None, pattern=r"^0[1-3]$")
    periodo: Optional[int] = None

class Tributo(BaseModel):
    codigo: str = Field(..., min_length=2, max_length=2)
    descripcion: str = Field(..., min_length=2, max_length=150)
    valor: float = Field(..., ge=0)

class ResumenFacturaElectronica(BaseModel):  # DTE 01
    totalNoSuj: float = Field(..., ge=0)
    totalExenta: float = Field(..., ge=0)
    totalGravada: float = Field(..., ge=0)
    subTotalVentas: float = Field(..., ge=0)
    descuNoSuj: float = Field(..., ge=0)
    descuExenta: float = Field(..., ge=0)
    descuGravada: float = Field(..., ge=0)
    porcentajeDescuento: float = Field(..., ge=0, le=100)
    totalDescu: float = Field(..., ge=0)
    tributos: Optional[List[Tributo]] = None
    subTotal: float = Field(..., ge=0)
    ivaRete1: float = Field(..., ge=0)
    reteRenta: float = Field(..., ge=0)
    montoTotalOperacion: float = Field(..., gt=0)
    totalNoGravado: float  # Permite negativos
    totalPagar: float = Field(..., ge=0)
    totalLetras: str = Field(..., max_length=200)
    totalIva: float = Field(..., ge=0)
    saldoFavor: float = Field(..., le=0)
    condicionOperacion: int = Field(..., ge=1, le=3)
    pagos: Optional[List[Pago]] = None
    numPagoElectronico: Optional[str] = Field(None, max_length=100)
    
    @validator("ivaRete1")
    def validar_iva_con_totalGravada(cls, v, values):
        if values.get("totalGravada", 0) == 0 and v != 0:
            raise ValueError("ivaRete1 debe ser 0 si totalGravada = 0")
        return v

class ResumenCreditoFiscal(BaseModel):  # DTE 03
    # Similar al anterior pero con ivaPerci1 y sin totalIva
    # ... campos ...
    ivaPerci1: float = Field(..., ge=0)
    # ...


class ResumenExportacion(BaseModel):  # DTE 11
    totalGravada: float = Field(..., ge=0)
    descuento: float = Field(..., ge=0)
    porcentajeDescuento: float = Field(..., ge=0, le=100)
    totalDescu: float = Field(..., ge=0)
    seguro: Optional[float] = Field(None, ge=0)
    flete: Optional[float] = Field(None, ge=0)
    montoTotalOperacion: float = Field(..., gt=0)
    totalNoGravado: float  # Permite negativos
    totalPagar: float = Field(..., ge=0)
    totalLetras: str = Field(..., max_length=200)
    condicionOperacion: int = Field(..., ge=1, le=3)
    pagos: Optional[List[Pago]] = None
    codIncoterms: Optional[str] = None
    descIncoterms: Optional[str] = Field(None, max_length=150)
    numPagoElectronico: Optional[str] = Field(None, max_length=100)
    observaciones: Optional[str] = Field(None, max_length=500)
```

---

## Matriz de compatibilidad de campos - Resumen

| Campo | 01 | 03 | 04 | 05 | 06 | 07 | 08 | 09 | 11 | 14 | 15 |
|-------|----|----|----|----|----|----|----|----|----|----|-----|
| totalNoSuj | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| totalExenta | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| totalGravada | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅± | ❌ | ✅ | ❌ | ❌ |
| subTotalVentas | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| descuNoSuj | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| descuExenta | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| descuGravada | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| porcentajeDescuento | ✅ | ✅ | ✅? | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| totalDescu | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅? | ❌ |
| tributos | ✅? | ✅? | ✅? | ✅? | ✅? | ❌ | ✅? | ❌ | ❌ | ❌ | ❌ |
| subTotal | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| ivaPerci1 | ❌ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| ivaRete1 | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| reteRenta | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| montoTotalOperacion | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅± | ❌ | ✅ | ❌ | ❌ |
| totalNoGravado | ✅± | ✅± | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅± | ❌ | ❌ |
| totalPagar | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ |
| totalLetras | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ | ✅ | ✅ |
| totalIva | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| saldoFavor | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| condicionOperacion | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ | ✅ | ❌ |
| pagos | ✅? | ✅? | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ✅? | ✅? |
| numPagoElectronico | ✅? | ✅? | ❌ | ❌ | ✅? | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| **Campos únicos DTE 07** |||||||||||||
| totalSujetoRetencion | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| totalIVAretenido | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| totalIVAretenidoLetras | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Campos únicos DTE 08** |||||||||||||
| totalExportacion | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| ivaPerci | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| total | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅± | ❌ | ❌ | ❌ | ❌ |
| **Campos únicos DTE 11** |||||||||||||
| descuento | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| seguro | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| flete | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| codIncoterms | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| descIncoterms | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| observaciones | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ✅? | ❌ |
| **Campos únicos DTE 14** |||||||||||||
| totalCompra | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| descu | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Campos únicos DTE 15** |||||||||||||
| valorTotal | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |

**Leyenda:**
- ✅ = Campo presente y requerido (NOT NULL)
- ✅? = Campo presente pero nullable
- ✅± = Campo presente pero permite valores negativos
- ❌ = Campo no existe en este tipo de DTE

---

## Notas importantes para implementación - Resumen

1. **DTE 09 (DCL) no tiene sección `resumen`**: Toda la información está en `cuerpoDocumento`.

2. **DTE 08 permite valores negativos**: Todos los campos monetarios pueden ser negativos (para ajustes).

3. **Validación de `tributos`**: 
   - Array nullable en la mayoría de DTEs
   - Si `totalGravada = 0`: `ivaPerci1` e `ivaRete1` deben ser 0 (en 03, 05, 06)

4. **Estructura de `pagos` varía**:
   - DTEs 01, 03, 11, 14: incluyen `plazo` y `periodo`
   - DTE 15: solo `codigo`, `montoPago`, `referencia`

5. **Campo `totalLetras`**: 
   - Presente en casi todos los DTEs (excepto 09)
   - Siempre maxLength: 200

6. **Condición de operación**:
   - Si `totalPagar = 0` o `total = 0`: `condicionOperacion` debe ser 1 (contado)
   - Si `condicionOperacion = 2` (crédito): `pagos` debe tener `plazo` y `periodo` no null

7. **Campos únicos por DTE**:
   - 01: `totalIva`, `saldoFavor`
   - 03: `saldoFavor`, `ivaPerci1`
   - 07: `totalSujetoRetencion`, `totalIVAretenido`, `totalIVAretenidoLetras`
   - 08: `totalExportacion`, `ivaPerci`, `total`
   - 11: `seguro`, `flete`, `codIncoterms`, `descIncoterms`
   - 14: `totalCompra`, `descu`
   - 15: `valorTotal`
