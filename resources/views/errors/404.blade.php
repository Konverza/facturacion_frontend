@extends('layouts.template')
@section('title', 'Página no encontrada')
@section('content')
    <section class="flex min-h-screen items-center justify-center">
        <div class="px-4 py-8 text-center lg:px-40">
            <h1 class="mb-4 text-7xl font-extrabold text-primary-500 dark:text-primary-300 lg:text-9xl">404</h1>
            <p class="mb-4 text-3xl font-bold tracking-tight dark:text-gray-300 md:text-4xl">
                ¡Ups! Página no encontrada.
            </p>
            <p class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-400 md:text-xl">
                Lo sentimos, no pudimos encontrar la página que estás buscando.
            </p>
            <div class="mb-8 animate-bounce">
                <svg class="mx-auto h-16 w-16 text-gray-800 dark:text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </div>
            <div class="flex items-center justify-center">
                <x-button type="a" typeButton="primary" text="Volver al inicio" icon="home"
                    href="{{ url()->previous() }}" />
            </div>
        </div>
    </section>
@endsection
