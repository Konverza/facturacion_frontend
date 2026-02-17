@extends('layouts.auth-template')
@section('title', 'Acceso API')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Acceso API
            </h1>
            <a href="{{ Route('admin.business.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>

        <div class="mt-4 flex flex-col gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-2">
                    <div class="text-sm text-gray-500 dark:text-gray-300">
                        Negocio: <span class="font-semibold text-gray-700 dark:text-gray-100">{{ $business->nombre }}</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-300">
                        NIT: <span class="font-semibold text-gray-700 dark:text-gray-100">{{ $business->nit }}</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-300">
                        Estado: 
                        @if ($business->has_api_access)
                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Habilitado</span>
                        @else
                            <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-semibold text-gray-700">Deshabilitado</span>
                        @endif
                    </div>
                </div>
            </div>

            @if (session('api_key_plain'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Esta es la unica vez que se mostrara la API Key. Copiela y guardela en un lugar seguro.
                    </p>
                    <div class="mt-3">
                        <x-input type="text" label="API Key" name="api_key_plain"
                            value="{{ session('api_key_plain') }}" readonly />
                    </div>
                </div>
            @endif

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Detalles de API Key</h2>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                            <div>
                                Ultimos 4 digitos: 
                                <span class="font-semibold text-gray-700 dark:text-gray-100">
                                    {{ $business->api_key_last4 ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                Fecha de creacion: 
                                <span class="font-semibold text-gray-700 dark:text-gray-100">
                                    {{ $business->api_key_created_at ? $business->api_key_created_at->format('d/m/Y H:i') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                        @if (!$business->has_api_access)
                            <form method="POST" action="{{ Route('admin.business.api-access.enable', $business->id) }}">
                                @csrf
                                <x-button type="submit" icon="check" typeButton="success" text="Habilitar" />
                            </form>
                        @else
                            <form method="POST" action="{{ Route('admin.business.api-access.disable', $business->id) }}">
                                @csrf
                                <x-button type="submit" icon="lock" typeButton="warning" text="Deshabilitar" />
                            </form>
                        @endif

                        <form method="POST" action="{{ Route('admin.business.api-access.generate', $business->id) }}">
                            @csrf
                            <x-button type="submit" icon="refresh" typeButton="primary" text="Generar/Rotar" />
                        </form>

                        <form method="POST" action="{{ Route('admin.business.api-access.revoke', $business->id) }}">
                            @csrf
                            <x-button type="submit" icon="trash" typeButton="danger" text="Revocar" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
