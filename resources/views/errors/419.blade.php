@extends('layouts.template')
@section('title', 'Sesión expirada')
@section('content')
    <section class="flex min-h-screen items-center justify-center">
        <div class="px-4 py-8 text-center lg:px-40">
            <div class="mb-4 flex items-center justify-center">
                <x-icon icon="shield" class="h-16 w-16 text-yellow-400" />
            </div>
            <p
                class="mb-4 flex items-center justify-center gap-2 text-xl font-bold tracking-tight dark:text-secondary-300 md:text-4xl">
                <x-icon icon="lock" class="inline-block size-8" />
                Sesión expirada
            </p>
            <p class="mb-4 text-lg font-medium text-secondary-800 dark:text-secondary-400 md:text-xl">
                Tu sesión ha expirado por motivos de seguridad.
            </p>
            <div class="my-4 flex flex-col justify-between overflow-hidden rounded-lg border-2 border-dashed border-yellow-300 bg-yellow-50 p-4 text-center dark:border-yellow-800 dark:bg-yellow-950/40"
                role="alert">
                <div class="flex justify-start gap-2 text-sm text-yellow-800 dark:text-yellow-200">
                    <x-icon icon="alert-triangle" class="h-5 w-5" />
                    ¿Por qué ocurre esto?
                </div>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    <ul class="list-disc space-y-1 pl-5 text-left">
                        <li>Has estado inactivo por mucho tiempo</li>
                        <li>Tu token de seguridad (CSRF) ha caducado</li>
                        <li>
                            La página se mantuvo abierta por más de {{ config('session.lifetime', 240) / 60 }} horas
                        </li>
                        <li>Se detectó actividad sospechosa</li>
                    </ul>
                </div>
            </div>

            <div class="my-4 flex flex-col justify-between overflow-hidden rounded-lg border-2 border-dashed border-blue-300 bg-blue-50 p-4 text-center dark:border-blue-800 dark:bg-blue-950/40"
                role="alert">
                <div class="flex justify-start gap-2 text-sm text-blue-800 dark:text-blue-200">
                    <x-icon icon="info-circle" class="h-5 w-5" />
                    ¿Qué puedes hacer?
                </div>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    Recarga la página para iniciar sesión nuevamente o vuelve a la página de inicio.
                </div>
            </div>

            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <x-button type="a" typeButton="secondary" text="Volver al inicio" icon="home"
                    href="{{ route('business.index') }}" class="w-full sm:w-auto" />
                @guest
                    <x-button type="a" typeButton="primary" text="Iniciar sesión" icon="login"
                        href="{{ route('login') }}" class="w-full sm:w-auto" />
                @endguest
            </div>

            <div class="mt-8 text-sm text-secondary-600 dark:text-secondary-400">
                <p>🛡️ Esta medida de seguridad protege tu cuenta y datos sensibles</p>
            </div>
        </div>
    </section>
@endsection
