<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
</body>
<script>
    // Oculta el loader al cargar la página o al restaurarla desde el bfcache
    window.addEventListener("pageshow", function(event) {
        console.log("Página mostrada o restaurada desde caché.");
        document.getElementById("loader").classList.add("hidden");
    });

    // Muestra el loader al intentar salir o recargar la página
    window.addEventListener("beforeunload", function() {
        console.log("Página a punto de recargarse...");
        document.getElementById("loader").classList.remove("hidden");
    });
</script>
@stack('scripts')

</html>
