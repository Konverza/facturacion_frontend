@extends('layouts.auth-template')
@section('title', 'Notificaciones')
@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Notificaciones por Correo
            </h1>
            <x-button type="a" href="{{ route('admin.notifications.create') }}" typeButton="primary" icon="plus"
                text="Nueva Notificación" size="normal" />
        </div>

        <div class="rounded-lg border border-gray-300 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
            <h2 class="mb-4 text-xl font-bold text-gray-600 dark:text-white sm:text-2xl">
                Historial de Envíos
            </h2>

            @if ($recentJobs->isEmpty())
                <div class="py-8 text-center">
                    <x-icon icon="inbox" class="mx-auto size-16 text-gray-400 dark:text-gray-600" />
                    <p class="mt-4 text-gray-500 dark:text-gray-400">
                        No hay envíos recientes
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <x-table id="table-notifications">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th>Asunto</x-th>
                                <x-th>Total</x-th>
                                <x-th>Enviados</x-th>
                                <x-th>Fallidos</x-th>
                                <x-th>Estado</x-th>
                                <x-th>Fecha</x-th>
                                <x-th>Usuario</x-th>
                                <x-th :last="true">Acciones</x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @foreach ($recentJobs as $job)
                                <x-tr>
                                    <x-td>{{ $job['subject'] }}</x-td>
                                    <x-td>{{ $job['total'] }}</x-td>
                                    <x-td>
                                        <span class="font-semibold text-green-600 dark:text-green-400">
                                            {{ $job['sent'] }}
                                        </span>
                                    </x-td>
                                    <x-td>
                                        <span class="font-semibold text-red-600 dark:text-red-400">
                                            {{ $job['failed'] }}
                                        </span>
                                    </x-td>
                                    <x-td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
                                            ];
                                            $statusTexts = [
                                                'pending' => 'Pendiente',
                                                'processing' => 'Procesando',
                                                'completed' => 'Completado',
                                                'failed' => 'Fallido'
                                            ];
                                        @endphp
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$job['status']] ?? '' }}">
                                            {{ $statusTexts[$job['status']] ?? $job['status'] }}
                                        </span>
                                    </x-td>
                                    <x-td>{{ \Carbon\Carbon::parse($job['created_at'])->format('d/m/Y H:i') }}</x-td>
                                    <x-td>{{ $job['created_by'] ?? '-' }}</x-td>
                                    <x-td>
                                        <div class="flex gap-2">
                                            <x-button type="button" typeButton="info" icon="eye" onlyIcon="true"
                                                size="small" class="view-progress" data-job-id="{{ $job['id'] }}"
                                                title="Ver progreso" />
                                        </div>
                                    </x-td>
                                </x-tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                </div>
            @endif
        </div>
    </section>

    <!-- Modal de Progreso -->
    <div id="progress-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Progreso de Envío</h3>
                <button type="button" id="close-modal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <x-icon icon="xmark" class="size-6" />
                </button>
            </div>
            <div id="progress-content">
                <div class="mb-4">
                    <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Progreso: <span id="progress-text">0/0</span>
                    </p>
                    <div class="h-4 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div id="progress-bar" class="h-full bg-primary-500 transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Enviados:</p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400" id="progress-sent">0</p>
                    </div>
                    <div>
                        <p class="text-gray-600 dark:text-gray-400">Fallidos:</p>
                        <p class="text-lg font-bold text-red-600 dark:text-red-400" id="progress-failed">0</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Estado:</p>
                    <p class="font-semibold" id="progress-status">-</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const progressModal = document.getElementById('progress-modal');
                const closeModal = document.getElementById('close-modal');
                let currentJobId = null;
                let intervalId = null;

                // Abrir modal de progreso
                document.querySelectorAll('.view-progress').forEach(btn => {
                    btn.addEventListener('click', function() {
                        currentJobId = this.dataset.jobId;
                        progressModal.classList.remove('hidden');
                        progressModal.classList.add('flex');
                        startProgressPolling();
                    });
                });

                // Cerrar modal
                closeModal.addEventListener('click', function() {
                    stopProgressPolling();
                    progressModal.classList.add('hidden');
                    progressModal.classList.remove('flex');
                });

                // Cerrar modal al hacer clic fuera
                progressModal.addEventListener('click', function(e) {
                    if (e.target === progressModal) {
                        stopProgressPolling();
                        progressModal.classList.add('hidden');
                        progressModal.classList.remove('flex');
                    }
                });

                function startProgressPolling() {
                    updateProgress();
                    intervalId = setInterval(updateProgress, 2000); // Actualizar cada 2 segundos
                }

                function stopProgressPolling() {
                    if (intervalId) {
                        clearInterval(intervalId);
                        intervalId = null;
                    }
                }

                async function updateProgress() {
                    try {
                        const response = await fetch(`/admin/notifications/progress/${currentJobId}`);
                        const result = await response.json();

                        if (result.success) {
                            const data = result.data;
                            const percentage = data.total > 0 ? Math.round(((data.sent + data.failed) / data.total) * 100) : 0;

                            document.getElementById('progress-text').textContent = `${data.sent + data.failed}/${data.total}`;
                            document.getElementById('progress-bar').style.width = `${percentage}%`;
                            document.getElementById('progress-sent').textContent = data.sent;
                            document.getElementById('progress-failed').textContent = data.failed;
                            
                            const statusTexts = {
                                'pending': 'Pendiente',
                                'processing': 'Procesando',
                                'completed': 'Completado',
                                'failed': 'Fallido'
                            };
                            document.getElementById('progress-status').textContent = statusTexts[data.status] || data.status;

                            // Detener polling si está completado o falló
                            if (data.status === 'completed' || data.status === 'failed') {
                                stopProgressPolling();
                                // Recargar la página después de 2 segundos
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        }
                    } catch (error) {
                        console.error('Error al obtener progreso:', error);
                    }
                }
            });
        </script>
    @endpush
@endsection
