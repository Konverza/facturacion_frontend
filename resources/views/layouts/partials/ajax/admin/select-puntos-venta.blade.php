<x-select name="default_pos_id" label="Punto de Venta Predeterminado" id="default_pos_id" required :options="$puntos_venta" value="{{ $punto_venta ?? null }}"
    selected="{{ $punto_venta ?? null }}" 
    />
