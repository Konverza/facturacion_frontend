<input type="hidden" id="tipo_dte" name="tipo_dte" value="{{ $number }}">
@if ($default_pos)
    <div class="flex flex-col gap-4 sm:flex-row">
        <input type="hidden" id="pos_id" name="pos_id" value="{{ $default_pos->id }}">
        <div class="flex-1">
            <x-input type="text" label="Sucursal" name="sucursal" value="{{ $default_pos->sucursal->nombre }}"
                readonly />
        </div>
        <div class="flex-1">
            <x-input type="text" label="Punto de Venta" name="punto_venta" value="{{ $default_pos->nombre }}"
                readonly />
        </div>
    </div>
    <div class="mt-4">
        <x-input type="textarea" label="Dirección" name="complemento_emisor"
            value="{{ $default_pos->sucursal->complemento }}, {{ $departamentos[$default_pos->sucursal->departamento] }}"
            readonly />
    </div>
    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-input type="text" label="Correo electrónico" icon="email" name="correo_emisor" readonly
                placeholder="example@examp.com" value="{{ $default_pos->sucursal->correo }}" />
        </div>
        <div class="flex-1">
            <x-input type="text" label="Teléfono" icon="phone" name="telefono_emisor" placeholder="XXXX XXXX"
                readonly value="{{ $default_pos->sucursal->telefono }}" />
        </div>
    </div>
@else
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="flex-1">
            <x-select id="sucursal_select" :options="$sucursals" label="Sucursal" name="sucursal"
                data-action="{{ Route('business.puntos-venta-html.index') }}" data-business-id="{{ $business->id }}" />
        </div>
        <div class="flex-1" id="punto_venta_select">
            <x-select name="pos_id" id="punto_venta" label="Punto de Venta" :options="[
                'seleccione_punto_venta' => 'Seleccione un Punto de Venta',
            ]" required/>
        </div>
    </div>
    <div class="hidden" id="datos-sucursal">
        <div class="mt-4">
            <x-input type="textarea" label="Dirección" name="complemento_emisor" id="complemento_emisor" readonly />
        </div>
        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
            <div class="flex-1">
                <x-input type="text" label="Correo electrónico" icon="email" name="correo_emisor"
                    id="correo_emisor" readonly />
            </div>
            <div class="flex-1">
                <x-input type="text" label="Teléfono" icon="phone" name="telefono_emisor" id="telefono_emisor"
                    readonly />
            </div>
        </div>
    </div>
@endif
