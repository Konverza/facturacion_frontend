@extends('layouts.auth-template')
@section('title', 'Clientes')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Clientes
            </h1>
            <x-button type="button" typeButton="primary" text="Compartir mi QR" icon="qrcode" 
                class="show-modal" data-target="#modal-qr" />
        </div>
        <div class="mt-4">
            <livewire:business.tables.clients />
        </div>
        @if (session('customer_import_summary'))
            <div class="mt-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-sm text-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-300">
                <p class="font-semibold">Resultado de importación</p>
                <p>
                    Procesados: {{ session('customer_import_summary.processed', 0) }} |
                    Actualizados: {{ session('customer_import_summary.updated', 0) }} |
                    Insertados: {{ session('customer_import_summary.inserted', 0) }} |
                    Omitidos: {{ session('customer_import_summary.skipped', 0) }}
                </p>
            </div>
        @endif

        @if (session('customer_import_errors') && count(session('customer_import_errors')) > 0)
            <div class="mt-4 overflow-hidden rounded-lg border border-red-300 bg-red-50 dark:border-red-900/60 dark:bg-red-950/30">
                <div class="border-b border-red-200 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900/70 dark:text-red-300">
                    Filas con error ({{ count(session('customer_import_errors')) }})
                </div>
                <div class="max-h-80 overflow-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-red-100 text-left text-red-700 dark:bg-red-900/30 dark:text-red-300">
                            <tr>
                                <th class="px-4 py-2">Fila</th>
                                <th class="px-4 py-2">Documento</th>
                                <th class="px-4 py-2">Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('customer_import_errors') as $rowError)
                                <tr class="border-t border-red-100 dark:border-red-900/40">
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $rowError['row'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $rowError['numDocumento'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $rowError['reason'] ?? 'Error no especificado' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el cliente"
            message="No podrás recuperar este registro" />

        <div id="modal-import" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-lg p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.customers.import') }}" method="POST" id="form-import"
                            enctype="multipart/form-data">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Importar Clientes
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#modal-import">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <div
                                    class="my-2 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
                                    <b>Nota: </b> Debe utilizar un archivo en formato <b>.xlsx</b> disponible en
                                    <a href="{{ $business->show_special_prices == 1 && !$business->price_variants_enabled ? url('templates/importacion_clientes_completo.xlsx') : url('templates/importacion_clientes.xlsx') }}" target="_blank"
                                        class="text-blue-600 underline dark:text-blue-400">este enlace</a> para
                                    importar clientes.
                                </div>
                                <div
                                    class="mb-2 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                                    <b>Advertencia: </b> Tome en cuenta que al importar clientes, si coincide el Número de Documento y
                                    el nombre de un cliente ya existente, se actualizará el cliente con los nuevos
                                    datos. Si no existe, se creará un nuevo cliente.
                                </div>

                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                                    <p class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-100">Modo de importación</p>
                                    <div class="flex flex-col gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" name="import_mode" value="full" checked class="import-mode-radio">
                                            <span>Importación completa (crea nuevos y actualiza existentes)</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" name="import_mode" value="partial" class="import-mode-radio">
                                            <span>Actualización parcial (solo actualiza clientes existentes)</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="partial-update-fields"
                                    class="hidden rounded-lg border border-dashed border-indigo-300 bg-indigo-50 p-4 dark:border-indigo-900/70 dark:bg-indigo-950/20">
                                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                                        Campos a actualizar en modo parcial
                                    </p>
                                    <p class="mt-1 text-xs text-indigo-700 dark:text-indigo-300">
                                        El campo <b>Número de Documento</b> siempre es obligatorio en el archivo.
                                    </p>
                                    <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-2">
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="tipoDocumento"> Tipo de Documento</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="nrc"> NRC</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="nombre"> Nombre</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="codActividad"> Actividad Económica</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="nombreComercial"> Nombre Comercial</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="departamento"> Departamento</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="municipio"> Municipio</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="complemento"> Dirección</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="telefono"> Teléfono</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="correo"> Correo</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="codPais"> País</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="tipoPersona"> Tipo de Persona</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="update_fields[]" value="special_price"> Aplicar Descuento</label>
                                    </div>
                                </div>

                                <x-input type="file" label="Archivo de Clientes" name="file" id="file"
                                    accept=".xlsx" maxSize="3072" />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#modal-import" />
                                <x-button type="submit" text="Importar" icon="cloud-upload" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal QR --}}
        <div id="modal-qr" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-2xl p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Código QR para Registro de Clientes
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#modal-qr">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Comparte este código QR con tus clientes para que puedan registrar sus datos directamente.
                            </p>
                            <div class="flex justify-center">
                                <img src="{{ route('business.customers.qr-image') }}" 
                                     alt="QR Registro de Clientes" 
                                     class="max-w-full h-auto border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg">
                            </div>
                            <div class="flex gap-2 justify-center mt-4">
                                <x-button type="a" href="{{ route('business.customers.qr-image') }}" 
                                    download="QR-Registro-Clientes.png" typeButton="primary" 
                                    text="Descargar QR" icon="download" />
                                <x-button type="button" typeButton="secondary" text="Compartir Link" 
                                    icon="share" id="share-link-btn" />
                            </div>
                            <div class="hidden" id="share-link-container">
                                <x-input type="text" label="Link de registro" 
                                    value="{{ route('registro-clientes', ['nit' => $business->nit]) }}" 
                                    readonly id="share-link-input" />
                                <x-button type="button" typeButton="success" text="Copiar" 
                                    icon="copy" class="mt-2 w-full" id="copy-link-btn" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @push('scripts')
    <script>
        const importModeInputs = document.querySelectorAll('.import-mode-radio');
        const partialFieldsContainer = document.getElementById('partial-update-fields');

        const togglePartialFields = () => {
            const selectedMode = document.querySelector('.import-mode-radio:checked')?.value;
            if (!partialFieldsContainer) return;
            partialFieldsContainer.classList.toggle('hidden', selectedMode !== 'partial');
        };

        importModeInputs.forEach((input) => {
            input.addEventListener('change', togglePartialFields);
        });
        togglePartialFields();

        document.getElementById('share-link-btn')?.addEventListener('click', function() {
            document.getElementById('share-link-container')?.classList.toggle('hidden');
        });

        document.getElementById('copy-link-btn')?.addEventListener('click', function() {
            const input = document.getElementById('share-link-input');
            input.select();
            document.execCommand('copy');
            
            // Mostrar mensaje de éxito
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Copiado!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        });
    </script>
    @endpush
@endsection
