@extends('layouts.template')
@section('title', 'Consulta DTE')
@section('content')
    <section class="flex h-auto w-full items-center justify-center mt-8">
        <div class=" w-auto rounded-lg border bg-white dark:bg-black shadow-lg p-6">
            <div class="overflow-hidden rounded-full">
                <img src="{{ asset('images/only-icon.png') }}" alt="Logo Konverza" class="mx-auto w-32 object-cover">
            </div>
            <div class="text-center">
                <h1 class="text-xl md:text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
                    Facturación Konverza
                </h1>
                <p class="mb-4 text-sm md:text-base text-gray-600 dark:text-gray-300">
                    Seleccione un Ambiente
                </p>
            </div>
            <div class="mt-4 flex flex-col gap-4">
                <a href="https://facturacion-pruebas.konverza.digital"
                    class="w-96 rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                    <div class="flex justify-start gap-4">
                        <img src="{{ asset('images/only-icon.png') }}" class="size-12 rounded-full object-cover"
                            alt="logo empresa" />
                        <div class="flex flex-col items-center">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                                Ambiente de Pruebas
                            </h2>
                        </div>
                    </div>
                </a>
                <a href="https://facturacion.konverza.digital"
                    class="w-96 rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                    <div class="flex justify-start gap-4">
                        <img src="{{ asset('images/only-icon.png') }}" class="size-12 rounded-full object-cover"
                            alt="logo empresa" />
                        <div class="flex flex-col items-center">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                                Ambiente Productivo
                            </h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
