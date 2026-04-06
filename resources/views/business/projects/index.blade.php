@extends('layouts.auth-template')
@section('title', 'Proyectos')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex w-full flex-wrap items-center justify-between gap-2">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">Proyectos</h1>
            <x-button type="a" href="{{ Route('business.projects.create') }}" typeButton="primary"
                text="Nuevo proyecto" icon="plus" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4">
            <x-table :datatable="false">
                <x-slot name="thead">
                    <x-tr>
                        <x-th>#</x-th>
                        <x-th>Nombre</x-th>
                        <x-th>Estado</x-th>
                        <x-th>Cotización vinculada</x-th>
                        <x-th>Actualizado</x-th>
                        <x-th :last="true">Acciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @forelse ($projects as $project)
                        <x-tr :last="$loop->last">
                            <x-td>{{ ($projects->currentPage() - 1) * $projects->perPage() + $loop->iteration }}</x-td>
                            <x-td>{{ $project->name }}</x-td>
                            <x-td>
                                @php
                                    $status = (string) ($project->status ?? 'draft');
                                    $statusLabelMap = [
                                        'draft' => 'Borrador',
                                        'comparison' => 'Comparacion',
                                        'costing' => 'Costeo',
                                        'quotation' => 'Cotizacion',
                                        'facturada' => 'Facturada',
                                    ];
                                    $statusClassMap = [
                                        'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200',
                                        'comparison' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'costing' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                        'quotation' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
                                        'facturada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                    ];

                                    $statusLabel = $statusLabelMap[$status] ?? ucfirst($status);
                                    $statusClasses = $statusClassMap[$status]
                                        ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </x-td>
                            <x-td>
                                @if ($project->quotation_id)
                                    #{{ $project->quotation_id }}
                                @else
                                    -
                                @endif
                            </x-td>
                            <x-td>
                                <div class="text-xs">
                                    <div class="font-semibold">{{ $project->updated_at?->format('d/m/Y') }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">{{ $project->updated_at?->format('h:i A') }}</div>
                                </div>
                            </x-td>
                            <x-td :last="true">
                                <div class="flex flex-wrap gap-2">
                                    <x-button type="a" href="{{ Route('business.projects.edit', $project->id) }}" icon="pencil"
                                        typeButton="info" text="Abrir" size="small" />
                                    <form action="{{ Route('business.projects.destroy', $project->id) }}" method="POST"
                                        id="form-delete-project-{{ $project->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="button" icon="trash" text="Eliminar" size="small" typeButton="danger"
                                            data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                            data-form="form-delete-project-{{ $project->id }}" class="buttonDelete" />
                                    </form>
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="6" class="py-8 text-center text-gray-500">No hay proyectos registrados.</x-td>
                        </x-tr>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="mt-4">{{ $projects->links() }}</div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Eliminar proyecto?"
            message="Esta accion no se puede deshacer." />
    </section>
@endsection
