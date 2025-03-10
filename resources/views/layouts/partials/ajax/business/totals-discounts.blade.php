    <x-input type="number" label="Descuento a ventas gravadas" icon="percentage" placeholder="0.00"
        name="descuento_venta_gravadas" id="descuento_venta_gravada"
        value="{{ isset($dte['percentaje_descuento_venta_gravada']) ? $dte['percentaje_descuento_venta_gravada'] : 0 }}"
        min="0" max="100" />
    <x-input type="number" label="Descuento a ventas exentas" icon="percentage" placeholder="0.00"
        name="descuento_venta_exentas" id="descuento_venta_exentas" max="100" min="0"
        value="{{ isset($dte['percentaje_descuento_venta_exenta']) ? $dte['percentaje_descuento_venta_exenta'] : 0 }}" />
    <x-input type="number" label="Descuento a ventas no sujetas" icon="percentage" placeholder="0.00"
        name="descuento_venta_no_sujetas" id="descuento_venta_no_sujeta"
        value="{{ isset($dte['percentaje_descuento_venta_no_sujeta']) ? $dte['percentaje_descuento_venta_no_sujeta'] : 0 }}"
        max="100" min="0" />
