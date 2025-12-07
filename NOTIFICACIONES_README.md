# Sistema de Notificaciones por Correo Electrónico

## 📋 Descripción

Sistema completo de notificaciones personalizadas por correo electrónico que permite enviar mensajes masivos a clientes y usuarios del sistema. Incluye editor HTML, selección múltiple de destinatarios, procesamiento en segundo plano con colas y seguimiento de progreso en tiempo real.

## 🚀 Características

- ✉️ **Editor HTML Rico**: Utiliza TinyMCE para crear contenido formateado
- 👥 **Selección Múltiple**: Envía a clientes (negocios), usuarios del sistema o correos personalizados
- ⚡ **Procesamiento en Colas**: Los envíos se procesan en segundo plano sin afectar la UI
- 📊 **Progreso en Tiempo Real**: Monitorea el estado de los envíos en vivo
- 🔄 **Persistencia**: El progreso se mantiene aunque se recargue la página
- 📧 **Plantilla Profesional**: Usa el mismo diseño del sistema con logo y pie de página
- 📈 **Historial**: Visualiza envíos recientes con estadísticas

## 📦 Archivos Creados

### Backend
- `app/Http/Controllers/Admin/NotificationController.php` - Controlador principal
- `app/Jobs/SendBulkNotificationEmail.php` - Job para envío masivo
- `app/Mail/CustomNotificationMail.php` - Clase Mailable

### Frontend
- `resources/views/admin/notifications/index.blade.php` - Listado e historial
- `resources/views/admin/notifications/create.blade.php` - Formulario de creación
- `resources/views/mail/custom-notification.blade.php` - Template del correo

### Rutas
- Agregadas en `routes/admin.php`

## ⚙️ Configuración

### 1. Configurar el Sistema de Colas

Edita `.env`:

```env
QUEUE_CONNECTION=database
```

### 2. Crear Tabla de Jobs

```bash
php artisan queue:table
php artisan migrate
```

### 3. Iniciar el Worker de Colas

En desarrollo:
```bash
php artisan queue:work
```

En producción (con Supervisor):
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

### 4. Configurar el Servidor de Correo

Asegúrate de tener configurado correctamente tu servidor SMTP en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Configurar Cache

El sistema usa caché para almacenar el progreso:

```env
CACHE_DRIVER=file  # o redis para mejor rendimiento
```

## 🎨 Uso

### Acceso al Sistema

1. Navega a `/admin/notifications` para ver el historial
2. Click en "Nueva Notificación" para crear un envío

### Crear una Notificación

1. **Asunto**: Ingresa el título del correo
2. **Contenido**: Usa el editor HTML para diseñar tu mensaje
3. **Destinatarios**: 
   - Selecciona tipo (Clientes, Usuarios o Personalizado)
   - Marca los destinatarios deseados
   - O ingresa correos manualmente (separados por comas o líneas)
4. **Enviar**: El sistema procesa en segundo plano

### Monitorear el Progreso

- Los envíos aparecen en el historial con estado en tiempo real
- Click en el ícono de "ojo" para ver progreso detallado
- El modal se actualiza cada 2 segundos automáticamente
- Estados: Pendiente → Procesando → Completado/Fallido

## 🔧 Personalización

### Cambiar el Logo

Edita la URL del logo en:
- `resources/views/mail/custom-notification.blade.php` (líneas 13 y 49)

Reemplaza:
```html
<img src="https://facturacion-pruebas.konverza.digital/images/only-icon.png" ...>
```

### Modificar la Plantilla

El archivo `custom-notification.blade.php` mantiene la misma estructura que el template de DTEs. Puedes personalizar:
- Colores del encabezado
- Estilos del contenido
- Texto del pie de página

### Ajustar Límites de Envío

En `SendBulkNotificationEmail.php`, línea 72:
```php
usleep(100000); // 0.1 segundos entre envíos
```

Aumenta el valor si tu servidor de correo tiene límites de tasa.

## 📊 Estructura de Datos

### Cache de Jobs
```php
[
    'notification_xxx' => [
        'id' => 'notification_xxx',
        'subject' => 'Asunto del correo',
        'total' => 100,
        'sent' => 75,
        'failed' => 2,
        'status' => 'processing', // pending|processing|completed|failed
        'created_at' => '2025-12-06 10:30:00',
        'created_by' => 'Admin User',
        'completed_at' => null,
        'error' => null
    ]
]
```

## 🛡️ Seguridad

- ✅ Validación CSRF en todos los formularios
- ✅ Middleware de autenticación y rol admin
- ✅ Validación de formato de correos electrónicos
- ✅ Sanitización de contenido HTML (TinyMCE)
- ✅ Logs de errores en `storage/logs/laravel.log`

## 🐛 Troubleshooting

### Los correos no se envían

1. Verifica que el worker de colas esté corriendo:
   ```bash
   php artisan queue:work
   ```

2. Revisa los logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Prueba el servidor SMTP:
   ```bash
   php artisan tinker
   Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
   ```

### El progreso no se actualiza

1. Verifica que el caché funcione:
   ```bash
   php artisan cache:clear
   ```

2. Asegúrate que el worker procese trabajos:
   ```bash
   php artisan queue:listen --verbose
   ```

### Error de permisos

Asegúrate que los directorios tengan permisos correctos:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📝 Notas Adicionales

- Los datos del historial se mantienen en caché por 7 días
- El sistema usa un sleep de 0.1 segundos entre envíos para evitar saturación
- TinyMCE se carga desde CDN (sin API key en modo básico)
- Compatible con modo claro y oscuro del sistema

## 🔗 Enlaces Útiles

- [Documentación de Laravel Queues](https://laravel.com/docs/queues)
- [TinyMCE Documentation](https://www.tiny.cloud/docs/)
- [Laravel Mail](https://laravel.com/docs/mail)

## 👨‍💻 Autor

Sistema desarrollado para Konverza Digital - Facturación Electrónica

---

**Versión**: 1.0.0  
**Fecha**: Diciembre 2025
