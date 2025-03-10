<x-select name="municipio" label="Municipio" id="municipio" required :options="$municipios" value="{{ $municipio ?? null }}"
    selected="{{ $municipio ?? null }}" />
