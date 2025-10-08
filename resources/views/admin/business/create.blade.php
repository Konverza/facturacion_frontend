@extends('layouts.auth-template')
@section('title', 'Nuevo negocio')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nuevo negocio
            </h1>
            @if (env('AMBIENTE_HACIENDA') == '01')
                <x-button type="button" icon="plus" typeButton="primary" text="Obtener del ambiente de pruebas"
                    id="obtener-pruebas" />
            @endif
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
                @if (env('AMBIENTE_HACIENDA') == '00')
                    <!-- Búsqueda por ID de Registro FE -->
                    <div
                        class="mt-2 rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-200">Prefill por ID de Registro
                            FE</h2>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <x-input type="number" min="1" label="ID empresa (Registro FE)"
                                    placeholder="Ej: 186" id="id_registro"
                                    value="{{ old('id_registro', request()->query('id_registro')) }}" />
                            </div>
                            <div class="flex gap-2">
                                <x-button type="button" id="buscar-id-registro" text="Buscar" icon="search"
                                    typeButton="secondary" />
                                @if (request()->has('id_registro'))
                                    <a href="{{ route('admin.business.create') }}"
                                        class="text-sm text-primary-600 hover:underline dark:text-primary-400">Limpiar</a>
                                @endif
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Ingresa el ID proveniente del sistema de
                            Registro FE para intentar autocompletar los campos.</p>
                    </div>
                    <!-- Fin búsqueda por ID -->
                @endif
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
                                    name="nit" id="nit" value="{{ old('nit', $prefill['nit'] ?? '') }}"
                                    required />
                                <x-input type="text" label="DUI" placeholder="Ingresa el DUI del negocio"
                                    name="dui_emisor" id="dui_emisor"
                                    value="{{ old('dui_emisor', $prefill['dui_emisor'] ?? '') }}" required />
                                <x-input type="text" label="NRC" placeholder="Ingresa el NRC del negocio"
                                    name="nrc" id="nrc" value="{{ old('nrc', $prefill['nrc'] ?? '') }}"
                                    required />
                                <x-input type="text" label="Razón social" name="razon_social" id="razon-social"
                                    value="{{ old('razon_social', $prefill['razon_social'] ?? '') }}" required />
                                <x-input type="text" label="Nombre comercial" name="nombre_comercial"
                                    value="{{ old('nombre_comercial', $prefill['nombre_comercial'] ?? '') }}" required />
                                <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica"
                                    name="actividad_economica"
                                    value="{{ old('actividad_economica', $prefill['actividad_economica'] ?? '') }}"
                                    selected="{{ old('actividad_economica', $prefill['actividad_economica'] ?? '') }}"
                                    required />
                                <x-select name="tipo_establecimiento" id="tipo_establecimiento"
                                    value="{{ old('tipo_establecimiento', $prefill['tipo_establecimiento'] ?? '') }}"
                                    selected="{{ old('tipo_establecimiento', $prefill['tipo_establecimiento'] ?? '') }}"
                                    label="Tipo de establecimiento" :options="$tipo_establecimiento" required />
                                <div class="flex flex-col gap-4">
                                    <div class="flex-1">
                                        <x-input type="text" label="Código establecimiento" placeholder="0001"
                                            name="codigo_establecimiento"
                                            value="{{ old('codigo_establecimiento', $prefill['codigo_establecimiento'] ?? '') }}"
                                            required />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Código establecimiento (Ministerio de Hacienda)"
                                            name="codigo_establecimiento_mh"
                                            value="{{ old('codigo_establecimiento_mh', $prefill['codigo_establecimiento_mh'] ?? '') }}" />
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4">
                                    <div class="flex-1">
                                        <x-input type="text" label="Código punto de venta" name="codigo_punto_venta"
                                            placeholder="01"
                                            value="{{ old('codigo_punto_venta', $prefill['codigo_punto_venta'] ?? '01') }}"
                                            required />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Código punto de venta (Ministerio de Hacienda)"
                                            name="codigo_punto_venta_mh"
                                            value="{{ old('codigo_punto_venta_mh', $prefill['codigo_punto_venta_mh'] ?? '') }}" />
                                    </div>
                                </div>
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-1">
                                        <x-select name="department" label="Departamento" id="departamento"
                                            name="departamento" required :options="$departamentos"
                                            value="{{ old('departamento', $prefill['departamento'] ?? '') }}"
                                            selected="{{ old('departamento', $prefill['departamento'] ?? '') }}"
                                            data-action="{{ Route('admin.get-municipios') }}" />
                                    </div>
                                    <div class="flex-1" id="select-municipio">
                                        @if (isset($municipios) && is_array($municipios))
                                            <x-select name="municipio" label="Municipio" id="municipality" required
                                                :options="$municipios" value="{{ old('municipio', $municipio_prefill ?? '') }}"
                                                selected="{{ old('municipio', $municipio_prefill ?? '') }}" />
                                        @else
                                            <x-select name="municipio" label="Municipio" id="municipality" required
                                                :options="[
                                                    'Selecciona un departamento' => 'Seleccione un departamento',
                                                ]" />
                                        @endif
                                        @if (isset($prefill['municipio_legacy_text']) && $prefill['municipio_legacy_text'])
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Municipio (referencia original): <span
                                                    class="font-semibold">{{ $prefill['municipio_legacy_text'] }}</span><br>
                                                Selecciona el municipio correspondiente en el catálogo actualizado.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <x-input type="text" label="Dirección" name="complemento"
                                    value="{{ old('complemento', $prefill['complemento'] ?? '') }}" required />
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="flex-[2]">
                                        <x-input type="email" label="Correo electrónico" icon="email" name="correo"
                                            placeholder="example@exam.com"
                                            value="{{ old('correo', $prefill['correo'] ?? '') }}" required />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" placeholder="XXXX - XXXX" label="Teléfono"
                                            icon="phone" name="telefono"
                                            value="{{ old('telefono', $prefill['telefono'] ?? '') }}" required />
                                    </div>
                                </div>
                                <!-- Input logo empresa -->
                                <x-input type="file" label="Logo de la empresa" name="logo" id="logo"
                                    accept=".png, .jpg, .jpeg, .webp" maxSize="3072" required />
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
                                    value="{{ old('plan_id') }}"
                                    selected="{{ old('plan_id', $prefill['plan_id'] ?? '') }}" required />
                                @if (isset($prefill['plan_name']))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Plan actual: {{ $prefill['plan_name'] }}
                                    </p>
                                @endif
                                <div>
                                    <span
                                        class="mb-1 block text-sm font-medium text-gray-500 after:ml-0.5 dark:text-gray-300">
                                        DTEs habilitados:
                                    </span>
                                    <div class="mt-4 flex flex-col gap-2">
                                        @php
                                            $dte_options = [
                                                ['id' => '01', 'label' => 'Factura Electrónica', 'value' => '01'],
                                                [
                                                    'id' => '03',
                                                    'label' => 'Comprobante de Crédito Fiscal',
                                                    'value' => '03',
                                                ],
                                                ['id' => '04', 'label' => 'Nota de Remisión', 'value' => '04'],
                                                ['id' => '05', 'label' => 'Nota de Crédito', 'value' => '05'],
                                                ['id' => '06', 'label' => 'Nota de Débito', 'value' => '06'],
                                                ['id' => '07', 'label' => 'Comprobante de Retención', 'value' => '07'],
                                                ['id' => '11', 'label' => 'Factura de Exportación', 'value' => '11'],
                                                [
                                                    'id' => '14',
                                                    'label' => 'Factura de Sujeto Excluido',
                                                    'value' => '14',
                                                ],
                                                ['id' => '15', 'label' => 'Comprobante de Donación', 'value' => '15'],
                                            ];
                                        @endphp

                                        @error('dtes')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror

                                        @foreach ($dte_options as $dte)
                                            @php
                                                $checked = in_array($dte['value'], old('dtes', $prefill['dtes'] ?? []));
                                            @endphp
                                            <x-input type="toggle" name="dtes[]" label="{{ $dte['label'] }}"
                                                value="{{ $dte['value'] }}" id="{{ $dte['id'] }}"
                                                :checked="$checked" />
                                        @endforeach

                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const form = document.querySelector('form');
                                                form.addEventListener('submit', function(e) {
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
                                            name="dui" id="dui"
                                            value="{{ old('dui', $prefill['dui'] ?? '') }}" required />
                                    </div>
                                    <div class="flex-1">
                                        <x-input type="text" label="Teléfono" icon="phone"
                                            value="{{ old('telefono_responsable', $prefill['telefono_responsable'] ?? '') }}"
                                            name="telefono_responsable" id="telefono_responsable" required
                                            placeholder="XXXX - XXXX" />
                                    </div>
                                </div>
                                <x-input type="text" label="Nombre según documento"
                                    value="{{ old('nombre_responsable', $prefill['nombre_responsable'] ?? '') }}"
                                    name="nombre_responsable" required />
                                <x-input type="text" label="Correo electrónico" icon="email"
                                    value="{{ old('correo_responsable', $prefill['correo_responsable'] ?? '') }}"
                                    name="correo_responsable" id="correo_responsable" placeholder="example@exam.com"
                                    required />
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
                                    id="certificado" accept=".crt" maxSize="3072" required />
                                <x-input type="text" label="Clave PRIVADA del certificado" name="certificate_password"
                                    id="certificate_password"
                                    value="{{ old('certificate_password', $prefill['certificate_password'] ?? '') }}"
                                    required />
                                <x-input type="text" label="Clave de USUARIO API registrada en Hacienda"
                                    name="api_password" id="api_password"
                                    value="{{ old('api_password', $prefill['api_password'] ?? '') }}" required />
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
    @push('scripts')
        <script>
            $(document).ready(function() {
                $(document).on("click", "#obtener-pruebas", function() {
                    const nit = $("#nit").val().trim();
                    if (!nit) {
                        alert("Por favor ingrese un NIT válido");
                        return;
                    }
                    const urlCreate = new URL("{{ route('admin.business.create') }}", window.location.origin);
                    urlCreate.searchParams.set('nit', nit);
                    window.location.href = urlCreate.toString();
                });

                $(document).on("click", "#buscar-id-registro", function() {
                    const id = $("#id_registro").val().trim();
                    if (!id) {
                        alert("Ingresa un ID válido");
                        return;
                    }
                    const urlCreate = new URL("{{ route('admin.business.create') }}", window.location.origin);
                    urlCreate.searchParams.set('id_registro', id);
                    window.location.href = urlCreate.toString();
                });
            });
        </script>
    @endpush
@endsection
