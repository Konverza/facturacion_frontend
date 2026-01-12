@extends('layouts.auth-template')
@section('title', 'Documentos Recibidos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between mb-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Importación de DTEs desde Hacienda
            </h1>
        </div>

        <div class="mb-6">
            <x-button type="a" typeButton="secondary" icon="arrow-left" href="{{ route('business.received-documents.index') }}"
                text="Volver a DTEs Recibidos" />
        </div>

        <!-- Proceso Activo -->
        @if ($activeProcess)
            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-6 shadow-sm dark:border-blue-800 dark:bg-blue-900/20"
                x-data="{
                    processId: {{ $activeProcess->id }},
                    status: '{{ $activeProcess->status }}',
                    progress: {{ $activeProcess->progress_percentage }},
                    totalDtes: {{ $activeProcess->total_dtes }},
                    processedDtes: {{ $activeProcess->processed_dtes }},
                    failedDtes: {{ $activeProcess->failed_dtes }},
                    startedAt: '{{ $activeProcess->started_at?->diffForHumans() ?? 'N/A' }}',
                    errorMessage: '{{ $activeProcess->error_message }}',
                    async updateProgress() {
                        try {
                            const response = await fetch(`/business/received-documents/import/progress/${this.processId}`);
                            const data = await response.json();
                            if (data.success) {
                                this.status = data.process.status;
                                this.progress = data.process.progress_percentage;
                                this.totalDtes = data.process.total_dtes;
                                this.processedDtes = data.process.processed_dtes;
                                this.failedDtes = data.process.failed_dtes;
                                this.errorMessage = data.process.error_message;
                
                                // Si completó o falló, recargar página después de 2 segundos
                                if (!data.process.is_in_progress) {
                                    setTimeout(() => window.location.reload(), 2000);
                                }
                            }
                        } catch (error) {
                            console.error('Error al actualizar progreso:', error);
                        }
                    }
                }" x-init="setInterval(() => updateProgress(), 3000)">

                <div class="flex items-center gap-3 mb-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                        x-show="status === 'pending' || status === 'downloading' || status === 'processing'"></div>
                    <h2 class="text-xl font-semibold text-blue-900 dark:text-blue-100">
                        Proceso en curso
                    </h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                Estado: <span x-text="status.toUpperCase()" class="font-bold"></span>
                            </span>
                            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                <span x-text="progress"></span>%
                            </span>
                        </div>
                        <div class="w-full bg-blue-200 rounded-full h-4 dark:bg-blue-800">
                            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" :style="`width: ${progress}%`">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div class="bg-white dark:bg-gray-800 p-3 rounded">
                            <p class="text-gray-600 dark:text-gray-400">Total DTEs</p>
                            <p class="text-2xl font-bold text-blue-600" x-text="totalDtes"></p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded">
                            <p class="text-gray-600 dark:text-gray-400">Procesados</p>
                            <p class="text-2xl font-bold text-green-600" x-text="processedDtes"></p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded">
                            <p class="text-gray-600 dark:text-gray-400">Fallidos</p>
                            <p class="text-2xl font-bold text-red-600" x-text="failedDtes"></p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded">
                            <p class="text-gray-600 dark:text-gray-400">Iniciado</p>
                            <p class="text-sm font-medium" x-text="startedAt"></p>
                        </div>
                    </div>

                    <div x-show="errorMessage" class="bg-red-100 dark:bg-red-900/20 p-3 rounded">
                        <p class="text-red-800 dark:text-red-200" x-text="errorMessage"></p>
                    </div>
                </div>
            </div>
        @else
            <!-- Iniciar Nuevo Proceso -->
            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                x-data="{
                    loading: false,
                    async startImport() {
                        if (this.loading) return;
                        if (!confirm('¿Está seguro de iniciar la importación de DTEs desde Hacienda?')) return;
                
                        this.loading = true;
                        try {
                            const response = await fetch('/business/received-documents/import/start', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                }
                            });
                            const data = await response.json();
                
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Error al iniciar la importación');
                                this.loading = false;
                            }
                        } catch (error) {
                            alert('Error al iniciar la importación: ' + error.message);
                            this.loading = false;
                        }
                    }
                }">

                <div class="text-center">
                    <x-icon icon="cloud-download" class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        No hay procesos de importación en curso
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Inicie un nuevo proceso para descargar y procesar los DTEs recibidos desde el portal de Hacienda.
                    </p>
                    <button @click="startImport()" :disabled="loading"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-icon icon="play" class="h-5 w-5" x-show="!loading" />
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" x-show="loading">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        <span x-text="loading ? 'Iniciando...' : 'Iniciar Importación'"></span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Historial de Procesos -->
        @if ($processHistory->count() > 0)
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-icon icon="history" class="inline mr-1" />
                        Historial de Importaciones
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach ($processHistory as $process)
                            <div
                                class="flex items-center justify-between p-4 rounded-lg border {{ $process->status === 'completed' ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' }}">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        @if ($process->status === 'completed')
                                            <x-icon icon="circle-check" class="h-5 w-5 text-green-600" />
                                            <span class="font-semibold text-green-900 dark:text-green-100">Completado</span>
                                        @else
                                            <x-icon icon="circle-x" class="h-5 w-5 text-red-600" />
                                            <span class="font-semibold text-red-900 dark:text-red-100">Fallido</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <p>Total: {{ $process->total_dtes }} | Procesados: {{ $process->processed_dtes }} |
                                            Fallidos: {{ $process->failed_dtes }}</p>
                                        <p>Iniciado: {{ $process->started_at?->format('d/m/Y H:i:s') }} | Finalizado:
                                            {{ $process->completed_at?->format('d/m/Y H:i:s') }}</p>
                                        @if ($process->error_message)
                                            <p class="text-red-600 dark:text-red-400 mt-1">Error:
                                                {{ $process->error_message }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold {{ $process->status === 'completed' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $process->progress_percentage }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
