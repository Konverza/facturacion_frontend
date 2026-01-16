@extends('layouts.auth-template')
@section('title', 'Descargas de DTEs')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Descarga de DTEs
            </h1>
        </div>

        <!-- Formulario para nueva solicitud -->
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                <x-icon icon="cube-plus" class="inline mr-2" />
                Nueva Descarga
            </h2>

            @if ($activeJob)
                <div
                    class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 dark:border-yellow-600 dark:bg-yellow-900/30">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <x-icon icon="warning" class="inline mr-1" />
                        Ya tienes una descarga en proceso. Debes esperar a que finalice antes de crear una nueva.
                    </p>
                </div>
            @else
                <form id="form-create-zip" class="space-y-4">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            <x-icon icon="filter" class="inline mr-1" />
                            Filtros
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <x-input type="date" name="emision_inicio" id="emision_inicio"
                                    label="Fecha Emisión Desde" required />
                            </div>
                            <div>
                                <x-input type="date" name="emision_fin" id="emision_fin" label="Fecha Emisión Hasta"
                                    required />
                            </div>
                            <div>
                                <x-input type="date" name="procesamiento_inicio" id="procesamiento_inicio" label="Fecha Procesamiento Desde" />
                            </div>
                            <div>
                                <x-input type="date" name="procesamiento_fin" id="procesamiento_fin" label="Fecha Procesamiento Hasta" />
                            </div>
                            <div class="flex-1">
                                <x-select id="tipo_dte" :options="$dtes_disponibles" name="tipo_dte"
                                    placeholder="Seleccione un tipo de DTE" :search="false"
                                    label="Buscar por tipo de DTE" />
                            </div>
                            <div>
                                <x-select id="codSucursal" :options="$sucursal_options" name="codSucursal"
                                    placeholder="Seleccione una sucursal" label="Buscar por sucursal" />
                            </div>
                            <div>
                                <x-select id="codPuntoVenta" :options="$puntos_venta_options" name="codPuntoVenta"
                                    placeholder="Seleccione un punto de venta" label="Buscar por punto de venta" />
                            </div>
                            <div>
                                <x-select id="documento_receptor" :options="$receptores_unicos" name="documento_receptor"
                                    placeholder="Seleccione un receptor" label="Buscar por receptor" :search="false" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <x-button type="button" typeButton="success" icon="download" id="btn-create-zip"
                            text="Generar ZIP" />
                    </div>
                </form>
            @endif
        </div>

        <!-- Trabajo activo -->
        @if ($activeJob)
            <div id="active-job-container"
                class="mb-6 rounded-lg border border-blue-300 bg-blue-50 p-6 dark:border-blue-600 dark:bg-blue-900/30">
                <h2 class="mb-4 text-xl font-semibold text-blue-900 dark:text-blue-100">
                    <x-icon icon="clock" class="inline mr-2" />
                    Descarga en Proceso
                </h2>
                <div id="active-job-content" data-job-id="{{ $activeJob->id }}">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Período:</strong> {{ $activeJob->fecha_inicio->format('d/m/Y') }} -
                                {{ $activeJob->fecha_fin->format('d/m/Y') }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Creado:</strong> {{ $activeJob->created_at->format('d/m/Y H:i') }}
                            </p>
                            @if (
                                $activeJob->tipo_dte ||
                                    $activeJob->estado ||
                                    $activeJob->cod_sucursal ||
                                    $activeJob->documento_receptor ||
                                    $activeJob->busqueda)
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
                                    <x-icon icon="filter" class="inline mr-1" />
                                    <strong>Con filtros aplicados</strong>
                                </p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Estado:</strong> <span id="job-status"
                                    class="font-semibold">{{ ucfirst($activeJob->status) }}</span>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <strong>Progreso:</strong> <span id="job-progress-text">{{ $activeJob->processed_dtes }} /
                                    {{ $activeJob->total_dtes }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Barra de progreso -->
                    <div class="mt-4">
                        <div class="mb-1 flex justify-between">
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-400">Procesando...</span>
                            <span id="job-progress-percentage"
                                class="text-sm font-medium text-blue-700 dark:text-blue-400">{{ $activeJob->getProgressPercentage() }}%</span>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                            <div id="job-progress-bar" class="h-2.5 rounded-full bg-blue-600 transition-all duration-500"
                                style="width: {{ $activeJob->getProgressPercentage() }}%"></div>
                        </div>
                    </div>

                    <!-- Botón de descarga (oculto hasta completar) -->
                    <div id="download-button-container" class="mt-4 hidden">
                        <a href="{{ route('business.documents.zip.download', $activeJob->id) }}" id="btn-download-zip"
                            class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            <x-icon icon="download" class="mr-2" />
                            Descargar ZIP
                        </a>
                    </div>

                    <!-- Mensaje de error (oculto por defecto) -->
                    <div id="error-message-container" class="mt-4 hidden rounded-lg bg-red-100 p-4 dark:bg-red-900/30">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            <x-icon icon="circle-x" class="inline mr-1" />
                            <span id="error-message-text"></span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Historial de descargas -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                <x-icon icon="history" class="inline mr-2" />
                Historial de Descargas
            </h2>

            @if ($recentJobs->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No hay descargas previas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Período</th>
                                <th scope="col" class="px-6 py-3">Creado</th>
                                <th scope="col" class="px-6 py-3">Filtros</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3">DTEs</th>
                                <th scope="col" class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentJobs as $job)
                                <tr class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                                    <td class="px-6 py-4">
                                        {{ $job->fecha_inicio->format('d/m/Y') }} - {{ $job->fecha_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $job->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($job->tipo_dte || $job->estado || $job->cod_sucursal || $job->documento_receptor || $job->busqueda)
                                            <span class="text-blue-600 dark:text-blue-400"
                                                title="{{ $job->getFiltersDescription() }}">
                                                <x-icon icon="filter" class="inline" />
                                                Sí
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($job->status === 'completed')
                                            <span
                                                class="rounded bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Completado
                                            </span>
                                        @elseif($job->status === 'failed')
                                            <span
                                                class="rounded bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Fallido
                                            </span>
                                        @elseif($job->status === 'processing')
                                            <span
                                                class="rounded bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                Procesando
                                            </span>
                                        @else
                                            <span
                                                class="rounded bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $job->processed_dtes }} / {{ $job->total_dtes }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            @if ($job->status === 'completed' && $job->fileExists())
                                                <a href="{{ route('business.documents.zip.download', $job->id) }}"
                                                    class="download-zip-link font-medium text-blue-600 hover:underline dark:text-blue-500">
                                                    <x-icon icon="download" class="inline" /> Descargar
                                                </a>
                                            @endif

                                            @if (!$job->isInProgress())
                                                <button onclick="deleteJob({{ $job->id }})"
                                                    class="font-medium text-red-600 hover:underline dark:text-red-500">
                                                    <x-icon icon="trash" class="inline" /> Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        let pollingInterval = null;

        // Crear nueva solicitud de ZIP
        document.getElementById('btn-create-zip')?.addEventListener('click', async function() {
            const form = document.getElementById('form-create-zip');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            if (!data.emision_inicio || !data.emision_fin) {
                alert('Por favor complete las fechas de emisión');
                return;
            }

            this.disabled = true;
            const originalHTML = this.innerHTML;
            this.innerHTML =
                '<svg class="inline animate-spin mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creando solicitud...';

            try {
                const response = await fetch('{{ route('business.documents.zip.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();

                if (responseData.success) {
                    window.location.reload();
                } else {
                    alert(responseData.message || 'Error al crear la solicitud');
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
                this.disabled = false;
                this.innerHTML = originalHTML;
            }
        });

        // Polling para actualizar el estado del trabajo activo
        function updateActiveJobStatus() {
            const activeJobContent = document.getElementById('active-job-content');
            if (!activeJobContent) return;

            const jobId = activeJobContent.dataset.jobId;

            fetch(`{{ url('business/documents/zip/status') }}/${jobId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const job = data.job;

                        // Actualizar estado
                        document.getElementById('job-status').textContent =
                            job.status === 'completed' ? 'Completado' :
                            job.status === 'processing' ? 'Procesando' :
                            job.status === 'failed' ? 'Fallido' : 'Pendiente';

                        // Actualizar progreso
                        document.getElementById('job-progress-text').textContent =
                            `${job.processed_dtes} / ${job.total_dtes}`;
                        document.getElementById('job-progress-percentage').textContent =
                            `${job.progress}%`;
                        document.getElementById('job-progress-bar').style.width =
                            `${job.progress}%`;

                        // Si está completado, mostrar botón de descarga
                        if (job.status === 'completed' && job.can_download) {
                            const downloadContainer = document.getElementById('download-button-container');
                            downloadContainer.classList.remove('hidden');

                            // Agregar evento para ocultar loader al descargar
                            const downloadBtn = document.getElementById('btn-download-zip');
                            if (downloadBtn && !downloadBtn.dataset.listenerAdded) {
                                downloadBtn.addEventListener('click', function() {
                                    // Mostrar loader
                                    document.getElementById('loader')?.classList.remove('hidden');

                                    // Ocultar loader después de 2 segundos (cuando comience la descarga)
                                    setTimeout(() => {
                                        document.getElementById('loader')?.classList.add('hidden');
                                        // Recargar página para actualizar tabla
                                        setTimeout(() => window.location.reload(), 500);
                                    }, 2000);
                                });
                                downloadBtn.dataset.listenerAdded = 'true';
                            }

                            stopPolling();
                        }

                        // Si falló, mostrar error
                        if (job.status === 'failed') {
                            const errorContainer = document.getElementById('error-message-container');
                            const errorText = document.getElementById('error-message-text');
                            errorContainer.classList.remove('hidden');
                            errorText.textContent = job.error_message || 'Error desconocido';
                            stopPolling();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al actualizar estado:', error);
                });
        }

        function startPolling() {
            if (pollingInterval) return;
            pollingInterval = setInterval(updateActiveJobStatus, 3000); // Cada 3 segundos
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        // Eliminar trabajo
        async function deleteJob(jobId) {
            if (!confirm('¿Está seguro de eliminar esta descarga?')) return;

            try {
                const response = await fetch(`{{ url('business/documents/zip') }}/${jobId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al eliminar');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            }
        }

        // Iniciar polling si hay trabajo activo
        if (document.getElementById('active-job-content')) {
            startPolling();
            updateActiveJobStatus(); // Primera actualización inmediata
        }

        // Detener polling al salir de la página
        window.addEventListener('beforeunload', stopPolling);

        // Manejar clicks en enlaces de descarga de la tabla
        document.querySelectorAll('.download-zip-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // Mostrar loader
                document.getElementById('loader')?.classList.remove('hidden');

                // Ocultar loader después de 2 segundos
                setTimeout(() => {
                    document.getElementById('loader')?.classList.add('hidden');
                }, 2000);
            });
        });
    </script>
@endpush
