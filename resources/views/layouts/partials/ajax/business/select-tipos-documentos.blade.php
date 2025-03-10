         <x-select label="Tipo de documento" name="tipo_documento" id="type_document" value="{{ $tipo_documento ?? '' }}"
             selected="{{ $tipo_documento ?? '' }}" :options="$tipos_documentos" />
