<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ env('APP_NAME') }} | @yield('title')
    </title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/hugerte@1/hugerte.min.js"></script>
    <script>
        // Configura CSRF para axios y jQuery usando la meta tag
        (function() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (window.axios) {
                if (token) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                }
                // Asegura compatibilidad con Laravel (cookie XSRF-TOKEN -> cabecera X-XSRF-TOKEN)
                window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
                window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
                window.axios.defaults.withCredentials = true; // same-origin: envía cookies
            }
            if (window.jQuery && token) {
                window.jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
            }
            // Expone global por si se necesita en scripts inline
            window.csrfToken = token || '';
        })();
    </script>
    <script>
        (function() {
            let theme = localStorage.getItem('theme') || "light"
            if (theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                        '(prefers-color-scheme: dark)')
                    .matches)) {
                document.documentElement.classList.add('dark');

            } else {
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    @livewireStyles
</head>

<body class="h-screen">
    <div
        class="fixed inset-0 -z-10 block h-full w-screen bg-white bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] dark:hidden">
    </div>

    <div class="fixed -z-10 hidden h-screen w-full bg-slate-950 dark:block">
        <div
            class="absolute bottom-0 left-0 right-0 top-0 bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] bg-[size:14px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]">
        </div>
    </div>

    @include('layouts.partials.loader')
    @include('layouts.partials.overlay')

    @if (auth()->user()->hasRole('admin'))
        @include('layouts.partials.admin.navbar')
    @else
        @include('layouts.partials.business.navbar')
    @endif

    @include('layouts.partials.alerts-container', ['class' => 'top-4 right-4 left-4'])

    <main class="@if (auth()->user()->hasRole('admin')) admin @else business @endif">
        @include('layouts.partials.breadcrumb')
        @include('layouts.partials.alert')
        @yield('content')
    </main>
    @livewireScripts
</body>
<script>
    (function() {
        let skipBeforeUnload = false;

        function hideLoader() {
            const loader = document.getElementById("loader");
            if (loader) {
                loader.classList.add("hidden");
            }
        }

        // Oculta el loader al cargar la página o al restaurarla desde el bfcache
        window.addEventListener("pageshow", function() {
            hideLoader();
        });

        // Si el usuario vuelve al foco luego de iniciar una descarga, ocultar loader
        window.addEventListener("focus", function() {
            if (skipBeforeUnload) {
                hideLoader();
                setTimeout(() => {
                    skipBeforeUnload = false;
                }, 300);
            }
        });

        // Marcar descargas para evitar el loader de navegación
        document.addEventListener("click", function(event) {
            const downloadTrigger = event.target.closest(
                "a.download-zip-link, a[data-download='true'], button[data-download='true']"
            );

            if (downloadTrigger) {
                skipBeforeUnload = true;
                // Fallback por si no hay cambio de foco
                setTimeout(hideLoader, 1500);
            }
        });

        // Muestra el loader al intentar salir o recargar la página
        window.addEventListener("beforeunload", function() {
            if (skipBeforeUnload) {
                return;
            }
            const loader = document.getElementById("loader");
            if (loader) {
                loader.classList.remove("hidden");
            }
        });
    })();
</script>
@stack('scripts')

</html>
