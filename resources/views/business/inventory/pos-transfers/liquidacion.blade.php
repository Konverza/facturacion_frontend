@extends('layouts.auth-template')
@section('title', 'Liquidación de Devolución')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                    Liquidación de Devolución
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Transferencia #{{ $traslado->numero_transferencia }}
                </p>
            </div>
            <x-button 
                type="button" 
                text="Volver" 
                icon="arrow-left" 
                typeButton="secondary"
                onclick="window.location.href='{{ route('business.inventory.transfers.show', $traslado->id) }}'"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <x-icon icon="map-pin" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Origen</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $traslado->puntoVentaOrigen->nombre }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <x-icon icon="flag" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Destino</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $traslado->sucursalDestino->nombre }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <x-icon icon="box" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Items</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $traslado->items->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-950 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <div class="flex">
                <x-icon icon="alert-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" />
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Instrucciones de Liquidación
                    </h3>
                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                        Ingresa la cantidad real recibida de cada producto. El sistema detectará automáticamente las discrepancias entre lo esperado y lo recibido.
                    </p>
                </div>
            </div>
        </div>

        <form id="form-liquidacion" class="bg-white dark:bg-gray-950 rounded-lg shadow border border-gray-200 dark:border-gray-800">
            @csrf
            
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Productos Devueltos
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Código</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3 text-center">Cantidad Esperada</th>
                                <th class="px-4 py-3 text-center">Cantidad Real</th>
                                <th class="px-4 py-3 text-center">Diferencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($traslado->items as $item)
                            <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="px-4 py-3 font-medium">{{ $item->businessProduct->codigo }}</td>
                                <td class="px-4 py-3">{{ $item->businessProduct->descripcion }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($item->cantidad_solicitada, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        type="number" 
                                        name="items[{{ $loop->index }}][item_id]" 
                                        value="{{ $item->id }}" 
                                        hidden
                                    >
                                    <input 
                                        type="number" 
                                        name="items[{{ $loop->index }}][cantidad_real]" 
                                        step="0.01" 
                                        min="0" 
                                        value="{{ $item->cantidad_solicitada }}"
                                        class="cantidad-real bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 text-center dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                                        data-esperada="{{ $item->cantidad_solicitada }}"
                                        data-index="{{ $loop->index }}"
                                        required
                                    >
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span id="diferencia-{{ $loop->index }}" class="font-semibold text-green-600 dark:text-green-400">
                                        0.00
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <label for="observaciones" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Observaciones sobre la Liquidación
                    </label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="4" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white"
                        placeholder="Describe cualquier discrepancia o situación especial..."></textarea>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                <div class="flex justify-end gap-3">
                    <x-button 
                        type="button" 
                        text="Cancelar" 
                        typeButton="secondary"
                        onclick="window.location.href='{{ route('business.inventory.transfers.show', $traslado->id) }}'"
                    />
                    <x-button 
                        type="submit" 
                        text="Completar Liquidación" 
                        icon="check" 
                        typeButton="primary"
                    />
                </div>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        // Calcular diferencias en tiempo real
        document.querySelectorAll('.cantidad-real').forEach(input => {
            input.addEventListener('input', function() {
                const index = this.dataset.index;
                const esperada = parseFloat(this.dataset.esperada);
                const real = parseFloat(this.value) || 0;
                const diferencia = real - esperada;
                
                const diferenciaSpan = document.getElementById(`diferencia-${index}`);
                diferenciaSpan.textContent = diferencia.toFixed(2);
                
                // Cambiar color según la diferencia
                diferenciaSpan.classList.remove('text-green-600', 'dark:text-green-400', 'text-red-600', 'dark:text-red-400', 'text-gray-600', 'dark:text-gray-400');
                
                if (diferencia > 0) {
                    diferenciaSpan.classList.add('text-green-600', 'dark:text-green-400');
                } else if (diferencia < 0) {
                    diferenciaSpan.classList.add('text-red-600', 'dark:text-red-400');
                } else {
                    diferenciaSpan.classList.add('text-gray-600', 'dark:text-gray-400');
                }
            });
        });

        // Enviar formulario
        document.getElementById('form-liquidacion').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                items: [],
                observaciones: formData.get('observaciones')
            };

            // Construir array de items
            let index = 0;
            while (formData.has(`items[${index}][item_id]`)) {
                data.items.push({
                    item_id: formData.get(`items[${index}][item_id]`),
                    cantidad_real: formData.get(`items[${index}][cantidad_real]`)
                });
                index++;
            }

            // Confirmar antes de procesar
            const result = await Swal.fire({
                title: '¿Confirmar Liquidación?',
                text: 'Esta acción no se puede deshacer',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await axios.post(
                    '{{ route("business.inventory.transfers.procesar-liquidacion", $traslado->id) }}', 
                    data
                );
                
                if (response.data.success) {
                    let mensaje = response.data.message;
                    if (response.data.tiene_discrepancias) {
                        mensaje += '\n\nSe detectaron discrepancias en las cantidades.';
                    }
                    
                    await Swal.fire('Éxito', mensaje, 'success');
                    window.location.href = '{{ route("business.inventory.transfers.show", $traslado->id) }}';
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', error.response?.data?.message || 'Error al procesar liquidación', 'error');
            }
        });
    </script>
    @endpush
@endsection
