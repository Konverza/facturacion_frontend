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
                    {{ isset($dte) ? 'Datos de su DTE' : 'Consultar un DTE' }}
                </p>
            </div>
            @if(isset($dte))
                <div class="mt-5">
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Tipo de Documento:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ $tipos_dte[$dte["tipo_dte"]] }}</p>
                    </div>
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Código de generación:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ $dte["codGeneracion"] }}</p>
                    </div>
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Número de Control:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ $dte["documento"]["identificacion"]["numeroControl"] }}</p>
                    </div>
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Sello de Recibido:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ $dte["selloRecibido"] ?? "N/A" }}</p>
                    </div>
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Fecha de Procesamiento:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ \Carbon\Carbon::parse($dte["fhProcesamiento"])->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="mb-5 border-b border-gray-300 dark:border-gray-600">
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">Estado:</p>
                        <p class="text-gray-800 dark:text-gray-200 font-semibold text-base md:text-lg">{{ $dte["estado"] }}</p>
                    </div>
                    @if($dte["estado"] == "PROCESADO")
                    <div class="flex gap-4 content-center justify-center">
                        <x-button type="a" href="{{ $dte['enlace_pdf'] }}" typeButton="danger" icon="file"
                            text="Descargar PDF" size="normal" target="_blank"/>
                        <x-button type="a" href="{{ $dte['enlace_json'] }}" typeButton="primary" icon="code"
                            text="Descargar JSON" size="normal" target="_blank" />
                    </div>
                    @endif
                    <div class="flex mt-4 gap-4 content-center justify-center">
                        <x-button type="a" href="{{ route('consulta') }}" typeButton="default" icon="arrow-bar-left"
                            text="Volver a consultar" size="normal" />
                    </div>
                </div>
            @else
            <form action="{{ route("consulta.search") }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <x-input type="text" name="codGeneracion" icon="barcode" placeholder="Ingrese el código de generación"
                    label="Código de Generación" value="{{ old('codGeneracion') }}" />
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Consultar DTE" icon="search" />
                </div>
            </form>
            @endif
        </div>
    </section>
@endsection
