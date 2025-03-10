 @if (isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0)
     @foreach ($dte['documentos_relacionados'] as $documento)
         <x-tr :last="$loop->last">
             <x-td>{{ $documento['tipo_documento'] }}</x-td>
             <x-td>{{ $documento['tipo_generacion'] }}</x-td>
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
