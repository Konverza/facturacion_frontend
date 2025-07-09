<x-select name="sucursal" label="Sucursal" id="sucursal" required :options="$sucursales" value="{{ $sucursal ?? null }}"
    selected="{{ $sucursal ?? null }}" 
    data-action="{{ route('admin.puntos_venta.json') }}"
    />
