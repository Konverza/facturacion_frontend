@extends('layouts.template')
@section('title', 'Servidor No Disponible')
@section('content')
    <section class="flex min-h-screen items-center justify-center">
        <div class="px-4 py-8 text-center lg:px-40">
            <h1 class="mb-4 text-7xl font-extrabold text-primary-500 dark:text-primary-300 lg:text-9xl">Mantenimiento</h1>
            <p class="mb-4 text-3xl font-bold tracking-tight dark:text-gray-300 md:text-4xl">
                El Servidor no está disponible.
            </p>
            <p class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-400 md:text-xl">
                Actualmente estamos en mantenimiento, por favor, vuelve más tarde.
            </p>
            <p class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-400 md:text-xl">
                El mantenimiento finalizará en: <span id="eta"></span>
            </p>
            <div class="mb-8 animate-bounce">
                <svg class="mx-auto h-16 w-16 text-gray-800 dark:text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Set the date we're counting down to
            var countDownDate = new Date(2026, 0, 28, 12, 15, 0).getTime();

            // Update the count down every 1 second
            var x = setInterval(function() {

                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for minutes and seconds
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="eta"
                document.getElementById("eta").innerHTML = minutes + "m " + seconds + "s ";

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("eta").innerHTML = "EXPIRED";
                }
            }, 1000);
        });
    </script>
@endsection
