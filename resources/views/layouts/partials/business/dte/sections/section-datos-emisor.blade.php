<input type="hidden" id="tipo_dte" name="tipo_dte" value="{{ $number }}">
<div class="flex flex-col gap-4 sm:flex-row">
    <div class="flex-1">
        <x-select id="actividad_economica_customer" :options="$actividades_economicas" label="Actividad económica"
            name="actividad_economica_emisor"
            value="{{ old('actividad_economica_emisor', $datos_empresa['codActividad']) }}" readonly
            selected="{{ old('actividad_economica_emisor', $datos_empresa['codActividad']) }}" />
    </div>
    <div class="flex-1">
        <x-select name="tipo_establecimiento" id="tipo_establecimiento"
            value="{{ old('tipo_establecimiento', $datos_empresa['tipoEstablecimiento']) }}"
            selected="{{ old('tipo_establecimiento', $datos_empresa['tipoEstablecimiento']) }}" readonly
            label="Tipo de establecimiento_emisor" :options="$tipos_establecimientos" />
    </div>
</div>
<div class="mt-4">
    <x-input type="textarea" label="Establecimiento / dirección" name="complemento_emisor"
        value="{{ old('complemento_emisor', $datos_empresa['complemento']) }}" readonly
        placeholder="Ingresar la dirección" />
</div>
<div class="mt-4 flex flex-col gap-4 sm:flex-row">
    <div class="flex-1">
        <x-input type="text" label="Correo electrónico" icon="email" name="correo_emisor" readonly
            placeholder="example@examp.com" value="{{ old('correo_emisor', $datos_empresa['correo']) }}" />
    </div>
    <div class="flex-1">
        <x-input type="text" label="Teléfono" icon="phone" name="telefono_emisor" placeholder="XXXX XXXX" readonly
            value="{{ old('telefono_emisor', $datos_empresa['telefono']) }}" />
    </div>
</div>
