# Guía rápida para agentes de IA en este repo

Proyecto Laravel 11 con Livewire 3 + Vite + Tailwind orientado a facturación electrónica (DTE). La UI es Blade + jQuery/axios con recargas parciales vía HTML renderizado en el backend.

## Arquitectura y flujo de datos
- Rutas claves en `routes/business.php` bajo prefijo `business` con `middleware(['auth','role:business','web'])`. Ej.: DTE, Productos, Reporting, POS.
- Controladores en `app/Http/Controllers/Business/*`. Patrón típico: leer/modificar `session('dte')`, recalcular totales y devolver JSON con fragmentos HTML renderizados.
- Frontend en `resources/js/*` con punto de entrada `app.js`. El archivo `dte.js` orquesta la mayoría de acciones (selección cliente/producto, descuentos, retenciones, exportación) usando jQuery + axios + Livewire events.
- Vistas parciales AJAX en `resources/views/layouts/partials/ajax/business/*` que el backend devuelve como strings para reemplazo directo en el DOM.

## Contrato de `session('dte')` (resumen práctico)
- Estructura principal: `products: []`, `type`, totales y banderas.
- Por producto (array `products`): `id`, `product_id`, `descripcion`, `cantidad`, `tipo` ('Gravada'|'Exenta'|'No sujeta'), `total`, `descuento`, `precio`, `precio_sin_tributos`, `ventas_gravadas|exentas|no_sujetas`, `iva`, banderas de tributos (`turismo_*`, `fovial`, `contrans`, `bebidas_alcoholicas`, `tabaco_*`), `documento_relacionado`, opcional `tipo_item`.
- Totales: `total_ventas_gravadas|exentas|no_sujetas`, `subtotal`, `total_taxes`, `iva`, `total_descuentos`, `total`, `total_pagar`, `monto_abonado`, `monto_pendiente`.
- Retenciones/percibidos: `retener_iva`, `retener_renta`, `percibir_iva`, `total_iva_retenido`, `isr`.
- Exportación: `flete`, `seguro`. Descuentos por porcentaje: `percentaje_descuento_venta_*` y flags `remove_discounts`.

## Respuestas JSON esperadas por la UI (claves comunes)
- `success` (bool), `message` (string opcional).
- Fragmentos HTML para reemplazo directo: `table_products`, `table_data` + `table` (id base), `table_exportacion`, `table_sujeto_excluido`, `table_selected_product`, `total_discounts`.
- Cierre UI: `modal` o `drawer` con el id del elemento a cerrar.
- Números: `total_pagar`, `monto_pendiente`, `total_iva_retenido_texto` (cuando aplica).
- Ejemplo: en `DTEProductController::store` se devuelve `table_products` y `table_exportacion` renderizados con `view(...)->render()`.

## Convenciones frontend (dte.js)
- Eventos: usa `change` y también un evento custom `Changed` para selects personalizados (ver `customSelect`).
- Elementos clave: `#loader`, `#overlay`, contenedores tipo `#table-products-dte`, `#table-exportacion`, inputs con ids fijos (p.ej. `#monto_total`).
- Patrones: botones `.submit-form` serializan y envían formularios por axios; en éxito, actualizan contenedores por id y cierran modal/drawer según claves en la respuesta.

## Flujo DTE (patrón de implementación)
1. Leer/modificar `session('dte')` y asegurar inicialización (ver `total_init`).
2. Actualizar `products` o parámetros (descuentos, retenciones, flete/seguro).
3. Llamar `totals()` para recalcular: ventas, descuentos, impuestos, retenciones, total a pagar.
4. Guardar con `session(['dte' => $this->dte])` y devolver JSON con fragmentos HTML a reemplazar.

## Build, dev y tests
- Instalar deps: `composer install` y `npm install`.
- Dev local (recomendado): `composer dev` (ejecuta en paralelo `php artisan serve`, `queue:listen`, `pail` y `vite`). Alternativa: `php artisan serve` + `npm run dev`.
- Build assets: `npm run build` (usa `vite.config.js` con entradas: css/js definidos).
- Tests: `php artisan test` o `vendor/bin/phpunit` (config en `phpunit.xml`, drivers in-memory para cache/sesión/colas por defecto en testing).

## Estilo y assets
- Tailwind configurado con `darkMode: 'class'` y color `primary` personalizado (`tailwind.config.js`). Plugins: `flowbite`, `tailwindcss-motion`, `tailwindcss-animated`.
- Si agregas un nuevo entry JS/CSS, inclúyelo en `vite.config.js -> laravel({ input: [...] })` y referencia con `@vite([...])` en Blade.

## Integraciones y servicios
- `AppServiceProvider` vincula `App\Services\OctopusService` vía contenedor IoC; inyéctalo por tipo si necesitas su uso.
- Importación/Exportación por `maatwebsite/excel`; permisos por `spatie/laravel-permission` (`role:business` en rutas).

## Ejemplos de ubicaciones clave
- Controladores DTE/productos: `app/Http/Controllers/Business/DTE*Controller.php`.
- JS principal de DTE: `resources/js/dte.js` (lee/actualiza DOM y dispara endpoints).
- Parciales reemplazables: `resources/views/layouts/partials/ajax/business/*`.

¿Algo no quedó claro o falta algún flujo (por ejemplo, envíos a Hacienda u OctopusService)? Indícame qué sección ajustar y lo incorporo enseguida.