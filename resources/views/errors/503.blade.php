@extends('layouts.template')
@section('title', 'Servidor No Disponible')
@section('content')
    <section class="flex min-h-screen items-center justify-center md:h-screen md:overflow-hidden">
        <div class="flex h-full w-full max-w-6xl flex-col items-center justify-center md:flex-row">
            <div class="h-[40vh] w-full md:h-auto md:w-1/4">
                <img src="{{ asset('images/maintenance.png') }}" alt="Mantenimiento" class="h-full w-full object-contain" />
            </div>
            <div class="flex w-full flex-col items-center justify-center px-4 py-6 text-center md:w-7/12 md:px-8 lg:px-12">
                <h1 class="mb-4 text-5xl font-extrabold text-primary-500 dark:text-primary-300 lg:text-7xl">Mantenimiento
                </h1>
                <p class="mb-4 text-3xl font-bold tracking-tight dark:text-gray-300 md:text-4xl">
                    El sistema no está disponible.
                </p>
                <p class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-400 md:text-xl">
                    Actualmente estamos en mantenimiento, por favor, vuelve más tarde.
                </p>
                <p class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-400 md:text-xl">
                    El mantenimiento finalizará aproximadamente en: <span id="eta"></span>
                </p>
            </div>
        </div>
    </section>
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var dateString = "{{ config('app.maintenance_end') }}";
                var countDownDate = new Date(dateString).getTime();
                var etaElement = document.getElementById("eta");

                if (!dateString || isNaN(countDownDate)) {
                    etaElement.innerHTML = "En breve";
                    return;
                }

                function renderEta() {
                    var now = new Date().getTime();
                    var distance = countDownDate - now;

                    if (distance <= 0) {
                        etaElement.innerHTML = "En breve";
                        return false;
                    }

                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    var parts = [];
                    if (hours > 0) parts.push(hours + "h");
                    if (minutes > 0) parts.push(minutes + "m");
                    if (seconds > 0) parts.push(seconds + "s");

                    etaElement.innerHTML = parts.length ? parts.join(" ") : "En breve";

                    return true;
                }

                renderEta();

                var x = setInterval(function() {
                    var shouldContinue = renderEta();
                    if (!shouldContinue) {
                        clearInterval(x);
                    }
                }, 1000);
            });
        </script>
    @endpush
@endsection
