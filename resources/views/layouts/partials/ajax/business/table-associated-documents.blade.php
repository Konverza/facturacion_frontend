@if (!empty($dte['otros_documentos']))
    @foreach ($dte['otros_documentos'] as $documento)
        @if (is_null($documento['medico']))
            <x-tr :last="true">
                <x-td>
                    {{ $documento['documento_asociado'] }}
                </x-td>
                <x-td>
                    {{ $documento['identificacion_documento'] }}
                </x-td>
                <x-td>
                    {{ $documento['descripcion_documento'] }}
                </x-td>
                @if ($dte['type'] === '11')
                    <x-td>
                        {{ $documento['placas'] ?? '' }}
                    </x-td>
                    <x-td>
                        {{ $documento['modo_transporte'] ?? '' }}
                    </x-td>
                    <x-td>
                        {{ $documento['numero_identificacion'] ?? '' }}
                    </x-td>
                    <x-td>
                        {{ $documento['nombre_conductor'] ?? '' }}
                    </x-td>
                @endif
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
            No hay documentos asociados
        </x-td>
    </x-tr>
@endif
