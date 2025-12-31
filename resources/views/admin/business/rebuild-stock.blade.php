@extends('layouts.auth-template')
@section('title', 'Reconstruir Stock - ' . $business->nombre)
@section('content')
    <section class="my-4 px-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                    Reconstruir Stock
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Negocio: <span class="font-semibold">{{ $business->nombre }}</span> ({{ $business->nit }})
                </p>
            </div>
            <div>
                <x-button type="a" href="{{ route('admin.business.index') }}" icon="arrow-left" typeButton="secondary"
                    text="Volver a negocios" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Panel de configuración -->
            <div class="lg:col-span-1">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h2 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Configuración
                    </h2>

                    <form id="rebuild-form">
                        <div class="space-y-4">
                            <div>
                                <label for="target_stock">Stock mínimo a reconstruir</label>
                                <x-input type="number" id="target_stock" name="target_stock" value="90000" min="0"
                                    step="0.01" class="w-full" required />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Solo se procesarán productos con este valor o mayor de stock.
                                </p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="dry_run" name="dry_run" value="1" checked
                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:ring-offset-gray-950">
                                <label for="dry_run" class="ml-2 text-sm text-gray-900 dark:text-gray-300">
                                    Modo prueba (no modificar datos)
                                </label>
                            </div>

                            <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                <div class="flex items-start">
                                    <x-icon icon="info" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                    <div class="ml-3 text-sm text-blue-700 dark:text-blue-300">
                                        <p class="font-medium">¿Cómo funciona?</p>
                                        <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                                            <li>Busca productos con el stock especificado</li>
                                            <li>Calcula: Stock Inicial + Entradas - Salidas</li>
                                            <li>Actualiza el stock y estado automáticamente</li>
                                            <li>En modo prueba, solo simula sin modificar</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <x-button type="button" id="btn-simulate" typeButton="secondary" text="Simular"
                                    icon="eye" class="flex-1" />
                                <x-button type="button" id="btn-execute" typeButton="primary" text="Ejecutar"
                                    icon="check" class="flex-1" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="mt-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">
                        Información del negocio
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total sucursales:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $business->sucursales->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total productos:</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $business->products->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de resultados -->
            <div class="lg:col-span-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-950">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Resultados
                        </h2>
                        <div id="status-badge" class="hidden"></div>
                    </div>

                    <div id="loading-container" class="hidden">
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-primary-600 dark:border-gray-700"></div>
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">Procesando...</p>
                        </div>
                    </div>

                    <div id="results-container" class="hidden">
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                            <pre id="output-text" class="max-h-[600px] overflow-auto whitespace-pre-wrap text-xs text-gray-900 dark:text-gray-100"></pre>
                        </div>

                        <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                            <div class="flex items-start">
                                <x-icon icon="alert" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                                <div class="ml-3 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p class="font-medium">Recomendaciones</p>
                                    <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                                        <li>Siempre ejecuta primero en modo prueba para verificar resultados</li>
                                        <li>Revisa los cálculos antes de aplicar cambios reales</li>
                                        <li>Considera hacer un respaldo de la base de datos antes de ejecutar</li>
                                        <li>Si encuentras errores, verifica los movimientos del producto</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="empty-state" class="flex flex-col items-center justify-center py-12">
                        <x-icon icon="box" class="h-16 w-16 text-gray-300 dark:text-gray-600" />
                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            Configura los parámetros y haz clic en "Simular" o "Ejecutar"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const businessId = {{ $business->id }};
            const executeUrl = "{{ route('admin.business.execute-rebuild-stock', $business->id) }}";

            function showLoading() {
                $('#empty-state').addClass('hidden');
                $('#results-container').addClass('hidden');
                $('#loading-container').removeClass('hidden');
            }

            function showResults(data) {
                $('#loading-container').addClass('hidden');
                $('#empty-state').addClass('hidden');
                $('#results-container').removeClass('hidden');
                
                // Mostrar output
                $('#output-text').text(data.output || 'No hay resultados disponibles');

                // Actualizar badge de estado
                const badge = $('#status-badge');
                badge.removeClass('hidden');
                
                if (data.success) {
                    badge.html(`
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900/20 dark:text-green-300">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            ${data.message}
                        </span>
                    `);
                } else {
                    badge.html(`
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-300">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            ${data.message}
                        </span>
                    `);
                }
            }

            function executeRebuild(dryRun) {
                const targetStock = $('#target_stock').val();

                if (!targetStock || targetStock < 0) {
                    alert('Por favor, ingresa un stock objetivo válido');
                    return;
                }

                showLoading();

                axios.post(executeUrl, {
                    target_stock: targetStock,
                    dry_run: dryRun
                })
                .then(response => {
                    showResults(response.data);
                })
                .catch(error => {
                    $('#loading-container').addClass('hidden');
                    $('#results-container').removeClass('hidden');
                    
                    const errorMessage = error.response?.data?.message || 'Error desconocido';
                    const errorDetail = error.response?.data?.error || '';
                    
                    $('#output-text').text(`ERROR: ${errorMessage}\n\n${errorDetail}`);
                    
                    $('#status-badge').removeClass('hidden').html(`
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 dark:bg-red-900/20 dark:text-red-300">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Error en la ejecución
                        </span>
                    `);
                });
            }

            $('#btn-simulate').on('click', function() {
                executeRebuild(true);
            });

            $('#btn-execute').on('click', function() {
                const confirmed = confirm(
                    '¿Estás seguro de que deseas reconstruir el stock?\n\n' +
                    'Esta acción modificará los datos de stock en la base de datos.\n' +
                    'Se recomienda haber ejecutado una simulación primero.'
                );
                
                if (confirmed) {
                    executeRebuild(false);
                }
            });

            // Actualizar checkbox de dry_run al hacer clic en los botones
            $('#btn-simulate').on('click', function() {
                $('#dry_run').prop('checked', true);
            });

            $('#btn-execute').on('click', function() {
                $('#dry_run').prop('checked', false);
            });
        </script>
    @endpush
@endsection
