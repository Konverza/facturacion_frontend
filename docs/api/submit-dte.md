# API Submit DTE

## Endpoint
- Method: POST
- URL: /api/submitDte

## Authentication
- Header: Authorization: Bearer {API_KEY}
- The API key is generated per business in the admin panel.

## Request
- Content-Type: application/json
- Body:

```json
{
  "dte": {
    "type": "01",
    "customer": {
      "tipoDocumento": "13",
      "numDocumento": "01234567-8",
      "nrc": null,
      "nombre": "Cliente Demo",
      "nombreComercial": null,
      "codActividad": "001",
      "descActividad": "Comercio al por menor",
      "departamento": "06",
      "municipio": "19",
      "complemento": "San Salvador",
      "telefono": "22223333",
      "correo": "cliente@demo.com",
      "tipoPersona": "01"
    },
    "products": [
      {
        "product": { "id": 123 },
        "descripcion": "Producto 1",
        "cantidad": 1,
        "tipo": "Gravada",
        "precio": 10.00,
        "precio_sin_tributos": 8.85,
        "total": 10.00,
        "descuento": 0,
        "iva": 1.15
      }
    ]
  }
}
```

Notes:
- The JSON must follow the same structure used by submitFromJson.
- The "type" key accepts DTE types like "01", "03", "11", "14", etc.
- The "products" array is required for most DTE types.

## Response
The response mirrors the internal DTE processing result and the Octopus API response.

### Success (200)
```json
{
  "estado": "PROCESADO",
  "codGeneracion": "A1B2C3",
  "selloRecibido": "...",
  "fechaHora": "2026-02-11T12:30:00"
}
```

### Validation or business error (422)
```json
{
  "success": false,
  "message": "Tipo de DTE no soportado o ausente."
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized."
}
```

### Server error (500)
```json
{
  "success": false,
  "message": "Error al procesar el DTE desde JSON."
}
```
