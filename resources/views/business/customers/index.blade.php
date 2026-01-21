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
