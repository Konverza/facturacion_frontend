@php
    $business_id = Session::get('business') ?? null;
    $business = \App\Models\Business::find($business_id);
@endphp
@extends('layouts.auth-template')
@section('title', 'Nuevo cliente')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nuevo cliente
            </h1>
            <a href="{{ Route('business.customers.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">
            <form action="{{ Route('business.customers.store') }}" method="POST">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de documento" name="tipo_documento" id="type_document" :options="$tipos_documentos"
                            value="{{ old('tipo_documento') }}" selected="{{ old('tipo_documento') }}" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" label="Número de documento" name="numero_documento"
                            value="{{ old('numero_documento') }}" placeholder="Ingresar el número de documento"
                            id="numero_documento" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre / Razón social" name="nombre" value="{{ old('nombre') }}"
                            placeholder="Ingresa el nombre o razón social" id="nombre" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="NRC" name="nrc" id="nrc" value="{{ old('nrc') }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre comercial" name="nombre_comercial" id="nombre_comercial"
                            placeholder="Ingresar el nombre comercial" value="{{ old('nombre_comercial') }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                            name="actividad_economica" value="{{ old('actividad_economica') }}"
                            selected="{{ old('actividad_economica') }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select name="department" label="Departamento" id="departamento" name="departamento" required
                            :options="$departamentos" value="{{ old('departamento') }}" selected="{{ old('departamento') }}"
                            data-action="{{ Route('business.get-municipios') }}" />
                    </div>
                    <div class="flex-1" id="select-municipio">
                        <x-select name="municipio" label="Municipio" id="municipality" required :options="[
                            'Selecciona un municipio' => 'Seleccione un municipio',
                        ]"
                            value="{{ old('municipio') }}" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" label="Dirección" name="complemento" id="complemento"
                        placeholder="Ingresar la dirección" value="{{ old('complemento') }}" />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Correo electrónico" icon="email" name="correo" id="correo"
                            placeholder="example@examp.com" value="{{ old('email') }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="Teléfono" icon="phone" name="telefono" id="telefono"
                            placeholder="XXXX XXXX" value="{{ old('phone') }}" />
                    </div>
                </div>
                @if ($business->show_special_prices)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Aplicar precio especial a este cliente" name="special_price"
                            id="special_price" />
                    </div>
                @endif
                <div class="mt-4">
                    <x-input type="checkbox" label="Rellenar datos de exportación" name="export_data" id="export-data" />
                </div>
                <div class="hidden" id="export-data-container">
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries" />
                        </div>
                        <div class="flex-1">
                            <x-select label="Tipo de persona" name="tipo_persona" id="tipo_persona" :options="[
                                '1' => 'Persona natural',
                                '2' => 'Persona jurídica',
                            ]" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Guardar cliente" class="w-full sm:w-auto"
                        icon="save" />
                </div>
            </form>
        </div>
    </section>
@endsection
