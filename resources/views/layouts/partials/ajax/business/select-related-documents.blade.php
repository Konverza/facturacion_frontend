       <x-select label="Documento relacionado" name="documento_relacionado" id="document" :required="isset($dte['documentos_relacionados']) && count($dte['documentos_relacionados']) > 0"
           :options="isset($dte['documentos_relacionados'])
               ? collect($dte['documentos_relacionados'])->mapWithKeys(function ($item) {
                   return [
                       $item['numero_documento'] => $item['tipo_documento'] . ' - ' . $item['numero_documento'],
                   ];
               })
               : []" value="{{ old('documento') }}" selected="{{ old('documento') }}" :search="false" />
