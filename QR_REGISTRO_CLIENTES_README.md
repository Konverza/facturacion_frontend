# Funcionalidad de Registro Público de Clientes con QR

## Descripción

Esta funcionalidad permite a los negocios compartir un código QR con sus clientes para que estos puedan registrarse ellos mismos en el sistema, cumpliendo con las disposiciones del Ministerio de Hacienda para la implementación de facturación electrónica.

## Características Implementadas

### 1. Botón "Compartir mi QR" en el Índice de Clientes

En la vista de clientes (`resources/views/business/customers/index.blade.php`), se agregó un botón que permite al negocio acceder al código QR.

### 2. Modal con QR y Opciones de Compartir

El modal incluye:
- Imagen del QR generada dinámicamente
- Botón para descargar la imagen del QR
- Botón para compartir el link de registro
- Input para copiar el link directo

### 3. Generación de Imagen QR

**Ruta:** `business/customers/qr/image`  
**Método:** `CustomerController::generateQRImage()`

La imagen incluye:
- Nombre de la empresa (en grande)
- Título "REGISTRO DE CLIENTES"
- Texto explicativo sobre facturación electrónica
- Código QR que apunta a la URL de registro
- Footer con "Con la tecnología de Konverza" y logo

**Características técnicas:**
- La imagen se genera usando GD Library de PHP
- Se cachea por 30 días para optimizar rendimiento
- Dimensiones: 1000x1400 px
- Formato: PNG
- Usa fuentes DejaVu Sans (TTF) si están disponibles, sino usa fuentes por defecto

### 4. Formulario Público de Registro

**URL:** `/registro-clientes/{nit}`  
**Método:** `CustomerController::showPublicRegistration()`

El formulario público incluye todos los campos necesarios:
- Tipo de documento
- Número de documento
- Nombre/Razón social
- NRC (opcional)
- Nombre comercial (opcional)
- Actividad económica
- Departamento y Municipio (con carga dinámica)
- Dirección
- Correo electrónico (opcional)
- Teléfono (opcional)
- Datos de exportación (opcionales)

**Características:**
- Usa el layout público (sin menús de autenticación)
- Valida que el NIT del negocio exista
- Si el NIT no existe, muestra error y link a Konverza
- Carga dinámica de municipios según departamento seleccionado
- Muestra mensajes de éxito/error

### 5. Procesamiento del Registro Público

**Ruta:** `POST /registro-clientes/{nit}`  
**Método:** `CustomerController::storePublicRegistration()`

**Funcionalidad:**
- Valida el NIT del negocio
- Valida formato de documentos (NIT: 14 dígitos, DUI: formato ########-#)
- Verifica si el cliente ya existe (por número de documento)
- Si existe, actualiza los datos
- Si no existe, crea un nuevo cliente
- Transaccional (usa DB::beginTransaction())
- Los clientes registrados públicamente tienen `special_price = false` y `use_branches = false`

### 6. API Pública de Municipios

**Ruta:** `/api/municipios/{departamento}`  
**Método:** `CustomerController::getMunicipios()`

Endpoint público para obtener los municipios de un departamento, usado por el formulario de registro.

## Archivos Modificados/Creados

### Archivos Modificados

1. **resources/views/business/customers/index.blade.php**
   - Agregado botón "Compartir mi QR"
   - Agregado modal con imagen QR y opciones de compartir
   - Scripts para manejar copiar link

2. **app/Http/Controllers/Business/CustomerController.php**
   - `generateQRImage()`: Genera y cachea la imagen del QR
   - `showPublicRegistration()`: Muestra el formulario público
   - `storePublicRegistration()`: Procesa el registro público

3. **routes/business.php**
   - Agregada ruta: `GET /business/customers/qr/image`

4. **routes/web.php**
   - Agregada ruta: `GET /registro-clientes/{nit}`
   - Agregada ruta: `POST /registro-clientes/{nit}`
   - Agregada ruta: `GET /api/municipios/{departamento}`

5. **composer.json**
   - Agregada dependencia: `simplesoftwareio/simple-qrcode`

### Archivos Creados

1. **resources/views/registro-clientes.blade.php**
   - Vista pública del formulario de registro de clientes

## Dependencias Instaladas

```bash
composer require simplesoftwareio/simple-qrcode
```

Esta librería se usa para generar el código QR que se incluye en la imagen.

## Uso

### Para el Negocio

1. Ir a la sección de "Clientes"
2. Hacer clic en el botón "Compartir mi QR"
3. Opciones disponibles:
   - Descargar la imagen del QR
   - Compartir el link directo
   - Copiar el link al portapapeles

### Para el Cliente Final

1. Escanear el código QR o acceder al link compartido
2. Completar el formulario con sus datos
3. Hacer clic en "Registrar mis datos"
4. Si el registro es exitoso, verá un mensaje de confirmación

## Validaciones Implementadas

- **NIT del negocio:** Debe existir en la base de datos
- **Número de documento:**
  - NIT (tipo 36): Debe tener exactamente 14 dígitos
  - DUI (tipo 13): Debe tener formato ########-# (8 dígitos, guion, 1 dígito)
- **Campos requeridos:** Tipo de documento, número de documento, nombre, actividad económica, departamento, municipio, dirección
- **Actualización:** Si el cliente ya existe (mismo número de documento), se actualizan sus datos

## Cache

La imagen del QR se cachea con la siguiente estrategia:
- **Key:** `qr_image_{business_id}`
- **Duración:** 30 días
- **Invalidación:** Automática después de 30 días o limpiando el cache manualmente

Para limpiar el cache de un negocio específico:
```php
Cache::forget('qr_image_' . $business_id);
```

## Estructura de la URL de Registro

```
https://tu-dominio.com/registro-clientes/{nit}
```

Donde `{nit}` es el NIT del negocio registrado en el sistema.

## Mensajes al Usuario

### Éxito
- Cliente nuevo: "Sus datos han sido registrados correctamente. ¡Gracias!"
- Cliente existente: "Sus datos han sido actualizados correctamente. ¡Gracias!"

### Error
- NIT inválido: "El NIT proporcionado no es válido o no está registrado en nuestro sistema."
- Error en validación: Mensajes específicos según el campo
- Error del servidor: "Ha ocurrido un error al registrar sus datos. Por favor, intente nuevamente."

## Consideraciones de Seguridad

1. **Sin autenticación requerida:** El formulario es público por diseño
2. **Validación de NIT:** Se valida que el negocio exista antes de mostrar el formulario
3. **Transacciones:** El registro usa transacciones de base de datos para garantizar integridad
4. **Sanitización:** Laravel sanitiza automáticamente los inputs
5. **Rate limiting:** Se recomienda agregar throttling a las rutas públicas

## Mejoras Futuras Sugeridas

1. Agregar CAPTCHA para prevenir spam
2. Implementar rate limiting en las rutas públicas
3. Enviar email de confirmación al cliente después del registro
4. Notificar al negocio cuando un cliente se registra
5. Permitir personalizar el texto de la imagen QR por negocio
6. Agregar Analytics para trackear cuántos clientes se registran vía QR
7. Permitir regenerar el QR con diferentes diseños/colores

## Troubleshooting

### La imagen QR no se genera
- Verificar que la extensión GD de PHP esté habilitada
- Verificar permisos de escritura en `storage/app/`
- Revisar logs en `storage/logs/laravel.log`

### Los municipios no cargan
- Verificar que la ruta `/api/municipios/{departamento}` esté accesible
- Verificar que OctopusService esté funcionando correctamente
- Revisar la consola del navegador para errores JavaScript

### El formulario no guarda
- Verificar que el NIT del negocio exista
- Revisar validaciones en el navegador (campos requeridos)
- Verificar logs del servidor para errores de base de datos

## Testing

Para probar la funcionalidad:

1. **Probar generación de QR:**
   ```
   GET /business/customers/qr/image
   ```

2. **Probar formulario público:**
   ```
   GET /registro-clientes/{nit-valido}
   ```

3. **Probar con NIT inválido:**
   ```
   GET /registro-clientes/123456789
   ```

4. **Probar registro:**
   - Completar el formulario con datos válidos
   - Verificar que el cliente se cree en la base de datos
   - Intentar registrar el mismo cliente nuevamente (debe actualizar)

## Soporte

Para cualquier problema o pregunta sobre esta funcionalidad, contactar al equipo de desarrollo de Konverza.
