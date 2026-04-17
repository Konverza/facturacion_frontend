<form action="{{ $formAction }}" method="POST">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <x-input type="text" name="name" id="quotation_name" label="Nombre de cotizacion"
            value="{{ old('name', $quotation->name ?? '') }}" required />

        <x-select label="Tipo de DTE al convertir" name="dte_type" id="quotation_dte_type"
            :options="['01' => 'Factura', '03' => 'Credito fiscal']"
            selected="{{ old('dte_type', $quotation->type ?? ($dte['type'] ?? '01')) }}"
            value="{{ old('dte_type', $quotation->type ?? ($dte['type'] ?? '01')) }}" :search="false" required />
    </div>

    <div class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Datos del cliente</h2>
            <div class="flex flex-col gap-2 sm:flex-row">
                <x-button type="button" text="Seleccionar cliente existente" typeButton="success" icon="user"
                    data-target="#selected-customer" class="show-modal w-full sm:w-auto" />
                <x-button type="a" href="{{ Route('business.customers.create') }}" target="_blank"
                    text="Crear cliente" typeButton="secondary" icon="plus" class="w-full sm:w-auto" />
            </div>
        </div>

        <input type="hidden" name="customer_id" id="customer_id"
            value="{{ old('customer_id', $dte['customer']['id'] ?? '') }}" />

        <div class="grid gap-4 md:grid-cols-2">
            <x-input type="text" name="numero_documento" id="numero_documento_customer" label="Numero de documento"
                value="{{ old('numero_documento', $dte['customer']['numDocumento'] ?? '') }}" />
            <x-input type="text" name="nrc_customer" id="nrc_customer" label="NRC"
                value="{{ old('nrc_customer', $dte['customer']['nrc'] ?? '') }}" />
            <x-input type="text" name="nombre_receptor" id="nombre_customer" label="Nombre del cliente"
                value="{{ old('nombre_receptor', $dte['customer']['nombre'] ?? '') }}" required />
            <x-input type="text" name="telefono" id="telefono_customer" label="Telefono"
                value="{{ old('telefono', $dte['customer']['telefono'] ?? '') }}" />
            <x-input type="email" name="correo" id="correo_customer" label="Correo"
                value="{{ old('correo', $dte['customer']['correo'] ?? '') }}" class="md:col-span-2" />
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
        <div class="mb-4 flex flex-col items-center justify-start gap-4 sm:flex-row">
            <div class="relative w-full sm:w-auto">
                <x-button type="button" icon="arrow-down" typeButton="info" text="Agregar detalle"
                    class="show-options w-full sm:w-auto" data-target="#options-add-quotation-item" size="normal"
                    data-align="right" />
                <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                    id="options-add-quotation-item">
                    <ul class="flex flex-col text-sm">
                        <li>
                            <button type="button"
                                class="show-drawer flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                data-target="#drawer-new-product"
                                aria-controls="drawer-new-product">
                                <x-icon icon="pencil" class="h-4 w-4" />
                                Producto o servicio
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <x-button type="button" text="Seleccionar producto existente" icon="arrow-double-next" typeButton="success"
                data-modal-target="selected-product" data-modal-toggle="selected-product" class="w-full sm:w-auto" />
        </div>

        <div id="table-products-dte" class="mt-4">
            @include('layouts.partials.ajax.business.table-products-dte', [
                'priceInputMode' => $priceInputMode ?? null,
            ])
        </div>
    </div>

    @php
        $missingQuotationConfig = !$hasPaymentMethods || !$hasDeliveryTimes;
    @endphp

    <div class="mt-6 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">Condiciones de cotizacion</h2>

        @if ($missingQuotationConfig)
            <div
                class="mt-3 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-700 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">
                Debes crear al menos una forma de pago y un tiempo de entrega antes de guardar la cotizacion.
            </div>
        @endif

        <div class="mt-4 grid gap-4 md:grid-cols-1">
            <x-input type="number" name="vigencia_dias" label="Vigencia (dias)" min="1" max="365"
                value="{{ old('vigencia_dias', $meta['vigencia_dias'] ?? 15) }}" required />

            <div class="space-y-2">
                <x-select label="Tiempo de entrega" name="delivery_time_id" id="quotation_delivery_time_id"
                    :options="$deliveryTimeOptions"
                    selected="{{ old('delivery_time_id', $selectedDeliveryTimeId) && isset($deliveryTimeOptions[old('delivery_time_id', $selectedDeliveryTimeId)]) ? $deliveryTimeOptions[old('delivery_time_id', $selectedDeliveryTimeId)] : 'Seleccionar' }}"
                    value="{{ old('delivery_time_id', $selectedDeliveryTimeId) }}" :search="true"
                    :required="$hasDeliveryTimes" />
                <div class="flex flex-col gap-2 sm:flex-row">
                    <x-input type="text" name="quick_delivery_time_name" id="quick_delivery_time_name"
                        placeholder="Nuevo tiempo de entrega" class="w-full" />
                    <x-button type="button" id="btn-quick-delivery-time" text="Agregar rapido" icon="plus"
                        typeButton="secondary" class="w-full sm:w-auto" />
                </div>
                <div class="flex items-center justify-between">
                    <span id="quick-delivery-time-feedback" class="text-xs text-gray-500 dark:text-gray-400"></span>
                    <a href="{{ Route('business.quotation-delivery-times.index') }}" target="_blank"
                        class="text-xs text-primary-500 hover:underline dark:text-primary-300">Administrar tiempos de
                        entrega</a>
                </div>
            </div>

            <div class="space-y-2">
                <x-select label="Forma de pago" name="payment_method_id" id="quotation_payment_method_id"
                    :options="$paymentMethodOptions"
                    selected="{{ old('payment_method_id', $selectedPaymentMethodId) && isset($paymentMethodOptions[old('payment_method_id', $selectedPaymentMethodId)]) ? $paymentMethodOptions[old('payment_method_id', $selectedPaymentMethodId)] : 'Seleccionar' }}"
                    value="{{ old('payment_method_id', $selectedPaymentMethodId) }}" :search="true"
                    :required="$hasPaymentMethods" />
                <div class="flex flex-col gap-2 sm:flex-row">
                    <x-input type="text" name="quick_payment_method_name" id="quick_payment_method_name"
                        placeholder="Nueva forma de pago" class="w-full" />
                    <x-button type="button" id="btn-quick-payment-method" text="Agregar rapido" icon="plus"
                        typeButton="secondary" class="w-full sm:w-auto" />
                </div>
                <div class="flex items-center justify-between">
                    <span id="quick-payment-method-feedback" class="text-xs text-gray-500 dark:text-gray-400"></span>
                    <a href="{{ Route('business.quotation-payment-methods.index') }}" target="_blank"
                        class="text-xs text-primary-500 hover:underline dark:text-primary-300">Administrar formas de
                        pago</a>
                </div>
            </div>

            <x-input type="textarea" name="thank_you_message" label="Mensaje de agradecimiento"
                value="{{ old('thank_you_message', $meta['thank_you_message'] ?? 'Gracias por su preferencia.') }}"
                class="md:col-span-1" />
            <x-input type="textarea" name="terms_conditions" label="Terminos y condiciones"
                value="{{ old('terms_conditions', $meta['terms_conditions'] ?? '') }}" class="md:col-span-1" />
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
        <x-button type="a" href="{{ Route('business.quotations.index') }}" text="Cancelar" icon="x"
            typeButton="secondary" class="w-full sm:w-auto" />
        <x-button type="submit" text="Guardar cotizacion" icon="save" typeButton="primary"
            class="w-full sm:w-auto" />
    </div>
</form>
