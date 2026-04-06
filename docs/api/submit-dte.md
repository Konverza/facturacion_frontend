# API Submit DTE (Especificación Técnica Exhaustiva)

Esta especificación documenta el contrato real del endpoint según la implementación backend actual.

## Endpoint
- Method: POST
- URL: /api/submitDte
- Content-Type: application/json

## Autenticación
- Header soportado:
  - Authorization: Bearer {API_KEY}
  - Authorization: ApiKey {API_KEY}
- Errores:
  - 401 Missing Authorization header.
  - 401 Invalid API key.
  - 401 Unauthorized.

Notas técnicas:
- El token se hashea con SHA-256 y se compara contra business.api_key_hash.
- Además, el negocio debe tener has_api_access = true.
- Si autentica, el middleware fija session('business') automáticamente.

## Tipos de DTE soportados
- 01: Factura
- 03: Crédito Fiscal
- 04: Nota de Remisión
- 05: Nota de Crédito
- 06: Nota de Débito
- 07: Comprobante de Retención
- 11: Factura de Exportación
- 14: Sujeto Excluido
- 15: Comprobante de Donación

Si dte.type no está en esa lista, responde 422 con mensaje Tipo de DTE no soportado o ausente.

## Esquema general del request

Ejemplo base:

{
  "dte": {
    "type": "01",
    "customer": { ... },
    "products": [ ... ]
  }
}

Regla mínima de validación inicial:
- dte: requerido, tipo objeto.

Luego, el backend completa defaults y aplica reglas de negocio.

## Matriz de campos del objeto dte

### 1) Campos raíz principales

- type
  - Tipo: string
  - Requerido: sí
  - Nullable: no
  - Valores permitidos: 01, 03, 04, 05, 06, 07, 11, 14, 15

- products
  - Tipo: array
  - Requerido: sí para todos excepto 07
  - Nullable: no (si no viene, se inicializa a [])
  - Regla: para type != 07, debe tener al menos 1 item

- customer
  - Tipo: object
  - Requerido: recomendado sí
  - Nullable: no (si no viene, se inicializa como objeto vacío)
  - Nota: los datos se normalizan y se proyectan según type

- condicion_operacion
  - Tipo: string
  - Requerido: no
  - Default: 1 (si no viene en dte.condicion_operacion ni dte.resumen.condicionOperacion)
  - Nullable: no en práctica, porque cae a default

- bienTitulo
  - Tipo: string
  - Requerido: sí para type 04
  - Nullable: sí para otros tipos
  - Catálogo: CAT-025

- metodos_pago
  - Tipo: array
  - Requerido: condicional
  - Nullable: no (si no viene, se inicializa [])
  - Regla fuerte:
    - Para type != 04, monto_abonado debe ser igual a total_pagar (redondeo a 2 decimales).
    - Para type 15, si hay donaciones de tipoDonacion = 1 con valor > 0, al menos un método de pago.

- monto_abonado
  - Tipo: number
  - Requerido: condicional (necesario para pasar validación de balance)
  - Nullable: no (default 0)

- documentos_relacionados
  - Tipo: array
  - Requerido: no
  - Nullable: no (default [])
  - Regla: si existe y tiene elementos, cada uno debe estar referenciado por al menos un producto en producto.documento_relacionado

- otros_documentos
  - Tipo: array
  - Requerido: sí para type 15
  - Nullable: no (default [])
  - Regla type 15: debe existir al menos un documento asociado

- documentos_retencion
  - Tipo: array
  - Requerido: sí para type 07 (en práctica)
  - Nullable: sí para otros
  - Nota: no se inicializa en defaults globales, debe enviarse cuando aplique retención

- emisor.regimen
- emisor.recintoFiscal
- emisor.tipoItemExpor
  - Tipo: string
  - Requerido: solo para type 11 según reglas funcionales
  - Nullable: regimen y recintoFiscal son nullable, tipoItemExpor se trata como requerido funcionalmente
  - Catálogos:
    - regimen_exportacion: CAT-028
    - recinto_fiscal: CAT-027

- resumen.codIncoterms
- incoterms
  - Tipo: string
  - Requerido: no
  - Nullable: sí
  - Prioridad: usa resumen.codIncoterms, y si no existe, dte.incoterms
  - Catálogo: CAT-031

- flete, seguro
  - Tipo: number
  - Requerido: no
  - Nullable: no (default 0)
  - Uso: type 11 para total_pagar

- retener_iva, retener_renta, percibir_iva
  - Tipo: string
  - Requerido: no
  - Nullable: sí en defaults iniciales
  - Valor activo esperado: active
  - Si no active, se trata como inactivo

- percentaje_descuento_venta_gravada
- percentaje_descuento_venta_exenta
- percentaje_descuento_venta_no_sujeta
  - Tipo: number
  - Requerido: no
  - Nullable: no (default 0)
  - Rango: no hay validación explícita; recomendado 0..100

- descuento_venta_gravada
- descuento_venta_exenta
- descuento_venta_no_sujeta
  - Tipo: number
  - Requerido: no
  - Nullable: no (default 0)

- turismo_por_alojamiento, turismo_salida_pais_via_aerea, fovial, contrans, bebidas_alcoholicas, tabaco_cigarillos, tabaco_cigarros
  - Tipo: number
  - Requerido: no
  - Nullable: no (default 0)
  - Códigos de tributo asociados:
    - 59, 71, D1, C8, C5, C6, C7

### 2) Objeto customer (input JSON)

Campos aceptados en customer:
- tipoDocumento
- numDocumento
- nrc
- nombre
- nombreComercial
- codActividad
- descActividad
- departamento
- municipio
- complemento
- telefono
- correo
- tipoPersona
- pais

Normalización aplicada:

- tipoDocumento
  - Acepta códigos: 02, 03, 13, 36, 37
  - Acepta alias texto:
    - nit -> 36
    - dui -> 13
    - pasaporte/pasport/passport -> 02
    - carnet de residente/carnet residencia/carnet de residencia/residente -> 03
    - otro/otros -> 37

- numDocumento
  - Si tipoDocumento = 36 (NIT): deja solo dígitos
  - Si tipoDocumento = 13 (DUI):
    - En type 03/05/06: solo dígitos
    - En otros: intenta formato XXXXXXXX-X cuando hay 9 dígitos
  - Otros tipos de documento: conserva texto recortado

- nrc
  - Se normaliza a solo dígitos
  - Límite funcional observado en flujos UI: máximo 8 dígitos

- codActividad
  - Puede enviarse como código o texto
  - Se resuelve por CAT-019

- departamento
  - Puede enviarse como código o nombre
  - Se resuelve por CAT-012

- municipio
  - Puede enviarse como código o nombre
  - Se resuelve por CAT-012 filtrado por departamento

- tipoPersona
  - Acepta convención textual natural/juridica y mapea a 01/02
  - Si ya viene código, se respeta

Defaults por ausencia:
- customer.departamento: 06
- customer.municipio: 01

## Proyección del receptor según type

El JSON customer se transforma en estructuras distintas para Hacienda/Octopus:

- type 01
  - Usa receptor con tipoDocumento, numDocumento, nombre, teléfono, correo, dirección, actividad, nrc
  - Regla especial: si tipoDocumento era DUI (13) y nrc existe, fuerza tipoDocumento a 36 y numDocumento a solo dígitos

- type 03
  - Usa receptor con nit y nrc (en lugar de numDocumento/tipoDocumento)

- type 04
  - Requiere bienTitulo en receptor

- type 05 y 06
  - Usa nit y nrc

- type 07
  - Receptor con tipoDocumento y numDocumento

- type 11
  - Receptor incluye codPais y nombrePais, tipoPersona
  - codPais viene de customer.pais mapeado a request.codigo_pais

- type 14
  - Regla especial: si documento es DUI (13), se envía sin guion

- type 15
  - Incluye codPais y codDomiciliado (si está en request)

## Objeto products (dte.products)

### Estructura recomendada por item

Campos comunes esperados por cálculos y armado de cuerpoDocumento:
- id (opcional en API; usado internamente)
- product (opcional objeto; si existe product.id, habilita validación de stock)
- product_id (opcional)
- codigo (opcional)
- unidad_medida
- descripcion
- cantidad
- tipo: Gravada | Exenta | No sujeta
- precio
- precio_sin_tributos
- descuento
- ventas_gravadas
- ventas_exentas
- ventas_no_sujetas
- total
- iva
- tipo_item (1 bien, 2 servicio; muy importante para retenciones)
- documento_relacionado (obligatorio en práctica para type 05 y 06)
- tributos (opcional, array o JSON string)
- banderas de tributos opcionales: turismo_por_alojamiento, turismo_salida_pais_via_aerea, fovial, contrans, bebidas_alcoholicas, tabaco_cigarillos, tabaco_cigarros

### Reglas por tipo para products

- type 01
  - precioUni en salida se toma de precio (para gravadas con IVA incluido)
  - ivaItem calculado desde item.iva

- type 03, 04, 05, 06
  - precioUni en salida se toma de precio_sin_tributos
  - Para type 05 y 06, documento_relacionado por item es obligatorio funcionalmente

- type 11
  - cuerpoDocumento usa: cantidad, codigo, uniMedida, descripcion, precioUni (sin tributos), montoDescu, ventaGravada, tributos, noGravado

- type 14
  - No usa IVA
  - cuerpoDocumento usa compra = base - descuento, con base en ventas_exentas o cantidad * precio_sin_tributos

- type 15
  - No usa estructura de item normal
  - Usa getCuerpoDocumentoComprobanteDonacion con campos por item:
    - tipo_donacion
    - cantidad
    - unidad_medida
    - descripcion
    - depreciacion
    - valor_unitario
    - valor_donado

- type 07
  - No usa products para cuerpoDocumento
  - Usa documentos_retencion

## documentos_relacionados

Entrada esperada por item:
- tipo_documento
- tipo_generacion
- numero_documento
- fecha_documento

Salida a Hacienda:
- tipoDocumento
- tipoGeneracion (int)
- numeroDocumento
- fechaEmision

Regla de consistencia:
- Cada numero_documento en documentos_relacionados debe estar referenciado por al menos un products[].documento_relacionado

## documentos_retencion (type 07)

Entrada esperada por item:
- tipo_generacion
- tipo_documento
- numero_documento
- codigo_retencion
- descripcion_retencion
- fecha_documento
- monto_sujeto_retencion
- iva_retenido

Salida a cuerpoDocumento:
- tipoDte
- tipoDoc
- numDocumento
- fechaEmision
- montoSujetoGrav
- codigoRetencionMH
- ivaRetenido
- descripcion

## otros_documentos

Entrada esperada por item:
- documento_asociado
- descripcion_documento (nullable)
- identificacion_documento (nullable)
- medico (nullable objeto)
  - nombre
  - tipo_servicio
  - tipo_documento
  - numero_documento
- Para exportación (documento_asociado = 4 en UI):
  - placas
  - modo_transporte
  - numero_identificacion
  - nombre_conductor

Salida:
- codDocAsociado (int)
- descDocumento
- detalleDocumento
- medico (cuando aplica)
- o placaTrans/modoTransp/numConductor/nombreConductor para type 11

Reglas de longitud observadas en flujo UI asociado:
- medico tipo_documento = 1: numero_documento debe tener 14 dígitos
- placas: min 5, max 70
- numero_identificacion: existe validación en código con tope 5 (hay condición duplicada posterior a 100)

## metodos_pago

Entrada esperada por item:
- id (opcional)
- forma_pago
- monto
- numero_documento (referencia)
- plazo (nullable)
- periodo (nullable)

Reglas:
- monto > 0
- Suma de montos no puede exceder total_pagar
- Para type != 15 se proyecta: codigo, montoPago, referencia, plazo, periodo
- Para type 15 se proyecta: codigo, montoPago, referencia

Catálogo de forma de pago:
- CAT-017

## Catálogos de Hacienda/Octopus usados en este flujo

- CAT-014: Unidad de medida
- CAT-012: Departamento y municipio
- CAT-022: Tipo de documento
- CAT-019: Actividad económica
- CAT-020: País
- CAT-027: Recinto fiscal
- CAT-028: Régimen de exportación
- CAT-009: Tipo de establecimiento
- CAT-017: Forma de pago
- CAT-010: Tipo de servicio
- CAT-030: Modo de transporte
- CAT-031: Incoterms
- CAT-025: Bienes remitidos a título de

## Validaciones de negocio críticas (rechazo)

El endpoint puede responder estado RECHAZADO con observaciones cuando:

- type != 07 y no hay productos
- Hay stock insuficiente para productos de base de datos con has_stock activo
- type 15 y no hay otros_documentos
- type 15 con donaciones monetarias y sin metodos_pago
- Hay documentos_relacionados sin vínculo a ningún producto
- type != 04 y monto_abonado no coincide con total_pagar

## Nullables y defaults importantes

Campos que backend inicializa a 0 si vienen null, vacío o no vienen:
- turismo_por_alojamiento
- turismo_salida_pais_via_aerea
- fovial
- contrans
- bebidas_alcoholicas
- tabaco_cigarillos
- tabaco_cigarros
- total_ventas_gravadas
- total_ventas_exentas
- total_ventas_no_sujetas
- descuento_venta_gravada
- descuento_venta_exenta
- descuento_venta_no_sujeta
- total_descuentos
- iva
- total_taxes
- subtotal
- total
- total_pagar
- monto_abonado
- monto_pendiente
- total_iva_retenido
- isr
- flete
- seguro

Campos bandera inicializados si no vienen:
- retener_iva: null
- retener_renta: null
- percibir_iva: null
- remove_discounts: null

Arreglos inicializados si no vienen:
- products: []
- metodos_pago: []
- documentos_relacionados: []
- otros_documentos: []

Customer inicializado con nulls en:
- tipoDocumento
- numDocumento
- nrc
- nombre
- nombreComercial
- codActividad
- descActividad
- departamento
- municipio
- complemento
- telefono
- correo
- tipoPersona

## Recomendación de payload mínimo seguro por tipo

Común mínimo:
- dte.type
- dte.customer con tipoDocumento, numDocumento, nombre, departamento, municipio
- dte.products (excepto 07)
- dte.monto_abonado
- dte.metodos_pago (para cuadrar monto_abonado vs total_pagar)

Adicional por tipo:
- 04: dte.bienTitulo
- 05/06: products[].documento_relacionado + customer.nrc
- 07: dte.documentos_retencion (en lugar de products)
- 11: customer.pais + customer.tipoPersona + emisor.tipoItemExpor
- 15: dte.otros_documentos + products con campos de donación + metodos_pago cuando corresponda

## Respuestas

### 200 OK
Retorna el JSON del procesamiento interno/Octopus, por ejemplo:

{
  "estado": "PROCESADO",
  "codGeneracion": "A1B2C3",
  "selloRecibido": "...",
  "fechaHora": "2026-02-11T12:30:00"
}

### 401 Unauthorized

{
  "success": false,
  "message": "Unauthorized."
}

### 422 Validación o negocio

{
  "success": false,
  "message": "Tipo de DTE no soportado o ausente."
}

O bien respuesta de negocio convertida desde flujo interno, por ejemplo:

{
  "estado": "RECHAZADO",
  "observaciones": "Debe agregar al menos un producto"
}

### 500 Error interno

{
  "success": false,
  "message": "Error al procesar el DTE desde JSON."
}

## Versión rápida para integradores (OpenAPI-friendly)

Esta sección resume el contrato en formato corto, orientado a implementación de clientes API.

### 1) Esquema resumido

Path: /api/submitDte
Method: POST
Auth: Authorization Bearer o ApiKey
Request body requerido: objeto con propiedad dte (object)

### 2) Campos dte de alto nivel

| Campo | Tipo | Requerido | Nullable | Regla principal |
|---|---|---|---|---|
| dte.type | string | Sí | No | Uno de 01,03,04,05,06,07,11,14,15 |
| dte.customer | object | Recomendado Sí | No | Si no viene, se crea objeto vacío |
| dte.products | array | Sí salvo type 07 | No | Para type distinto de 07 debe tener al menos 1 item |
| dte.metodos_pago | array | Condicional | No | Necesario para cuadrar monto_abonado en type distinto de 04 |
| dte.monto_abonado | number | Condicional | No | Debe igualar total_pagar en type distinto de 04 |
| dte.documentos_relacionados | array | No | No | Si se envía, cada documento debe estar ligado a un producto |
| dte.otros_documentos | array | Sí en type 15 | No | En type 15 al menos un documento asociado |
| dte.documentos_retencion | array | Sí en type 07 | Sí | Base de cuerpoDocumento para comprobante de retención |
| dte.bienTitulo | string | Sí en type 04 | Sí | Catálogo CAT-025 |
| dte.condicion_operacion | string | No | No | Default 1 cuando no viene |

### 3) Customer mínimo recomendado

| Campo | Tipo | Requerido | Nullable | Regla |
|---|---|---|---|---|
| customer.tipoDocumento | string | Sí | Sí | Códigos: 02,03,13,36,37 o alias texto |
| customer.numDocumento | string | Sí | Sí | Se normaliza según tipoDocumento |
| customer.nombre | string | Sí | Sí | Nombre de receptor |
| customer.nrc | string | Condicional | Sí | Solo dígitos, límite funcional 8 |
| customer.codActividad | string | Recomendado | Sí | CAT-019 |
| customer.departamento | string | Recomendado | Sí | CAT-012; default 06 |
| customer.municipio | string | Recomendado | Sí | CAT-012 por departamento; default 01 |
| customer.telefono | string | No | Sí | Sin validación estricta backend |
| customer.correo | string | No | Sí | Sin validación estricta backend |
| customer.tipoPersona | string | Type 11 recomendado Sí | Sí | Natural/Jurídica o código |
| customer.pais | string | Type 11 recomendado Sí | Sí | CAT-020 |

### 4) Product mínimo recomendado

| Campo | Tipo | Requerido | Nullable | Regla |
|---|---|---|---|---|
| products[].descripcion | string | Sí | No | Descripción del ítem |
| products[].cantidad | number | Sí | No | Mayor que 0 recomendado |
| products[].tipo | string | Sí | No | Gravada, Exenta o No sujeta |
| products[].precio | number | Sí | No | Precio unitario mostrado |
| products[].precio_sin_tributos | number | Sí | No | Base sin IVA |
| products[].total | number | Sí | No | Total de línea |
| products[].descuento | number | No | No | Default funcional 0 |
| products[].iva | number | No | No | Relevante en gravadas |
| products[].tipo_item | number/string | Recomendado Sí | Sí | 1 bien, 2 servicio |
| products[].documento_relacionado | string | Sí en type 05 y 06 | Sí | Debe correlacionar con documentos_relacionados |

### 5) Dependencias por type

| Type | Campos críticos adicionales |
|---|---|
| 01 | customer normalizado; si tipoDocumento es 13 y nrc existe, se fuerza a 36 |
| 03 | customer.nrc requerido funcional; receptor usa nit+nrc |
| 04 | dte.bienTitulo requerido |
| 05 | customer.nrc requerido funcional; products[].documento_relacionado requerido funcional |
| 06 | customer.nrc requerido funcional; products[].documento_relacionado requerido funcional |
| 07 | dte.documentos_retencion requerido; products no requerido |
| 11 | customer.pais y customer.tipoPersona recomendados; emisor.tipoItemExpor requerido funcional |
| 14 | DUI se envía sin guion |
| 15 | dte.otros_documentos requerido; items de donación específicos; pagos cuando hay donación monetaria |

### 6) Catálogos aplicables

| Catálogo | Uso |
|---|---|
| CAT-014 | Unidad de medida |
| CAT-012 | Departamento y municipio |
| CAT-022 | Tipo de documento |
| CAT-019 | Actividad económica |
| CAT-020 | País |
| CAT-027 | Recinto fiscal |
| CAT-028 | Régimen de exportación |
| CAT-017 | Forma de pago |
| CAT-030 | Modo de transporte |
| CAT-031 | Incoterms |
| CAT-025 | Bienes remitidos a título de |

### 7) Plantilla JSON rápida (base integrador)

{
  "dte": {
    "type": "01",
    "customer": {
      "tipoDocumento": "13",
      "numDocumento": "01234567-8",
      "nrc": null,
      "nombre": "Cliente Demo",
      "codActividad": "001",
      "departamento": "06",
      "municipio": "01",
      "correo": "cliente@demo.com"
    },
    "products": [
      {
        "descripcion": "Producto 1",
        "cantidad": 1,
        "tipo": "Gravada",
        "precio": 10.0,
        "precio_sin_tributos": 8.85,
        "total": 10.0,
        "descuento": 0,
        "iva": 1.15,
        "tipo_item": 1
      }
    ],
    "metodos_pago": [
      {
        "forma_pago": "01",
        "monto": 10.0,
        "numero_documento": null,
        "plazo": null,
        "periodo": null
      }
    ],
    "monto_abonado": 10.0
  }
}

### 8) Checklist de validación previo al envío

- type válido
- Para type distinto de 07: products con al menos un item
- Para type 04: bienTitulo presente
- Para type 05 y 06: documento_relacionado por item
- Para type 07: documentos_retencion presente
- Para type 15: otros_documentos presente
- monto_abonado igual a total_pagar cuando type distinto de 04
- Si se envía documentos_relacionados, cada uno referenciado por un producto
