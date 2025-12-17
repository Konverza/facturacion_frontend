@extends('layouts.template')
@section('title', 'Registro de Cliente')
@section('content')
    <section class="flex h-auto w-full items-center justify-center px-4">
        <div class="w-full max-w-3xl rounded-lg border bg-white dark:bg-black shadow-lg p-6">
            @if (!$business)
                {{-- Error: NIT inválido --}}
                <div class="text-center mt-6">
                    <h1 class="text-xl md:text-2xl font-bold uppercase text-red-500 dark:text-red-400">
                        NIT Inválido
                    </h1>
                    <p class="mb-4 text-sm md:text-base text-gray-600 dark:text-gray-300">
                        {{ $error }}
                    </p>
                    <div class="mt-6">
                        <x-button type="a" href="https://konverza.digital" typeButton="primary" text="Volver"
                            icon="arrow-back" />
                    </div>
                </div>
            @else
                @php
                    $logo = Http::get(
                        env('OCTOPUS_API_URL') . '/datos_empresa/nit/' . $business->nit . '/logo',
                    )->json();
                @endphp
                <div class="overflow-hidden rounded-full">
                    <img src="{{ $logo['url'] }}" alt="Logo Negocio" class="mx-auto w-32 object-cover">
                </div>
                {{-- Formulario de registro --}}
                <div class="text-center">
                    <h1 class="text-xl md:text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
                        {{ strtoupper($business->nombre) }}
                    </h1>
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-gray-200 mt-2">
                        Registro de Clientes
                    </h2>
                    <p class="mb-4 text-sm md:text-base text-gray-600 dark:text-gray-300 mt-4">
                        Dando cumplimiento a las disposiciones del Ministerio de Hacienda, estamos implementando la
                        facturación electrónica. <br> Agradeceremos completar el siguiente formulario con los datos
                        correspondientes, así evitará contratiempos al momento de solicitar su factura.
                    </p>
                </div>

                @if (session('success'))
                    <div
                        class="mb-4 rounded-lg border border-green-500 bg-green-100 p-4 text-green-700 dark:bg-green-950/30 dark:text-green-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-4 rounded-lg border border-red-500 bg-red-100 p-4 text-red-700 dark:bg-red-950/30 dark:text-red-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <form action="{{ route('registro-clientes.store', ['nit' => $business->nit]) }}" method="POST"
                    class="mt-6">
                    @csrf

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select label="Tipo de documento" name="tipo_documento" id="type_document" :options="$tipos_documentos"
                                value="{{ old('tipo_documento', '36') }}" selected="{{ old('tipo_documento', '36') }}"
                                required />
                        </div>
                        <div class="flex-[2]">
                            <x-input type="text" label="Número de documento" name="numero_documento"
                                value="{{ old('numero_documento') }}" placeholder="Ingresar el número de documento"
                                id="numero_documento" required />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" label="Nombre / Razón social" name="nombre" value="{{ old('nombre') }}"
                                placeholder="Ingresa el nombre o razón social" id="nombre" required />
                        </div>
                        <div class="flex-1">
                            <x-input type="text" label="NRC" name="nrc" id="nrc"
                                value="{{ old('nrc') }}" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="text" label="Nombre comercial" name="nombre_comercial" id="nombre_comercial"
                                placeholder="Ingresar el nombre comercial" value="{{ old('nombre_comercial') }}" />
                        </div>
                        <div class="flex-1">
                            <x-select id="actividad_economica" :options="$actividades_economicas" label="Actividad económica (opcional)"
                                name="actividad_economica" value="{{ old('actividad_economica') }}"
                                selected="{{ old('actividad_economica') }}" />
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-select name="department" label="Departamento" id="departamento" name="departamento" required
                                :options="$departamentos" value="{{ old('departamento') }}" selected="{{ old('departamento') }}"
                                data-action="{{ Route('api.municipios') }}" />
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
                            placeholder="Ingresar la dirección" value="{{ old('complemento') }}" required />
                    </div>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                        <div class="flex-1">
                            <x-input type="email" label="Correo electrónico" icon="email" name="correo"
                                id="correo" placeholder="example@examp.com" value="{{ old('correo') }}" required />
                        </div>
                        <div class="flex-1">
                            <x-input type="text" label="Teléfono" icon="phone" name="telefono" id="telefono"
                                placeholder="XXXX XXXX" value="{{ old('telefono') }}" required />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input type="checkbox" label="Rellenar datos de exportación" name="export_data"
                            id="export-data" />
                    </div>

                    <div class="hidden" id="export-data-container">
                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-select label="País" name="codigo_pais" id="codigo_pais" :options="$countries" />
                            </div>
                            <div class="flex-1">
                                <x-select label="Tipo de persona" name="tipo_persona" id="tipo_persona"
                                    :options="['1' => 'Persona natural', '2' => 'Persona jurídica']" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-center">
                        <x-button type="submit" typeButton="primary" text="Registrar mis datos"
                            class="w-full sm:w-auto" icon="save" />
                    </div>
                </form>

                <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
                    Con la tecnología de <a href="https://konverza.digital" target="_blank"
                        class="text-primary-500 dark:text-primary-300 hover:underline">Konverza</a>
                </div>
            @endif
        </div>
    </section>

    @if ($business)
        @push('scripts')
            <script>
                // $(document).ready(function() {
                //     const $departamento = $("#departamento");

                //     $departamento.on("Changed", function() {
                //         const action = $(this).data("action");
                //         const codigo = $(this).val();
                //         handleAjax(action, codigo);
                //     });

                //     function handleAjax(action, codigo) {
                //         $.ajax({
                //             url: action,
                //             type: "GET",
                //             data: {
                //                 codigo: codigo,
                //                 municipio: $("#municipio").val() ? $("#municipio").val() : null,
                //             },
                //             success: function(response) {
                //                 $("#select-municipio").html(response.html);
                //                 $("#edit-municipio").html(response.html);
                //             },
                //             error: function() {
                //                 console.log("Error");
                //             },
                //         });
                //     }
                // });

                // Manejo de datos de exportación
                document.getElementById('export-data')?.addEventListener('change', function(e) {
                    const exportContainer = document.getElementById('export-data-container');
                    if (e.target.checked) {
                        exportContainer.classList.remove('hidden');
                    } else {
                        exportContainer.classList.add('hidden');
                    }
                });
            </script>
        @endpush
    @endif
@endsection
