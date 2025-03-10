@if (!empty($dte['otros_documentos']))
    @if (collect($dte['otros_documentos'])->pluck('medico')->filter()->count() > 0)
        @foreach ($dte['otros_documentos'] as $documento)
            @if (!is_null($documento['medico']))
                <x-tr :last="$loop->last">
                    <x-td>
                        {{ $documento['medico']['nombre'] }}
                    </x-td>
                    <x-td>
                        {{ $documento['medico']['tipo_servicio'] }}
                    </x-td>
                    <x-td>
                        {{ $documento['medico']['numero_documento'] }}
                    </x-td>
                    <x-td>
                        {{ $documento['medico']['tipo_documento'] }}
                    </x-td>
                    <x-td :last="true">
                        <x-button type="button" icon="trash" size="small" typeButton="danger" class="btn-delete"
                            text="Eliminar"
                            data-action="{{ Route('business.dte.associated-documents.delete', $documento['id']) }}" />
                    </x-td>
                </x-tr>
            @endif
        @endforeach
    @else
        <x-tr :last="true">
            <x-td colspan="4" class="text-center" :last="true">
                No hay medicos asociados
            </x-td>
        </x-tr>
    @endif
@else
    <x-tr :last="true">
        <x-td colspan="4" class="text-center" :last="true">
            No hay documentos asociados
        </x-td>
    </x-tr>
@endif
