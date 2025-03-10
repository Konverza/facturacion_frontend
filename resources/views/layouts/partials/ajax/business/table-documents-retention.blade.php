 @if (isset($dte['documentos_retencion']) && count($dte['documentos_retencion']) > 0)
     @foreach ($dte['documentos_retencion'] as $documento)
         <x-tr>
             <x-td>
                 {{ $documento['tipo_generacion'] }}
             </x-td>
             <x-td>
                 {{ $documento['numero_documento'] }}
             </x-td>
             <x-td>
                 {{ $documento['codigo_retencion'] }}
             </x-td>
             <x-td>
                 {{ $documento['descripcion_retencion'] }}
             </x-td>
             <x-td>
                 {{ $documento['fecha_documento'] }}
             </x-td>
             <x-td>
                 ${{ number_format($documento['monto_sujeto_retencion'], 2) }}
             </x-td>
             <x-td>
                 ${{ number_format($documento['iva_retenido'], 2) }}
             </x-td>
             <x-td :last="true">
                 <x-button type="button" icon="trash" size="small"
                     data-action="{{ Route('business.dte.documents.delete', $documento['id']) }}" typeButton="danger"
                     onlyIcon class="btn-delete" />
             </x-td>
         </x-tr>
     @endforeach
 @else
     <x-tr>
         <x-td :last="true" colspan="9" class="text-center">No hay documentos</x-td>
     </x-tr>
 @endif
 <x-tr>
     <x-td colspan="9" :last="true">
         <div class="flex items-center justify-end gap-4 text-end">
             Monto sujeto a retención
             <span>
                 ${{ number_format($dte['monto_sujeto_retencion_total'] ?? 0, 2) }}
             </span>
         </div>
     </x-td>
 </x-tr>
 <x-tr :last="true">
     <x-td colspan="9" :last="true">
         <div class="flex items-center justify-end gap-4 text-end">
             Total IVA retenido
             <span>
                 ${{ number_format($dte['total_iva_retenido'] ?? 0, 2) }}
             </span>
         </div>
     </x-td>
 </x-tr>
