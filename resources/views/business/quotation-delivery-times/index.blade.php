@extends('layouts.auth-template')
@section('title', 'Tiempos de entrega para cotizaciones')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">
                Tiempos de entrega para cotizaciones
            </h1>
            <x-button type="a" href="{{ Route('business.quotations.index') }}" typeButton="secondary" text="Volver"
                icon="arrow-left" class="w-full sm:w-auto" />
        </div>

        <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Agregar tiempo de entrega</h2>
            <form action="{{ Route('business.quotation-delivery-times.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" label="Nombre" name="name" required
                            placeholder="Ej: 3 a 5 dias habiles" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-button type="submit" typeButton="primary" text="Guardar" icon="save" />
                </div>
            </form>
        </div>

        <div class="mt-6 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tiempos de entrega registrados</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="border-b border-gray-200 text-xs uppercase dark:border-gray-700">
                        <tr>
                            <th class="px-3 py-2">Nombre</th>
                            <th class="px-3 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryTimes as $deliveryTime)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-2">
                                    <input type="text" name="name" value="{{ $deliveryTime->name }}"
                                        form="delivery-time-form-{{ $deliveryTime->id }}"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        required>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ Route('business.quotation-delivery-times.update', $deliveryTime->id) }}"
                                            method="POST" id="delivery-time-form-{{ $deliveryTime->id }}">
                                            @csrf
                                            @method('PUT')
                                            <x-button type="submit" typeButton="secondary" size="small" text="Guardar"
                                                icon="save" />
                                        </form>
                                        <form action="{{ Route('business.quotation-delivery-times.destroy', $deliveryTime->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" typeButton="danger" size="small" text="Eliminar"
                                                icon="trash" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-4 text-center" colspan="2">No hay tiempos de entrega registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
