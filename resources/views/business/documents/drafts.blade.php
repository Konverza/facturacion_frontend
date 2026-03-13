@extends('layouts.auth-template')
@section('title', 'Borradores de DTE')
@section('content')
    @php
        $types = [
            '01' => 'Factura Consumidor Final',
            '03' => 'Comprobante de crédito fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de crédito',
            '06' => 'Nota de débito',
            '07' => 'Comprobante de retención',
            '11' => 'Factura de exportación',
            '14' => 'Factura de sujeto excluido',
            '15' => 'Comprobante de Donación',
        ];
    @endphp

    <section class="my-4 px-4">
        <div class="flex w-full justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Borradores de DTE
            </h1>
            <x-button type="a" href="{{ Route('business.documents.index') }}" typeButton="secondary" text="Documentos Emitidos"
                icon="document" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4 pb-4">
            <x-table id="table-data-drafts" :datatable="false">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Tipo</x-th>
                        @if ($canSeeOthersDtes)
                            <x-th>Autor</x-th>
                        @endif
                        <x-th>Fecha</x-th>
                        <x-th :last="true">Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($drafts as $draft)
                        <x-tr :last="$loop->last">
                            <x-td>
                                {{ ($drafts->currentPage() - 1) * $drafts->perPage() + $loop->iteration }}
                            </x-td>
                            <x-td>{{ $types[$draft->type] ?? $draft->type }}</x-td>
                            @if ($canSeeOthersDtes)
                                <x-td>{{ $draft->user?->name ?? 'Sin autor' }}</x-td>
                            @endif
                            <x-td>
                                <div class="text-xs">
                                    <div class="font-semibold">{{ $draft->created_at?->format('d/m/Y') }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ $draft->created_at?->format('h:i A') }}</div>
                                </div>
                            </x-td>
                            <x-td :last="true">
                                <div class="flex items-center gap-2">
                                    <x-button type="a"
                                        href="{!! Route('business.dte.create', ['document_type' => $draft->type]) . '&id=' . $draft->id !!}"
                                        icon="arrow-right" typeButton="secondary" onlyIcon />

                                    <button type="button"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-600 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 dark:hover:bg-gray-900"
                                        title="Imprimir (próximamente)">
                                        <x-icon icon="printer" class="size-5" />
                                    </button>

                                    <form action="{{ Route('business.delete-dte', $draft->id) }}" method="POST"
                                        id="form-delete-draft-{{ $draft->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="button" icon="trash" typeButton="danger" class="buttonDelete"
                                            data-modal-toggle="deleteModal" data-modal-target="deleteModal"
                                            data-form="form-delete-draft-{{ $draft->id }}" onlyIcon />
                                    </form>
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="{{ $canSeeOthersDtes ? 5 : 4 }}" class="text-center py-8">
                                <div class="flex flex-col items-center gap-3">
                                    <x-icon icon="file-report" class="size-12 text-gray-400 dark:text-gray-600" />
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <p class="text-lg font-medium">No hay borradores</p>
                                        <p class="text-sm">Aún no se han guardado DTEs como borrador</p>
                                    </div>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="mt-4">
                {{ $drafts->links() }}
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el borrador?"
            message="No podrás recuperar este registro" />
    </section>
@endsection
