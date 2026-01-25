@extends('layouts.auth-template')
@section('title', 'Descargas de DTEs recibidos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Descarga de DTEs recibidos
            </h1>
        </div>

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
                            <div class="flex-1">
                                <x-select id="tipo_dte" :options="$tipos_dte" name="tipo_dte"
                                    placeholder="Seleccione un tipo de DTE" :search="false"
                                    label="Buscar por tipo de DTE" />
                            </div>
                            <div>
                                <x-select id="documento_emisor" :options="$emisores_unicos" name="documento_emisor"
                                    placeholder="Seleccione un emisor" label="Buscar por emisor" :search="false" />
                            </div>
                            <div>
                                <x-input type="text" name="busqueda" id="busqueda" label="Búsqueda rápida"
                                    placeholder="Código generación, sello, documento o nombre" />
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
                            @if ($activeJob->tipo_dte || $activeJob->documento_emisor || $activeJob->busqueda)
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

                    <div id="download-button-container" class="mt-4 hidden">
                        <a href="{{ route('business.received-documents.zip.download', $activeJob->id) }}" id="btn-download-zip" data-download="true"
                            class="inline-flex items-center justify-center w-full px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            <x-icon icon="download" class="mr-2" />
                            Descargar ZIP
                        </a>
                    </div>

                    <div id="error-message-container" class="mt-4 hidden rounded-lg bg-red-100 p-4 dark:bg-red-900/30">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            <x-icon icon="circle-x" class="inline mr-1" />
                            <span id="error-message-text"></span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

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
                                        @if ($job->tipo_dte || $job->documento_emisor || $job->busqueda)
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
                                                <a href="{{ route('business.received-documents.zip.download', $job->id) }}" data-download="true"
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

        document.getElementById('btn-create-zip')?.addEventListener('click', async function() {
            const form = document.getElementById('form-create-zip');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            if (!data.emision_inicio || !data.emision_fin) {
                alert('Por favor complete las fechas de emisión');
                return;
            }

            try {
                const response = await fetch("{{ route('business.received-documents.zip.create') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    alert(result.message || 'Error al crear la solicitud');
                    return;
                }

                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear la solicitud');
            }
        });

        const activeJobContainer = document.getElementById('active-job-content');
        if (activeJobContainer) {
            const jobId = activeJobContainer.dataset.jobId;

            const updateStatus = async () => {
                try {
                    const response = await fetch(`{{ url('/business/received-documents/zip/status') }}/${jobId}`);
                    const data = await response.json();

                    if (data.success) {
                        const job = data.job;

                        document.getElementById('job-status').textContent = job.status.charAt(0).toUpperCase() + job.status.slice(1);
                        document.getElementById('job-progress-text').textContent = `${job.processed_dtes} / ${job.total_dtes}`;
                        document.getElementById('job-progress-percentage').textContent = `${job.progress}%`;
                        document.getElementById('job-progress-bar').style.width = `${job.progress}%`;

                        if (job.status === 'completed' && job.can_download) {
                            document.getElementById('download-button-container').classList.remove('hidden');
                            clearInterval(pollingInterval);
                        }

                        if (job.status === 'failed') {
                            document.getElementById('error-message-text').textContent = job.error_message || 'Error en el proceso';
                            document.getElementById('error-message-container').classList.remove('hidden');
                            clearInterval(pollingInterval);
                        }
                    }
                } catch (error) {
                    console.error('Error al actualizar estado:', error);
                }
            };

            pollingInterval = setInterval(updateStatus, 5000);
            updateStatus();
        }

        async function deleteJob(jobId) {
            if (!confirm('¿Seguro que deseas eliminar este registro?')) {
                return;
            }

            try {
                const response = await fetch(`{{ url('/business/received-documents/zip') }}/${jobId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                });

                const result = await response.json();

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert(result.message || 'Error al eliminar el trabajo');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el trabajo');
            }
        }
    </script>
@endpush
