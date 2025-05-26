@php
$tipo_generacion = [
    1 => 'Físico',
    2 => 'Electrónico',
];
$tipos_documentos = [
    '01' => 'Factura Electrónica',
    '03' => 'Comprobante de Crédito Fiscal',
    '04' => 'Nota de Remisión',
    '05' => 'Nota de Crédito',
    '06' => 'Nota de Débito',
    '07' => 'Comprobante de Retención',
    '11' => 'Factura de Exportación',
    '14' => 'Factura de Sujeto Excluido'
];
@endphp
@if (isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0)
    @foreach ($dte['documentos_relacionados'] as $documento)
        <x-tr :last="$loop->last">
            <x-td>{{ $tipos_documentos[$documento['tipo_documento']] }}</x-td>
            <x-td>{{ $tipo_generacion[$documento['tipo_generacion']] }}</x-td>
            <x-td>{{ $documento['numero_documento'] }}</x-td>
            <x-td>{{ $documento['fecha_documento'] }}</x-td>
            <x-td :last="true">
                <x-button type="button" icon="trash"
                    data-action="{{ Route('business.dte.related-documents.delete', $documento['id']) }}" size="small"
                    typeButton="danger" class="btn-delete" text="Eliminar" />
            </x-td>
        </x-tr>
    @endforeach
@else
    <x-tr :last="true">
        <x-td colspan="5" class="text-center" :last="true">
            No hay documentos relacionados
        </x-td>
    </x-tr>
@endif
