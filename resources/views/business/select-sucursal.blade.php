@extends('layouts.template')
@section('title', 'Selecciona una sucursal')
@section('content')
    <section class="flex h-screen w-full flex-col items-center justify-center">
        <h1 class="text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
            Selecciona una sucursal
        </h1>
        <p class="mt-2 text-center text-gray-500 dark:text-gray-400">
            Puede ver los datos de las sucursales asociadas a tu negocio, selecciona una para continuar
        </p>
        <div class="mt-4 max-h-96 overflow-y-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($sucursales as $sucursal)
                    <form action="{{ Route('business.select-sucursal') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sucursal" value="{{ $sucursal->id }}" />
                        <button type="submit"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                            <div class="flex justify-start gap-4">
                                <div class="flex flex-col items-start">
                                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                                        {{ $sucursal->nombre }}
                                    </h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Direccion: {{ $sucursal->complemento }}
                                    </p>
                                    <p class="text-wrap text-left text-sm text-gray-500 dark:text-gray-400">
                                        Correo: {{ $sucursal->correo }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
        <div class="mt-6 flex justify-center">
            <form action="{{ Route('business.select-sucursal') }}" method="POST">
                @csrf
                <input type="hidden" name="no_sucursal" value="true" />
                <button type="submit"
                    class="rounded-lg border border-gray-300 bg-gray-50 px-6 py-3 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                        Ver todas las sucursales
                    </h2>
                </button>
            </form>
        </div>
    </section>
@endsection
