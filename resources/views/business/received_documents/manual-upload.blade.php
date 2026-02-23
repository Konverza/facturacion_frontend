@extends('layouts.auth-template')
@section('title', 'Carga manual de DTE')
@section('content')
    <section class="my-4 px-4">
        <div class="mb-4 flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Cargar DTE manualmente
            </h1>
        </div>

        <div class="mb-6">
            <x-button type="a" typeButton="secondary" icon="arrow-left"
                href="{{ route('business.received-documents.index') }}" text="Volver a DTEs Recibidos" />
        </div>

        <div x-data="manualUploadManager()"
            class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <form @submit.prevent="uploadFiles" class="space-y-4">
                <div
                    class="rounded-lg border border-dashed border-blue-400 bg-blue-50 p-4 text-sm text-blue-700 dark:border-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                    Puede cargar varios archivos JSON de DTE a la vez. Arrastre y suelte archivos o selecciónelos manualmente.
                    Cada archivo se procesará de forma individual y verá su resultado en pantalla.
                </div>

                <div @dragover.prevent="isDragOver = true" @dragleave.prevent="isDragOver = false"
                    @drop.prevent="handleDrop($event)" :class="isDragOver ? 'border-primary-500 bg-primary-50/70 dark:bg-primary-900/20' : ''"
                    class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center transition-colors dark:border-gray-600">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Arrastre y suelte archivos aquí
                    </p>
                    <p class="my-2 text-xs text-gray-500">o</p>
                    <button type="button" @click="$refs.fileInput.click()"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">
                        <x-icon icon="folder-open" class="h-4 w-4" />
                        Seleccionar archivos
                    </button>
                    <p class="mt-2 text-xs text-gray-500">Formatos permitidos: .json, .txt (máximo 5 MB por archivo)</p>
                </div>

                <input x-ref="fileInput" type="file" class="hidden" multiple accept=".json,.txt"
                    @change="handleSelect($event)">

                <div x-show="files.length > 0" class="space-y-2">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Archivos seleccionados</h3>
                    <template x-for="(file, index) in files" :key="`${file.name}-${file.size}-${index}`">
                        <div
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-200" x-text="file.name"></p>
                                <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                            </div>
                            <button type="button" @click="removeFile(index)"
                                class="rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                Eliminar
                            </button>
                        </div>
                    </template>
                </div>

                <template x-if="errorMessage">
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300"
                        x-text="errorMessage"></div>
                </template>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="clearFiles" :disabled="uploading || files.length === 0"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                        Limpiar lista
                    </button>
                    <button type="submit" :disabled="uploading || files.length === 0"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="uploading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <x-icon x-show="!uploading" icon="cloud-upload" class="h-4 w-4" />
                        <span x-text="uploading ? 'Procesando...' : 'Cargar archivos'"></span>
                    </button>
                </div>
            </form>

            <div x-show="summary" class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20">
                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">Resultado de la carga</p>
                <p class="text-xs text-blue-700 dark:text-blue-300">
                    Total: <span x-text="summary?.total ?? 0"></span> |
                    Procesados: <span x-text="summary?.processed ?? 0"></span> |
                    Fallidos: <span x-text="summary?.failed ?? 0"></span>
                </p>
            </div>

            <div x-show="results.length > 0" class="mt-4 space-y-2">
                <template x-for="(result, idx) in results" :key="`${result.file_name}-${idx}`">
                    <div :class="result.success
                        ? 'border-green-200 bg-green-50 dark:border-green-700 dark:bg-green-900/20'
                        : 'border-red-200 bg-red-50 dark:border-red-700 dark:bg-red-900/20'"
                        class="rounded-lg border p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="result.file_name"></p>
                                <p class="text-xs text-gray-600 dark:text-gray-300" x-text="result.message"></p>
                            </div>
                            <span :class="result.success
                                ? 'text-green-700 dark:text-green-300'
                                : 'text-red-700 dark:text-red-300'"
                                class="text-xs font-bold uppercase" x-text="result.success ? 'OK' : 'Error'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function manualUploadManager() {
            return {
                files: [],
                isDragOver: false,
                uploading: false,
                results: [],
                summary: null,
                errorMessage: '',

                handleSelect(event) {
                    const selectedFiles = Array.from(event.target.files || []);
                    this.addFiles(selectedFiles);
                    event.target.value = '';
                },

                handleDrop(event) {
                    this.isDragOver = false;
                    const droppedFiles = Array.from(event.dataTransfer.files || []);
                    this.addFiles(droppedFiles);
                },

                addFiles(newFiles) {
                    this.errorMessage = '';
                    for (const file of newFiles) {
                        const extension = file.name.split('.').pop()?.toLowerCase();
                        if (!['json', 'txt'].includes(extension || '')) {
                            this.errorMessage = `El archivo ${file.name} no tiene un formato permitido.`;
                            continue;
                        }

                        if (file.size > 5 * 1024 * 1024) {
                            this.errorMessage = `El archivo ${file.name} supera el límite de 5 MB.`;
                            continue;
                        }

                        const exists = this.files.some((existing) =>
                            existing.name === file.name &&
                            existing.size === file.size &&
                            existing.lastModified === file.lastModified
                        );

                        if (!exists) {
                            this.files.push(file);
                        }
                    }
                },

                removeFile(index) {
                    this.files.splice(index, 1);
                },

                clearFiles() {
                    this.files = [];
                    this.errorMessage = '';
                },

                formatFileSize(bytes) {
                    if (bytes < 1024) return `${bytes} B`;
                    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
                    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
                },

                async uploadFiles() {
                    this.errorMessage = '';
                    this.results = [];
                    this.summary = null;

                    if (this.files.length === 0) {
                        this.errorMessage = 'Debe seleccionar al menos un archivo.';
                        return;
                    }

                    this.uploading = true;

                    try {
                        const formData = new FormData();
                        this.files.forEach((file) => formData.append('dte_json_files[]', file));

                        const response = await fetch('{{ route('business.received-documents.manual-upload.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        this.results = data.results || [];
                        this.summary = data.summary || null;

                        if (!response.ok) {
                            this.errorMessage = data.message || 'Ocurrió un error al cargar los archivos.';
                            return;
                        }

                        if (!data.success) {
                            this.errorMessage = data.message || 'La carga finalizó con errores en algunos archivos.';
                            return;
                        }

                        this.clearFiles();
                    } catch (error) {
                        this.errorMessage = 'Error de red al procesar los archivos.';
                    } finally {
                        this.uploading = false;
                    }
                }
            }
        }
    </script>
@endpush
