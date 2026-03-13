@extends('layouts.auth-template')
@section('title', 'Cotizaciones')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex w-full items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">Cotizaciones</h1>
            <x-button type="a" href="{{ Route('business.quotations.create') }}" typeButton="primary"
                text="Nueva cotizacion" icon="plus" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4">
            <x-table :datatable="false">
                <x-slot name="thead">
                    <x-tr>
                        <x-th>#</x-th>
                        <x-th>Nombre</x-th>
                        <x-th>Tipo DTE</x-th>
                        <x-th>Cliente</x-th>
                        <x-th>Estado</x-th>
                        <x-th>Actualizada</x-th>
                        <x-th :last="true">Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($quotations as $quotation)
                        @php
                            $content = is_array($quotation->content) ? $quotation->content : [];
                            $customerName = $content['customer']['nombre'] ?? 'Sin cliente';
                        @endphp
                        <x-tr :last="$loop->last">
                            <x-td>{{ ($quotations->currentPage() - 1) * $quotations->perPage() + $loop->iteration }}</x-td>
                            <x-td>{{ $quotation->name ?? 'Cotizacion' }}</x-td>
                            <x-td>{{ $quotation->type === '03' ? 'Credito fiscal' : 'Factura' }}</x-td>
                            <x-td>{{ $customerName }}</x-td>
                            <x-td>
                                @if ($quotation->linked_dte_code)
                                    <span class="text-xs font-semibold text-green-600 dark:text-green-400">Facturada</span>
                                @else
                                    <span class="text-xs font-semibold text-yellow-600 dark:text-yellow-400">Pendiente</span>
                                @endif
                            </x-td>
                            <x-td>
                                <div class="text-xs">
                                    <div class="font-semibold">{{ $quotation->updated_at?->format('d/m/Y') }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ $quotation->updated_at?->format('h:i A') }}</div>
                                </div>
                            </x-td>
                            <x-td :last="true">
                                <div class="flex flex-wrap gap-2">
                                    <x-button type="a" href="{{ Route('business.quotations.show', $quotation->id) }}" icon="eye"
                                        typeButton="secondary" text="Ver" size="small" />
                                    <x-button type="a" href="{{ Route('business.quotations.edit', $quotation->id) }}" icon="pencil"
                                        typeButton="info" text="Editar" size="small" />
                                    <form action="{{ Route('business.quotations.destroy', $quotation->id) }}" method="POST"
                                        id="form-delete-quotation-{{ $quotation->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="button" icon="trash" text="Eliminar" size="small" typeButton="danger"
                                            data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                            data-form="form-delete-quotation-{{ $quotation->id }}" class="buttonDelete" />
                                    </form>
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="7" class="py-8 text-center text-gray-500">No hay cotizaciones registradas.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="mt-4">{{ $quotations->links() }}</div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Eliminar cotizacion?"
            message="Esta accion no se puede deshacer." />
    </section>
@endsection
