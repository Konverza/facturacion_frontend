import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/autocomplete.js',

                'resources/js/admin.js',
                'resources/js/invoices.js',
                'resources/js/clients.js',
                'resources/js/producto.js',
                'resources/js/customers.js',
                'resources/js/business_dashboard.js',

                'resources/js/factura.js',
                'resources/js/credito_fiscal.js',
            ],
            refresh: true,
        }),
    ],
    // server: {
    //     https: true, // Asegura que Vite use HTTPS
    // },
});
