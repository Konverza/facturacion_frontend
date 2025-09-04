@extends('layouts.auth-template')
@section('title', 'Nuevo negocio')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nuevo negocio
            </h1>
            <a href="{{ Route('admin.business.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-1">
            <p class="text-sm text-gray-600 dark:text-gray-400 sm:text-base">
                Ingrese los datos del negocio. Los campos marcados con <span class="text-red-500">*</span> son obligatorios.
            </p>
            <form class="mt-4 flex flex-col pb-4" action="{{ Route('admin.business.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-6 xl:flex-row">
                    <div class="flex-1">
                        <div class="mt-4">
                            <span class="text-gray-400 dark:text-gray-600">
                                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                    Datos del negocio
                                </h2>
                            </span>
                            <div class="mt-2 flex flex-col gap-4">
                                <x-input type="text" label="NIT" placeholder="Ingresa el NIT del negocio"
                                    name="nit" id="nit" value="{{ old('nit') }}" required/>
                                <x-input type="text" label="DUI" placeholder="Ingresa el DUI del negocio"
                                    name="dui" id="dui" value="{{ old('dui') }}" required/>
                                <x-input type="text" label="NRC" placeholder="Ingresa el NRC del negocio"
                                    name="nrc" id="nrc" value="{{ old('nrc') }}" required/>
                                <x-input type="text" label="Razón social" name="razon_social" id="razon-social"
                                    value="{{ old('razon_social') }}" required/>
                                <x-input type="text" label="Nombre comercial" name="nombre_comercial"
                                    value="{{ old('nombre_comercial') }}" required/>
                                <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                                    name="actividad_economica" value="{{ old('actividad_economica') }}" required/>
                                <x-select name="tipo_establecimiento" id="tipo_establecimiento"
                                    value="{{ old('tipo_establecimiento') }}" label="Tipo de establecimiento"
                                    :options="$tipo_establecimiento" required/>
                                <div class="flex flex-col gap-4">
                                    <div class="flex-1">
                                        <x-input type="text" label="Código establecimiento" placeholder="0001"
                                            name="codigo_establecimiento" value="{{ old('codigo_establecimiento') }}" required/>
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Código establecimiento (Ministerio de Hacienda)"
                                            name="codigo_establecimiento_mh"
                                            value="{{ old('codigo_establecimiento_mh') }}"/>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <div class="flex-1">
                                        <x-input type="text" label="Código punto de venta" name="codigo_punto_venta"
                                            placeholder="01" value="01" value="{{ old('codigo_punto_venta') }}" required/>
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Código punto de venta (Ministerio de Hacienda)"
                                            name="codigo_punto_venta_mh" value="{{ old('codigo_punto_venta_mh') }}"/>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-1">
                                        <x-select name="department" label="Departamento" id="departamento"
                                            name="departamento" required :options="$departamentos"
                                            value="{{ old('departamento') }}" selected="{{ old('departamento') }}"
                                            data-action="{{ Route('admin.get-municipios') }}"/>
                                    </div>
                                    <div class="flex-1" id="select-municipio">
                                        <x-select name="municipio" label="Municipio" id="municipality" required
                                            :options="[
                                                'Selecciona un departamento' => 'Seleccione un departamento',
                                            ]" />
                                    </div>
                                </div>
                                <x-input type="text" label="Dirección" name="complemento" required/>
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-[2]">
                                        <x-input type="email" label="Correo electrónico" icon="email" name="correo"
                                            placeholder="example@exam.com" value="{{ old('correo') }}" required/>
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" placeholder="XXXX - XXXX" label="Teléfono"
                                            icon="phone" name="telefono" value="{{ old('telefono') }}" required/>
                                    </div>
                                </div>
                                <!-- Input logo empresa -->
                                <x-input type="file" label="Logo de la empresa" name="logo" id="logo"
                                    accept=".png, .jpg, .jpeg, .webp" maxSize="3072" required/>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="mt-4">
                            <span class="text-gray-400 dark:text-gray-600">
                                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                    Configuración de facturación electrónica
                                </h2>
                            </span>
                            <div class="mt-2 flex flex-col gap-4">
                                <x-select label="Plan contratado" name="plan_id" id="plan" :options="$plans->pluck('nombre', 'id')->toArray()"
                                    value="{{ old('plan_id') }}" selected="{{ old('plan_id') }}" required/>
                                <div>
                                    <span
                                        class="mb-1 block text-sm font-medium text-gray-500 after:ml-0.5 dark:text-gray-300">
                                        DTEs habilitados:
                                    </span>
                                    <div class="mt-4 flex flex-col gap-2">
                                        @php
                                            $dte_options = [
                                                ['id' => '01', 'label' => 'Factura Electrónica', 'value' => '01'],
                                                ['id' => '03', 'label' => 'Comprobante de Crédito Fiscal', 'value' => '03'],
                                                ['id' => '04', 'label' => 'Nota de Remisión', 'value' => '04'],
                                                ['id' => '05', 'label' => 'Nota de Crédito', 'value' => '05'],
                                                ['id' => '06', 'label' => 'Nota de Débito', 'value' => '06'],
                                                ['id' => '07', 'label' => 'Comprobante de Retención', 'value' => '07'],
                                                ['id' => '11', 'label' => 'Factura de Exportación', 'value' => '11'],
                                                ['id' => '14', 'label' => 'Factura de Sujeto Excluido', 'value' => '14'],
                                                ['id' => '15', 'label' => 'Comprobante de Donación', 'value' => '15'],
                                            ];
                                        @endphp

                                        @error('dtes')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror

                                        @foreach($dte_options as $dte)
                                            <x-input type="toggle" name="dtes[]" label="{{ $dte['label'] }}"
                                                value="{{ $dte['value'] }}" id="{{ $dte['id'] }}" />
                                        @endforeach

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                const form = document.querySelector('form');
                                                form.addEventListener('submit', function (e) {
                                                    const checked = document.querySelectorAll('input[name="dtes[]"]:checked');
                                                    if (checked.length === 0) {
                                                        e.preventDefault();
                                                        alert('Debes seleccionar al menos un tipo de DTE.');
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="text-gray-400 dark:text-gray-600">
                                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                    Datos del responsable
                                </h2>
                            </span>
                            <div class="mt-2 flex flex-col gap-4">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-1">
                                        <x-input type="text" placeholder="DUI" label="Documento de identidad"
                                            name="dui" id="dui" value="{{ old('dui') }}" required />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Teléfono" icon="phone"
                                            value="{{ old('telefono_responsable') }}" name="telefono_responsable"
                                            id="telefono_responsable" required placeholder="XXXX - XXXX" />
                                    </div>
                                </div>
                                <x-input type="text" label="Nombre según documento"
                                    value="{{ old('nombre_responsable') }}" name="nombre_responsable" required />
                                <x-input type="text" label="Correo electrónico" icon="email"
                                    value="{{ old('correo_responsable') }}" name="correo_responsable"
                                    id="correo_responsable" placeholder="example@exam.com" required />
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="text-gray-400 dark:text-gray-600">
                                <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                                    Datos de acceso a API y certificado
                                </h2>
                            </span>
                            <div class="mt-2 flex flex-col gap-4">
                                <x-input type="file" label="Certificado de firma electrónica" name="certificado_file"
                                    id="certificado" accept=".crt" maxSize="3072" required/>
                                <x-input type="text" label="Clave PRIVADA del certificado" name="certificate_password"
                                    id="certificate_password" value="{{ old('certificate_password') }}" required/>
                                <x-input type="text" label="Clave de USUARIO API registrada en Hacienda"
                                    name="api_password" id="api_password" value="{{ old('api_password') }}" required/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" text="Guardar negocio" class="w-full sm:w-max" icon="plus"
                        typeButton="primary" />
                </div>
            </form>
        </div>
    </section>
@endsection
