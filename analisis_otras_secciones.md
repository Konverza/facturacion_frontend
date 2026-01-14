---

# Análisis de las secciones adicionales: ventaTercero, otrosDocumentos, extension, apendice

## Índice - Secciones adicionales
- [Sección ventaTercero](#sección-ventatercero)
- [Sección otrosDocumentos](#sección-otrosdocumentos)
- [Sección extension](#sección-extension)
- [Sección apendice](#sección-apendice)
- [Matriz de compatibilidad - Secciones adicionales](#matriz-de-compatibilidad---secciones-adicionales)

---

## Sección ventaTercero

### Descripción
Información sobre ventas realizadas por cuenta de terceros (intermediarios). Esta sección identifica al tercero por el cual se está facturando.

### DTEs que incluyen esta sección:
**01 (Factura), 03 (CCF), 04 (NR), 05 (NC), 06 (ND), 11 (FEX)**

### Estructura (idéntica en todos):

| Campo | Tipo | Nullable | Validación | Observaciones |
|-------|------|----------|------------|---------------|
| `nit` | string | No | Pattern: `^([0-9]{14}\|[0-9]{9})$` | NIT de 14 o 9 dígitos |
| `nombre` | string | No | minLength: 1-3, maxLength: 200-250 | Razón social del tercero |

### Variaciones por DTE:

```javascript
// DTE 01, 03, 05, 06: minLength nombre = 3
{
  "nit": "06142803901234",
  "nombre": "EMPRESA INTERMEDIARIA S.A. DE C.V."  // minLength: 3
}

// DTE 04: minLength nombre = 1
{
  "nit": "06142803901234",
  "nombre": "E"  // Permitido
}

// DTE 11: minLength nombre = 1, maxLength = 250 (en lugar de 200)
{
  "nit": "06142803901234",
  "nombre": "INTERNATIONAL TRADING COMPANY INC..."  // maxLength: 250
}
```

### Tipo de objeto:
- **Nullable**: Sí (`["object", "null"]`)
- **Required**: Sí (si se incluye)

### DTEs que NO tienen esta sección:
07 (CR), 08 (CL), 09 (DCL), 14 (FSE), 15 (CD)

---

## Sección otrosDocumentos

### Descripción
Documentos asociados o complementarios al DTE principal. Incluye facturas de servicios médicos, documentos de transporte, etc.

### DTEs que incluyen esta sección:

#### Grupo 1: Documentos estándar (01, 03)
**Estructura completa con opción de servicios médicos**

| Campo | Tipo | Nullable | Validación | Observaciones |
|-------|------|----------|------------|---------------|
| `codDocAsociado` | integer | No | min: 1, max: 4 | Tipo de documento |
| `descDocumento` | string | Condicional | maxLength: 100 | Descripción del documento |
| `detalleDocumento` | string | Condicional | maxLength: 300 | Detalle del documento |
| `medico` | object | Condicional | - | Solo si `codDocAsociado = 3` |

**Códigos de documento asociado:**
- `1`: Otro documento
- `2`: Otro documento
- `3`: **Servicios médicos** (activa sección `medico`)
- `4`: Otro documento

**Estructura del objeto `medico`** (solo si `codDocAsociado = 3`):

| Campo | Tipo | Nullable | Validación | Observaciones |
|-------|------|----------|------------|---------------|
| `nombre` | string | No | maxLength: 100 | Nombre del médico |
| `nit` | string | Condicional | Pattern: `^([0-9]{14}\|[0-9]{9})$` | NIT del médico |
| `docIdentificacion` | string | Condicional | minLength: 2, maxLength: 25 | Doc. extranjero |
| `tipoServicio` | number | No | min: 1, max: 6 | Código del servicio |

**Validación XOR:** Debe tener `nit` O `docIdentificacion` (no ambos, no ninguno):
```javascript
if (nit === null) {
    docIdentificacion = "string";  // Requerido
}
if (docIdentificacion === null) {
    nit = "string";  // Requerido
}
```

**Condiciones:**
```javascript
// Si codDocAsociado = 3 (Servicios médicos)
if (codDocAsociado === 3) {
    medico = objeto completo;
    descDocumento = null;
    detalleDocumento = null;
}

// Si codDocAsociado != 3
else {
    descDocumento = "string";  // Requerido
    detalleDocumento = "string";  // Requerido
    medico = null;
}
```

**Límites:**
- minItems: 1
- maxItems: 10

---

#### Grupo 2: Factura de Exportación (11)
**Estructura extendida para documentos de transporte internacional**

| Campo | Tipo | Nullable | Validación | Observaciones |
|-------|------|----------|------------|---------------|
| `codDocAsociado` | integer | No | enum: [1,2,3,4] | Tipo de documento |
| `descDocumento` | string | Condicional | maxLength: 100 | Descripción |
| `detalleDocumento` | string | Condicional | maxLength: 300 | Detalle |
| `placaTrans` | string | Condicional | minLength: 5, maxLength: 70 | Identificación transporte |
| `modoTransp` | integer | Condicional | enum: [1,2,3,4,5,6,7] | Modo de transporte |
| `numConductor` | string | Condicional | minLength: 5, maxLength: 100 | Doc. conductor |
| `nombreConductor` | string | Condicional | minLength: 5, maxLength: 200 | Nombre conductor |

**Códigos modoTransp:**
- `1`: Marítimo
- `2`: Aéreo
- `3`: Terrestre
- `4`: Ferroviario
- `5`: Otro
- `6`: Multimodal
- `7`: Postales

**Condiciones:**
```javascript
// Si codDocAsociado = 4 (Documento de transporte)
if (codDocAsociado === 4) {
    modoTransp = integer;       // Requerido
    numConductor = "string";    // Requerido
    nombreConductor = "string"; // Requerido
    placaTrans = "string";      // Requerido
}

// Si codDocAsociado = 1 o 2
if (codDocAsociado === 1 || codDocAsociado === 2) {
    descDocumento = "string";   // Requerido
    detalleDocumento = "string"; // Requerido
}

// Si codDocAsociado != 4
else {
    modoTransp = null;
    numConductor = null;
    nombreConductor = null;
    placaTrans = null;
}
```

**Límites:**
- minItems: 1
- maxItems: **20** (mayor que otros DTEs)

---

#### Grupo 3: Comprobante de Donación (15)
**Estructura simplificada**

| Campo | Tipo | Nullable | Validación | Observaciones |
|-------|------|----------|------------|---------------|
| `codDocAsociado` | integer | No | enum: [1,2] | Solo 2 opciones |
| `descDocumento` | string | No | maxLength: 100 | Descripción |
| `detalleDocumento` | string | No | maxLength: 300 | Detalle |

**Límites:**
- minItems: 1
- maxItems: 10

**No incluye:** `medico`, campos de transporte

---

### DTEs que NO tienen esta sección:
04 (NR), 05 (NC), 06 (ND), 07 (CR), 08 (CL), 09 (DCL), 14 (FSE)

### Tipo de objeto:
- **Nullable**: Sí (`["array", "null"]`) en todos los DTEs que la incluyen
- **Required**: Sí (debe estar presente aunque sea null)

---

## Sección extension

### Descripción
Información adicional sobre la entrega y recepción del documento, observaciones generales, y datos específicos según el tipo de DTE.

### DTEs que incluyen esta sección:
**Todos excepto:** 11 (FEX), 14 (FSE), 15 (CD)

### Estructura base (presente en la mayoría):

| Campo | Tipo | Nullable | Min/Max Length | Observaciones |
|-------|------|----------|----------------|---------------|
| `nombEntrega` | string | Sí (null) | min: 1-5, max: 100 | Nombre quien genera |
| `docuEntrega` | string | Sí (null) | min: 1-5, max: 25 | Documento quien genera |
| `nombRecibe` | string | Sí (null) | min: 1-5, max: 100 | Nombre receptor |
| `docuRecibe` | string | Sí (null) | min: 1-5, max: 25 | Documento receptor |
| `observaciones` | string | Sí (null) | maxLength: 3000 | Observaciones generales |

### Variaciones por DTE:

#### Grupo 1: Factura y CCF (01, 03)
**Incluye campo adicional de vehículo**

```javascript
{
  "nombEntrega": "Juan Pérez",        // minLength: 5
  "docuEntrega": "03654789-0",
  "nombRecibe": "María López",
  "docuRecibe": "04987123-5",
  "observaciones": "Entrega urgente",
  "placaVehiculo": "P123456"          // Campo adicional, maxLength: 10
}
```

| Campo adicional | Tipo | Nullable | Validación | Observaciones |
|-----------------|------|----------|------------|---------------|
| `placaVehiculo` | string | Sí (null) | minLength: 2, maxLength: 10 | Placa del vehículo |

---

#### Grupo 2: Notas (04, 05, 06)
**Sin campo de vehículo**

```javascript
{
  "nombEntrega": "J",                 // minLength: 1 (más flexible)
  "docuEntrega": "0",                 // minLength: 1
  "nombRecibe": "M",
  "docuRecibe": "0",
  "observaciones": "Sin observaciones"
}
```

**No incluye:** `placaVehiculo`

---

#### Grupo 3: Comprobante de Retención y Liquidación (07, 08)
**Igual que Grupo 1 pero con minLength: 5 (más estricto)**

```javascript
{
  "nombEntrega": "Maria",             // minLength: 5 (más estricto)
  "docuEntrega": "03654",
  "nombRecibe": "Pedro",
  "docuRecibe": "04987",
  "observaciones": "Retención aplicada"
}
```

**No incluye:** `placaVehiculo`

---

#### Grupo 4: Documento Contable de Liquidación (09)
**Estructura única y simplificada**

| Campo | Tipo | Nullable | Min/Max Length | Observaciones |
|-------|------|----------|----------------|---------------|
| `nombEntrega` | string | No | min: 5, max: 100 | Requerido (no null) |
| `docuEntrega` | string | No | min: 5, max: 25 | Requerido (no null) |
| `codEmpleado` | string | Sí (null) | min: 1, max: 15 | **Campo único de este DTE** |

```javascript
{
  "nombEntrega": "Carlos Ruiz",       // Requerido
  "docuEntrega": "12345678-9",        // Requerido
  "codEmpleado": "EMP001"             // Campo adicional
}
```

**No incluye:** `nombRecibe`, `docuRecibe`, `observaciones`, `placaVehiculo`

---

### Tipo de objeto:
- **01, 03, 04, 05, 06, 07, 08**: `["object", "null"]` (nullable)
- **09**: `object` (requerido, no nullable)

### DTEs que NO tienen esta sección:
11 (FEX), 14 (FSE), 15 (CD)

---

## Sección apendice

### Descripción
Campos personalizados adicionales para agregar información no contemplada en el esquema estándar. Permite flexibilidad para información específica de cada empresa.

### DTEs que incluyen esta sección:
**Todos los DTEs** (01, 03, 04, 05, 06, 07, 08, 09, 11, 14, 15)

### Estructura (idéntica en todos):

| Campo | Tipo | Nullable | Min/Max Length | Observaciones |
|-------|------|----------|----------------|---------------|
| `campo` | string | No | min: 2, max: 25 | Nombre del campo personalizado |
| `etiqueta` | string | No | min: 3, max: 50 | Etiqueta descriptiva |
| `valor` | string | No | min: 1, max: 150 | Valor del campo |

### Ejemplo:

```javascript
{
  "apendice": [
    {
      "campo": "referenciaPedido",
      "etiqueta": "Número de Pedido del Cliente",
      "valor": "PO-2024-001234"
    },
    {
      "campo": "vendedor",
      "etiqueta": "Código de Vendedor",
      "valor": "VEND-0012"
    },
    {
      "campo": "centroCoste",
      "etiqueta": "Centro de Costos",
      "valor": "CC-VENTAS-01"
    }
  ]
}
```

### Variaciones en validación:

#### Grupo 1: Mayoría de DTEs (01, 04, 05, 06, 08, 11)
**Sin minLength especificado** (solo maxLength)

```javascript
{
  "campo": "c",           // Sin minLength
  "etiqueta": "e",        // Sin minLength
  "valor": "v"            // Sin minLength
}
```

#### Grupo 2: DTEs con minLength (03, 07, 09, 15)
**Con validación de longitud mínima**

| Campo | minLength |
|-------|-----------|
| `campo` | 2 |
| `etiqueta` | 3 |
| `valor` | 1 |

```javascript
{
  "campo": "ab",          // minLength: 2
  "etiqueta": "abc",      // minLength: 3
  "valor": "a"            // minLength: 1
}
```

#### Grupo 3: Sujeto Excluido (14)
**Sin validación de longitud mínima**

```javascript
{
  "campo": "",            // Sin minLength
  "etiqueta": "",         // Sin minLength
  "valor": ""             // Sin minLength
}
```

### Límites:
- **minItems**: 1 (si se incluye)
- **maxItems**: 10

### Tipo de objeto:
- **Nullable**: Sí (`["array", "null"]`) en **todos los DTEs**
- **Required**: Sí (debe estar presente aunque sea null)

### Uso recomendado:
- Números de referencia internos
- Códigos de vendedor/sucursal
- Información de proyectos
- Referencias de pedidos
- Cualquier dato adicional relevante para el negocio

---

## Matriz de compatibilidad - Secciones adicionales

| Sección / DTE | 01 | 03 | 04 | 05 | 06 | 07 | 08 | 09 | 11 | 14 | 15 |
|---------------|----|----|----|----|----|----|----|----|----|----|-----|
| **ventaTercero** | ✅? | ✅? | ✅? | ✅? | ✅? | ❌ | ❌ | ❌ | ✅? | ❌ | ❌ |
| **otrosDocumentos** | ✅? | ✅? | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅? | ❌ | ✅? |
| **extension** | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅ | ❌ | ❌ | ❌ |
| **apendice** | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? | ✅? |

**Leyenda:**
- ✅ = Sección presente y requerida (NOT NULL)
- ✅? = Sección presente pero nullable
- ❌ = Sección no existe

---

### Detalles por sección:

#### ventaTercero
| DTE | Presente | maxLength nombre | minLength nombre |
|-----|----------|------------------|------------------|
| 01  | Sí (nullable) | 200 | 3 |
| 03  | Sí (nullable) | 200 | 3 |
| 04  | Sí (nullable) | 200 | 1 |
| 05  | Sí (nullable) | 200 | 3 |
| 06  | Sí (nullable) | 200 | 3 |
| 11  | Sí (nullable) | **250** | 1 |

---

#### otrosDocumentos
| DTE | Presente | maxItems | Características especiales |
|-----|----------|----------|----------------------------|
| 01  | Sí (nullable) | 10 | Incluye objeto `medico` |
| 03  | Sí (nullable) | 10 | Incluye objeto `medico` |
| 11  | Sí (nullable) | **20** | Incluye campos de transporte |
| 15  | Sí (nullable) | 10 | Estructura simplificada (sin medico ni transporte) |

---

#### extension
| DTE | Presente | Nullable | Campos únicos |
|-----|----------|----------|---------------|
| 01  | Sí | Sí | `placaVehiculo` |
| 03  | Sí | Sí | `placaVehiculo` |
| 04  | Sí | Sí | - |
| 05  | Sí | Sí | - |
| 06  | Sí | Sí | - |
| 07  | Sí | Sí | - |
| 08  | Sí | Sí | - |
| 09  | Sí | **No** | `codEmpleado` (estructura reducida) |

---

#### apendice
| DTE | minLength campo | minLength etiqueta | minLength valor |
|-----|-----------------|--------------------|--------------------|
| 01  | - | - | - |
| 03  | 2 | 3 | 1 |
| 04  | - | - | - |
| 05  | - | - | - |
| 06  | - | - | - |
| 07  | 2 | 3 | 1 |
| 08  | - | - | - |
| 09  | 2 | 3 | 1 |
| 11  | - | - | - |
| 14  | - | - | - |
| 15  | 2 | 3 | 1 |

---

## Código de implementación

### Validación de ventaTercero:

```python
from pydantic import BaseModel, Field, validator
from typing import Optional

class VentaTercero(BaseModel):
    nit: str = Field(..., pattern=r"^([0-9]{14}|[0-9]{9})$")
    nombre: str
    
    @validator("nombre")
    def validate_nombre_length(cls, v, values):
        # Diferentes minLength según tipoDte (pasar como contexto)
        tipoDte = cls.Config.tipoDte  # Configurar externamente
        
        if tipoDte in ["01", "03", "05", "06"]:
            if len(v) < 3:
                raise ValueError("nombre debe tener al menos 3 caracteres")
            if len(v) > 200:
                raise ValueError("nombre no puede exceder 200 caracteres")
        elif tipoDte == "04":
            if len(v) < 1 or len(v) > 200:
                raise ValueError("nombre debe tener entre 1 y 200 caracteres")
        elif tipoDte == "11":
            if len(v) < 1 or len(v) > 250:
                raise ValueError("nombre debe tener entre 1 y 250 caracteres")
        
        return v
```

---

### Validación de otrosDocumentos:

```python
class Medico(BaseModel):
    nombre: str = Field(..., max_length=100)
    nit: Optional[str] = Field(None, pattern=r"^([0-9]{14}|[0-9]{9})$")
    docIdentificacion: Optional[str] = Field(None, min_length=2, max_length=25)
    tipoServicio: int = Field(..., ge=1, le=6)
    
    @validator("nit")
    def validate_nit_xor_doc(cls, v, values):
        # XOR: debe tener nit O docIdentificacion (no ambos, no ninguno)
        doc = values.get("docIdentificacion")
        if v is None and doc is None:
            raise ValueError("Debe proporcionar nit o docIdentificacion")
        if v is not None and doc is not None:
            raise ValueError("Solo puede proporcionar nit o docIdentificacion, no ambos")
        return v


class OtroDocumento(BaseModel):
    codDocAsociado: int = Field(..., ge=1, le=4)
    descDocumento: Optional[str] = Field(None, max_length=100)
    detalleDocumento: Optional[str] = Field(None, max_length=300)
    medico: Optional[Medico] = None
    
    @validator("medico")
    def validate_medico_conditional(cls, v, values):
        cod = values.get("codDocAsociado")
        
        if cod == 3:
            # Si es servicio médico, medico es requerido
            if v is None:
                raise ValueError("medico es requerido cuando codDocAsociado = 3")
            # desc y detalle deben ser null
            if values.get("descDocumento") is not None:
                raise ValueError("descDocumento debe ser null cuando codDocAsociado = 3")
        else:
            # Si no es servicio médico, medico debe ser null
            if v is not None:
                raise ValueError("medico debe ser null cuando codDocAsociado != 3")
            # desc y detalle son requeridos
            if values.get("descDocumento") is None:
                raise ValueError("descDocumento es requerido cuando codDocAsociado != 3")
        
        return v


class OtroDocumentoExportacion(BaseModel):
    """Para DTE 11 (Exportación)"""
    codDocAsociado: int = Field(..., ge=1, le=4)
    descDocumento: Optional[str] = Field(None, max_length=100)
    detalleDocumento: Optional[str] = Field(None, max_length=300)
    placaTrans: Optional[str] = Field(None, min_length=5, max_length=70)
    modoTransp: Optional[int] = Field(None, ge=1, le=7)
    numConductor: Optional[str] = Field(None, min_length=5, max_length=100)
    nombreConductor: Optional[str] = Field(None, min_length=5, max_length=200)
    
    @validator("modoTransp")
    def validate_transporte_conditional(cls, v, values):
        cod = values.get("codDocAsociado")
        
        if cod == 4:
            # Documento de transporte: todos los campos son requeridos
            if v is None:
                raise ValueError("modoTransp requerido cuando codDocAsociado = 4")
            if values.get("placaTrans") is None:
                raise ValueError("placaTrans requerido cuando codDocAsociado = 4")
        else:
            # No es transporte: todos deben ser null
            if v is not None:
                raise ValueError("modoTransp debe ser null cuando codDocAsociado != 4")
        
        return v
```

---

### Validación de extension:

```python
class Extension(BaseModel):
    nombEntrega: Optional[str] = Field(None, max_length=100)
    docuEntrega: Optional[str] = Field(None, max_length=25)
    nombRecibe: Optional[str] = Field(None, max_length=100)
    docuRecibe: Optional[str] = Field(None, max_length=25)
    observaciones: Optional[str] = Field(None, max_length=3000)
    placaVehiculo: Optional[str] = Field(None, max_length=10)
    
    class Config:
        # Configurar minLength según tipoDte
        tipoDte: str = "01"
    
    @validator("nombEntrega", "nombRecibe")
    def validate_nombre_minlength(cls, v):
        if v is None:
            return v
        
        tipoDte = cls.Config.tipoDte
        
        if tipoDte in ["01", "03", "07", "08", "09"]:
            min_len = 5
        else:  # 04, 05, 06
            min_len = 1
        
        if len(v) < min_len:
            raise ValueError(f"Debe tener al menos {min_len} caracteres")
        
        return v


class ExtensionDCL(BaseModel):
    """Para DTE 09 - Estructura reducida y no nullable"""
    nombEntrega: str = Field(..., min_length=5, max_length=100)
    docuEntrega: str = Field(..., min_length=5, max_length=25)
    codEmpleado: Optional[str] = Field(None, min_length=1, max_length=15)
```

---

### Validación de apendice:

```python
class Apendice(BaseModel):
    campo: str = Field(..., max_length=25)
    etiqueta: str = Field(..., max_length=50)
    valor: str = Field(..., max_length=150)
    
    class Config:
        tipoDte: str = "01"
    
    @validator("campo", "etiqueta", "valor")
    def validate_minlength(cls, v, field):
        tipoDte = cls.Config.tipoDte
        
        # DTEs con minLength: 03, 07, 09, 15
        if tipoDte in ["03", "07", "09", "15"]:
            min_lengths = {
                "campo": 2,
                "etiqueta": 3,
                "valor": 1
            }
            min_len = min_lengths.get(field.name, 0)
            
            if len(v) < min_len:
                raise ValueError(f"{field.name} debe tener al menos {min_len} caracteres")
        
        return v
```

---

## Notas importantes para implementación

### ventaTercero
1. **Validación NIT**: Siempre 14 o 9 dígitos
2. **maxLength nombre varía**: 200 (mayoría) vs 250 (DTE 11)
3. **minLength nombre varía**: 3 (mayoría) vs 1 (DTEs 04, 11)
4. **Nullable**: Siempre nullable en todos los DTEs que lo incluyen

### otrosDocumentos
1. **Objeto `medico`**: Solo en DTEs 01, 03 cuando `codDocAsociado = 3`
2. **XOR validation**: `nit` XOR `docIdentificacion` en objeto medico
3. **Campos de transporte**: Solo en DTE 11 cuando `codDocAsociado = 4`
4. **maxItems varía**: 10 (mayoría) vs 20 (DTE 11)
5. **Validación condicional compleja**: Requiere lógica if/else robusta

### extension
1. **DTE 09 única**: No nullable, estructura reducida, campo `codEmpleado`
2. **minLength varía**: 5 (DTEs 01,03,07,08,09) vs 1 (DTEs 04,05,06)
3. **Campo `placaVehiculo`**: Solo en DTEs 01, 03
4. **Campos reducidos en DTE 09**: Solo 3 campos (no incluye nombRecibe, docuRecibe, observaciones)

### apendice
1. **Universal**: Presente en **todos los DTEs**
2. **Siempre nullable**: `["array", "null"]`
3. **minLength condicional**: Solo en DTEs 03, 07, 09, 15
4. **Límite fijo**: maxItems = 10 en todos
5. **Uso flexible**: Permite campos personalizados no contemplados en el esquema

### Consideraciones generales
- Todas estas secciones son **opcionales** (nullable) excepto `extension` en DTE 09
- **apendice** es la única sección presente en **todos los DTEs sin excepción**
- Las validaciones condicionales son críticas en `otrosDocumentos`
- `extension` tiene la mayor variación de estructura entre DTEs
