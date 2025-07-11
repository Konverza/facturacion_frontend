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
                Editar cliente
            </h1>
            <a href="{{ Route('business.customers.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">
            <form action="{{ Route('business.customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select label="Tipo de documento" name="tipo_documento" id="type_document" :options="$tipos_documentos"
                            value="{{ $customer->tipoDocumento }}" selected="{{ $customer->tipoDocumento }}" />
                    </div>
                    <div class="flex-[2]">
                        <x-input type="text" label="Número de documento" name="numero_documento"
                            value="{{ $customer->numDocumento }}" placeholder="Ingresar el número de documento"
                            id="numero_documento" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre / Razón social" name="nombre" value="{{ $customer->nombre }}"
                            placeholder="Ingresa el nombre o razón social" id="nombre" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="NRC" name="nrc" id="nrc" value="{{ $customer->nrc }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre comercial" name="nombre_comercial" id="nombre_comercial"
                            placeholder="Ingresar el nombre comercial" value="{{ $customer->nombreComercial }}" />
                    </div>
                    <div class="flex-1">
                        <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                            selected="{{ $customer->codActividad }}" name="actividad_economica"
                            value="{{ old('actividad_economica', $customer->codActividad) }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select name="department" label="Departamento" id="departamento" name="departamento" required
                            :options="$departamentos" value="{{ old('departamento', $customer->departamento) }}"
                            selected="{{ old('departamento', $customer->departamento) }}"
                            data-action="{{ Route('business.get-municipios') }}" />
                    </div>
                    <div class="flex-1" id="select-municipio">
                        <x-select name="municipio" label="Municipio" id="municipality" required :options="$municipios"
                            value="{{ $customer->municipio }}" selected="{{ $customer->municipio }}" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-input type="textarea" label="Dirección" name="complemento" id="complemento"
                        placeholder="Ingresar la dirección" value="{{ old('complemento', $customer->complemento) }}" />
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Correo electrónico" icon="email" name="correo" id="correo"
                            placeholder="example@examp.com" value="{{ old('correo', $customer->correo) }}" />
                    </div>
                    <div class="flex-1">
                        <x-input type="text" label="Teléfono" icon="phone" name="telefono" id="telefono"
                            placeholder="XXXX XXXX" value="{{ old('telefono', $customer->telefono) }}" />
                    </div>
                </div>
                @if($business->show_special_prices)
                    <div class="mt-4">
                        <x-input type="checkbox" label="Aplicar precio especial a este cliente" name="special_price" id="special_price" :checked="$customer->special_price" />
                    </div>
                @endif
                <div class="mt-4">
                    @php
                        $checked = $customer->codPais || $customer->tipoPersona ? true : false;
                    @endphp
                    <x-input type="checkbox" label="Rellenar datos de exportación" name="export_data" id="export-data"
                        :checked="$checked" />
                </div>
                <div class="hidden" id="export-data-container">
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries"
                                value="{{ old('codigo_pais', $customer->codPais) }}"
                                selected="{{ old('codigo_pais', $customer->codPais) }}" />
                        </div>
                        <div class="flex-1">
                            <x-select label="Tipo de persona" name="tipo_persona" id="tipo_persona" :options="[
                                '1' => 'Persona natural',
                                '2' => 'Persona jurídica',
                            ]"
                                value="{{ old('tipo_persona', $customer->tipoPersona) }}" :search="false"
                                selected="{{ old('tipo_persona', $customer->tipoPersona) }}" />
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Editar cliente" class="w-full sm:w-auto"
                        icon="pencil" />
                </div>
            </form>
        </div>
    </section>
@endsection
