@extends('layouts.auth-template')
@section('title', 'Envío Masivo')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Envío Masivo
            </h1>
        </div>


        <div class="my-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
                data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="plantilla-tab"
                        data-tabs-target="#plantilla" type="button" role="tab" aria-controls="plantilla"
                        aria-selected="false">Envío de Plantilla de DTE</button>
                </li>
                <li class="me-2" role="presentation">
                    <button
                        class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                        id="excel-tab" data-tabs-target="#excel" type="button" role="tab" aria-controls="excel"
                        aria-selected="false">Envío de DTEs desde Excel</button>
                </li>
            </ul>
        </div>
        <div id="default-tab-content">
            <div class="hidden pb-4 rounded-lg" id="plantilla" role="tabpanel" aria-labelledby="plantilla-tab">
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <p class="text-md text-gray-500 dark:text-gray-400">Esta opción permite enviar <strong
                            class="font-medium text-gray-800 dark:text-white">la misma plantilla de DTE</strong>
                        a distintos clientes, utilizando una plantilla creada previamente y la lista de clientes que adjunte
                        en el Paso 1. <br>Si desea enviar distintos DTEs a distintos clientes, utilice la opción
                        <strong class="font-medium text-gray-800 dark:text-white">Envío de DTEs desde Excel</strong>.
                    </p>
                </div>
                <div class="mt-4 border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300">
                        <x-icon icon="users" class="size-5" />
                        Paso 1. Seleccione los clientes a los que enviará el DTE
                    </h2>
                    <div
                        class="my-2 rounded-lg border border-dashed border-blue-500 bg-blue-100 p-4 text-blue-500 dark:bg-blue-950/30">
                        <b>Nota: </b> Debe utilizar un archivo en formato <b>.xlsx</b> disponible en
                        <a href="{{ url('templates/importacion_clientes.xlsx') }}" target="_blank"
                            class="text-blue-600 underline dark:text-blue-400">este enlace</a> para
                        seleccionar clientes.
                    </div>
                    <x-input type="file" label="Archivo de Clientes" name="file" id="file" accept=".xlsx"
                        maxSize="3072" />
                </div>
                <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
                    <h2 class="flex items-center gap-1 text-xl font-semibold text-primary-500 dark:text-primary-300 mb-2">
                        <x-icon icon="files" class="size-5" />
                        Paso 2. Seleccione la plantilla de DTE a enviar
                    </h2>
                    <x-select name="template_id" label="Plantilla de DTE" id="template_id" :options="$templates" />
                    <div class="mt-4 flex flex-col items-center justify-center gap-4 px-4 sm:flex-row">
                        <x-button type="submit" typeButton="primary" icon="file-symlink" text="Enviar DTEs"
                            class="w-full sm:w-auto" name="action" value="generate" id="masivos-dte" />
                    </div>
                </div>

                <div id="resultados" class="my-5 hidden">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <span id="bulk-count-ok" class="text-green-600 dark:text-green-400">0</span> procesados •
                            <span id="bulk-count-fail" class="text-red-600 dark:text-red-400">0</span> con error •
                            <span id="bulk-count-total">0</span> total
                        </div>
                        <div class="w-1/2 h-2 rounded bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <div id="bulk-progress" class="h-2 bg-primary-500" style="width:0%"></div>
                        </div>
                    </div>
                    <x-table>
                        <x-slot name="thead">
                            <x-tr>
                                <x-th>Cliente</x-th>
                                <x-th>Documento</x-th>
                                <x-th>Plantilla</x-th>
                                <x-th :last="true">Estado</x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            <tbody id="bulk-results-body"></tbody>
                        </x-slot>
                    </x-table>
                </div>
            </div>
            <div class="hidden rounded-lg" id="excel" role="tabpanel" aria-labelledby="excel-tab">
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <p class="text-md text-gray-500 dark:text-gray-400 mb-4">Suba un Excel donde <strong class="font-medium text-gray-800 dark:text-white">cada fila contenga los datos del cliente y de un producto</strong>. Se agruparán automáticamente los productos por cliente para generar múltiples DTEs. Sólo se admiten tipos 01 (Consumidor Final) y 03 (Crédito Fiscal).</p>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <x-select label="Tipo de DTE" id="bulk_dte_type" name="bulk_dte_type" :options="['01'=>'Consumidor Final','03'=>'Crédito Fiscal']" />
                        </div>
                        <div class="sm:col-span-2 flex flex-col sm:flex-row sm:items-end gap-4">
                            <x-input type="file" label="Excel Clientes + Productos" id="file_customers_products" name="file_customers_products" accept=".xlsx,.xls,.csv" />
                            <button id="parse-customers-products" type="button" class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-400 disabled:opacity-50 mt-2">Procesar</button>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Columnas adicionales esperadas: tipo de item, unidad de medida, cantidad, precio unitario (sin IVA), descripcion, tipo de venta (gravada|exenta|no sujeta).
                    </div>
                    <div id="bulk-dte-preview" class="mt-6 hidden">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-primary-600 dark:text-primary-300">DTEs generados <span id="bulk-dte-count" class="text-sm font-normal text-gray-500"></span></h3>
                            <button id="send-bulk-generated" type="button" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 disabled:opacity-50">Enviar DTEs</button>
                        </div>
                        <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left">#</th>
                                        <th class="px-3 py-2 text-left">Cliente</th>
                                        <th class="px-3 py-2 text-left">Documento</th>
                                        <th class="px-3 py-2 text-left">Items</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                        <th class="px-3 py-2 text-left">Estado</th>
                                        <th class="px-3 py-2 text-left">Ver</th>
                                    </tr>
                                </thead>
                                <tbody id="bulk-dte-body"></tbody>
                            </table>
                        </div>
                        <div id="bulk-discarded" class="mt-6 hidden">
                            <h4 class="text-md font-semibold text-red-600 dark:text-red-400 mb-2">Filas descartadas</h4>
                            <div class="overflow-x-auto rounded border border-red-200 dark:border-red-700 max-h-60">
                                <table class="w-full text-xs">
                                    <thead class="bg-red-100 dark:bg-red-900/40">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Fila</th>
                                            <th class="px-2 py-1 text-left">Motivos</th>
                                            <th class="px-2 py-1 text-left">Descripción Producto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulk-discarded-body"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400" id="bulk-dte-progress"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    @push('scripts')
        <script>
            (function() {
                const $file = $("#file");
                const $template = $("#template_id");
                const $sendBtn = $("#masivos-dte");
                const $results = $("#resultados");
                const $tbody = $("#bulk-results-body");
                const $countOk = $("#bulk-count-ok");
                const $countFail = $("#bulk-count-fail");
                const $countTotal = $("#bulk-count-total");
                const $progress = $("#bulk-progress");

                const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const http = window.axios || {
                    post: (url, data, cfg = {}) => $.ajax({
                        url,
                        method: 'POST',
                        data,
                        processData: false,
                        contentType: false,
                        headers: cfg.headers
                    }),
                    get: (url, cfg = {}) => $.ajax({
                        url,
                        method: 'GET',
                        headers: cfg.headers
                    })
                };

                let customers = [];
                let templateDte = null;
                let templateName = '';

                function badge(status, extra) {
                    const map = {
                        PROCESADO: {
                            c: 'text-green-500',
                            i: 'check',
                            t: 'Procesado'
                        },
                        CONTINGENCIA: {
                            c: 'text-yellow-600',
                            i: 'alert-triangle',
                            t: 'Contingencia'
                        },
                        RECHAZADO: {
                            c: 'text-red-600',
                            i: 'x',
                            t: 'Rechazado'
                        },
                        PENDIENTE: {
                            c: 'text-gray-500',
                            i: 'loader',
                            t: extra || 'Pendiente'
                        }
                    };
                    const b = map[status] || map.PENDIENTE;
                    return `<span class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold ${b.c}">
                        <x-icon icon="${b.i}" class="size-5 ${b.c}" />${extra ? (b.t+': '+extra) : b.t}
                    </span>`;
                }

                function renderRow(idx, c) {
                    const name = c.nombre || c.nombreComercial || 'Cliente';
                    const doc = `${c.tipoDocumento || ''} ${c.numDocumento || ''}`.trim();
                    return `<tr id="row-${idx}">
                        <td class="px-4 py-2">${name}</td>
                        <td class="px-4 py-2">${doc}</td>
                        <td class="px-4 py-2">${templateName || '-'}</td>
                        <td class="px-4 py-2" id="state-${idx}">${badge('PENDIENTE')}</td>
                    </tr>`;
                }

                async function uploadCustomers(file) {
                    const fd = new FormData();
                    fd.append('file', file);
                    if (CSRF) fd.append('_token', CSRF);
                    const res = await http.post('/business/dte/import-customers-excel', fd, {
                        headers: {
                            'X-CSRF-TOKEN': CSRF
                        }
                    });
                    const data = res.data || res; // $.ajax returns JSON already parsed
                    if (!data.success) throw new Error(data.message || 'Error importando clientes');
                    return data.items || [];
                }

                async function fetchTemplate(id) {
                    const res = await http.get(`/business/bulk/template/${id}`);
                    const data = res.data || res;
                    if (!data.success) throw new Error(data.message || 'Error obteniendo plantilla');
                    return data;
                }

                function deepClone(obj) {
                    return JSON.parse(JSON.stringify(obj));
                }

                function mergeDte(base, customer) {
                    const dte = deepClone(base);
                    dte.customer = {
                        ...dte.customer,
                        ...customer
                    };
                    // Garantizar type presente
                    dte.type = dte.type || dte.tipo || '01';
                    return dte;
                }

                async function sendOne(idx, customer) {
                    const payload = {
                        dte: mergeDte(templateDte, customer)
                    };
                    try {
                        let data;
                        if (window.axios) {
                            const res = await window.axios.post('/business/dte/submit-from-json', payload, {
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                            });
                            data = res.data;
                        } else {
                            data = await $.ajax({
                                url: '/business/dte/submit-from-json',
                                method: 'POST',
                                data: JSON.stringify(payload),
                                contentType: 'application/json',
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                            });
                        }
                        if (!data || typeof data !== 'object' || (!data.estado && data.success === undefined)) {
                            throw new Error('Respuesta inesperada (posible redirección)');
                        }
                        
                        const estado = data.estado || (data.success === false ? 'RECHAZADO' : 'PROCESADO');
                        const mensaje = data.observaciones || data.message || '';
                        $(`#state-${idx}`).html(badge(estado, mensaje));
                        return estado === 'PROCESADO' || estado === 'CONTINGENCIA';
                    } catch (e) {
                        const msg = e.response?.data?.message || e.message || 'Error';
                        $(`#state-${idx}`).html(badge('RECHAZADO', msg));
                        return false;
                    }
                }

                async function runQueue(items, concurrency = 3) {
                    let ok = 0,
                        fail = 0,
                        done = 0;
                    $countTotal.text(items.length);
                    const pool = new Array(Math.min(concurrency, items.length)).fill(null);
                    async function next(i) {
                        if (i >= items.length) return;
                        const success = await sendOne(i, items[i]);
                        ok += success ? 1 : 0;
                        fail += success ? 0 : 1;
                        done += 1;
                        $countOk.text(ok);
                        $countFail.text(fail);
                        $progress.css('width', `${Math.round((done / items.length) * 100)}%`);
                        return next(i + pool.length);
                    }
                    await Promise.all(pool.map((_, k) => next(k)));
                }

                // Eventos
                $file.on('change', async function() {
                    const file = this.files?.[0];
                    if (!file) return;
                    $("#loader").removeClass('hidden');
                    try {
                        customers = await uploadCustomers(file);
                        // Pre-render tabla
                        $tbody.empty();
                        customers.forEach((c, idx) => {
                            $tbody.append(renderRow(idx, c));
                        });
                        if (customers.length > 0) {
                            $results.removeClass('hidden');
                        }
                    } catch (e) {
                        window.alert(e.message || 'Error al importar clientes');
                    } finally {
                        $("#loader").addClass('hidden');
                    }
                });

                $template.on('Changed', async function() {
                    const id = $(this).val();
                    if (!id) {
                        templateDte = null;
                        templateName = '';
                        return;
                    }
                    $("#loader").removeClass('hidden');
                    try {
                        const data = await fetchTemplate(id);
                        templateDte = data.dte;
                        templateName = data.name || '';
                    } catch (e) {
                        window.alert(e.message || 'Error al obtener la plantilla');
                    } finally {
                        $("#loader").addClass('hidden');
                    }
                });

                $sendBtn.on('click', async function() {
                    if (!customers.length) {
                        return window.alert('Primero importe el archivo de clientes');
                    }
                    if (!templateDte) {
                        return window.alert('Seleccione una plantilla de DTE');
                    }
                    $("#loader").removeClass('hidden');
                    try {
                        // Reset contadores y estados
                        $countOk.text('0');
                        $countFail.text('0');
                        $countTotal.text(customers.length);
                        $progress.css('width', '0%');
                        // Enviar en cola
                        await runQueue(customers, 1);
                    } finally {
                        $("#loader").addClass('hidden');
                        $results.removeClass('hidden');
                    }
                });

                /* ================== NUEVO FLUJO: Excel Clientes + Productos ================== */
                const $fileCP = $('#file_customers_products');
                const $btnParseCP = $('#parse-customers-products');
                const $typeCP = $('#bulk_dte_type');
                const $previewCP = $('#bulk-dte-preview');
                const $bodyCP = $('#bulk-dte-body');
                const $countCP = $('#bulk-dte-count');
                const $progressCP = $('#bulk-dte-progress');
                const $sendCP = $('#send-bulk-generated');
                let generatedDtes = [];

                function currency(v){
                    return '$' + Number(v || 0).toLocaleString('en-US', { minimumFractionDigits:2, maximumFractionDigits:2 });
                }
                function renderCPRow(idx, item) {
                    const c = item.customer || {}; const name = c.nombre || c.nombreComercial || 'Cliente';
                    const docLabel = c.tipoDocumentoLabel || c.tipoDocumento || '';
                    const docNumber = c.numDocumento || '';
                    let totalCell;
                    if (item.type === '03' && item.preview_totals) {
                        totalCell = `<div class="flex flex-col text-right leading-tight">
                            <span class="font-semibold">Base: ${currency(item.preview_totals.base)}</span>
                            <span>IVA: ${currency(item.preview_totals.iva)}</span>
                            <span class="border-t border-dashed mt-0.5 pt-0.5">Total: <strong>${currency(item.preview_totals.total)}</strong></span>
                        </div>`;
                    } else {
                        totalCell = `<span class="font-semibold">${currency(item.total_pagar ?? item.total ?? 0)}</span>`;
                    }
                    // Botón para modal/desplegable
                    const modalId = `items-modal-${idx}`;
                    // Helper para mostrar montos con placeholder
                    const fmt = (v, opts={placeholder:true}) => {
                        const num = Number(v||0);
                        if(num > 0) return currency(num);
                        return opts.placeholder ? '$ -' : currency(0);
                    };
                    let ivaTotal = 0, gravadaTotal = 0, exentaTotal = 0, noSujTotal = 0;
                    const listItems = (item.items_preview||[]).map(p=>{
                        const gravadaConIva = (Number(p.gravada||0) + Number(p.iva||0));
                        ivaTotal += Number(p.iva||0);
                        gravadaTotal += gravadaConIva;
                        exentaTotal += Number(p.exenta||0);
                        noSujTotal += Number(p.no_suj||0);
                        return `<tr>
                            <td class='px-2 py-1'>${p.descripcion}</td>
                            <td class='px-2 py-1 text-right'>${currency(p.precio)}</td>
                            <td class='px-2 py-1 text-center'>${p.cantidad}</td>
                            <td class='px-2 py-1 text-right'>${fmt(p.iva)}</td>
                            <td class='px-2 py-1 text-right'>${fmt(gravadaConIva)}</td>
                            <td class='px-2 py-1 text-right'>${fmt(p.exenta)}</td>
                            <td class='px-2 py-1 text-right'>${fmt(p.no_suj)}</td>
                        </tr>`;}).join('');
                    const totalsRow = `<tr class='font-semibold bg-gray-50 dark:bg-gray-700/50'>
                        <td class='px-2 py-1 text-right' colspan='3'>Totales</td>
                        <td class='px-2 py-1 text-right'>${fmt(ivaTotal,{placeholder:false})}</td>
                        <td class='px-2 py-1 text-right'>${fmt(gravadaTotal,{placeholder:false})}</td>
                        <td class='px-2 py-1 text-right'>${fmt(exentaTotal,{placeholder:false})}</td>
                        <td class='px-2 py-1 text-right'>${fmt(noSujTotal,{placeholder:false})}</td>
                    </tr>`;
                    const grandTotal = gravadaTotal + exentaTotal + noSujTotal; // gravada ya incluye IVA
                    const modalHtml = `<div id="${modalId}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                        <div class="w-full max-w-2xl rounded-lg bg-white p-4 dark:bg-gray-800 shadow">
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Items DTE #${idx+1}</h5>
                                <button type="button" data-modal-hide="${modalId}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <span class="sr-only">Cerrar</span>
                                    &times;
                                </button>
                            </div>
                            <div class="overflow-x-auto border rounded border-gray-200 dark:border-gray-700 max-h-80">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Descripción</th>
                                            <th class="px-2 py-1 text-right">Precio Unitario</th>
                                            <th class="px-2 py-1 text-center">Cant</th>
                                            <th class="px-2 py-1 text-right">IVA</th>
                                            <th class="px-2 py-1 text-right">Gravada (c/ IVA)</th>
                                            <th class="px-2 py-1 text-right">Exenta</th>
                                            <th class="px-2 py-1 text-right">No Suj.</th>
                                        </tr>
                                    </thead>
                                    <tbody>${listItems}</tbody>
                                    <tfoot>${totalsRow}</tfoot>
                                </table>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Total General: <span class="text-primary-600 dark:text-primary-300">${currency(grandTotal)}</span></div>
                            </div>
                            <div class="mt-4 text-right">
                                <button data-modal-hide="${modalId}" class="inline-flex items-center rounded bg-primary-600 px-3 py-1.5 text-white text-sm hover:bg-primary-700">Cerrar</button>
                            </div>
                        </div>
                    </div>`;
                    // Insert modal into DOM (lazy container)
                    setTimeout(()=>{ if(!document.getElementById(modalId)){ document.body.insertAdjacentHTML('beforeend', modalHtml); } },0);
                    return `<tr id="cp-row-${idx}" class="border-b border-gray-100 dark:border-gray-700">
                        <td class="px-3 py-1">${idx + 1}</td>
                        <td class="px-3 py-1">${name}</td>
                        <td class="px-3 py-1">${docLabel} ${docNumber}</td>
                        <td class="px-3 py-1">${item.products.length}</td>
                        <td class="px-3 py-1">${totalCell}</td>
                        <td class="px-3 py-1"><span class="inline-flex rounded bg-gray-300 dark:bg-gray-600 px-2 py-0.5 text-xs font-semibold text-gray-800 dark:text-gray-100" data-status>LISTO</span></td>
                        <td class="px-3 py-1 text-center"><button data-modal-target="${modalId}" data-modal-toggle="${modalId}" class="text-primary-600 hover:underline text-xs">Ver</button></td>
                    </tr>`;
                }

                async function importCustomersProductsExcel(file, dteType) {
                    const fd = new FormData(); fd.append('file', file); fd.append('dte_type', dteType); if (CSRF) fd.append('_token', CSRF);
                    const res = await http.post('/business/dte/import-customers-products-excel', fd, {
                        headers: { 'X-CSRF-TOKEN': CSRF }
                    });
                    return res.data || res;
                }

                async function sendOneCP(idx, dte) {
                    const $row = $(`#cp-row-${idx}`); const $st = $row.find('[data-status]');
                    $st.text('ENVIANDO').removeClass().addClass('inline-flex rounded bg-blue-500 px-2 py-0.5 text-xs font-semibold text-white');
                    try {
                        let data;
                        if (window.axios) {
                            const res = await window.axios.post('/business/dte/submit-from-json', { dte }, {
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                            });
                            data = res.data;
                        } else {
                            const res = await $.ajax({
                                url: '/business/dte/submit-from-json',
                                method: 'POST',
                                data: JSON.stringify({ dte }),
                                contentType: 'application/json',
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                            });
                            data = res;
                        }
                        if (!data || typeof data !== 'object' || (!data.estado && data.success === undefined)) {
                            throw new Error('Respuesta inesperada (posible redirección)');
                        }
                        const estado = data.estado || (data.success === false ? 'RECHAZADO' : 'PROCESADO');
                        if (estado === 'PROCESADO' || estado === 'CONTINGENCIA') {
                            $st.text('PROCESADO').removeClass().addClass('inline-flex rounded bg-green-600 px-2 py-0.5 text-xs font-semibold text-white');
                            return true;
                        }
                        $st.text(estado).removeClass().addClass('inline-flex rounded bg-red-600 px-2 py-0.5 text-xs font-semibold text-white');
                        return false;
                    } catch (e) {
                        $st.text('ERROR').removeClass().addClass('inline-flex rounded bg-red-600 px-2 py-0.5 text-xs font-semibold text-white');
                        return false;
                    }
                }

                async function runCPQueue(concurrency = 2) {
                    let done = 0, ok = 0; const total = generatedDtes.length; $progressCP.text(`0/${total}`);
                    const pool = new Array(Math.min(concurrency, total)).fill(null);
                    async function next() {
                        const idx = done; if (idx >= total) return; done++;
                        const success = await sendOneCP(idx, generatedDtes[idx]); if (success) ok++;
                        $progressCP.text(`${done}/${total} procesados (Exitosos: ${ok})`);
                        await next();
                    }
                    await Promise.all(pool.map(() => next()));
                }

                $btnParseCP.on('click', async function() {
                    const file = $fileCP[0].files?.[0]; const dteType = $typeCP.val();
                    if (!file) return alert('Seleccione un archivo'); if (!dteType) return alert('Seleccione el tipo de DTE');
                    $('#loader').removeClass('hidden');
                    try {
                        const data = await importCustomersProductsExcel(file, dteType);
                        if (!data.success) throw new Error(data.message || 'Error');
                        generatedDtes = data.items || [];
                        $bodyCP.empty(); generatedDtes.forEach((d,i)=> $bodyCP.append(renderCPRow(i,d)));
                        // Descartados
                        const discarded = data.discarded || [];
                        const $discardBody = $('#bulk-discarded-body');
                        $discardBody.empty();
                        if (discarded.length){
                            discarded.forEach(r=>{
                                $discardBody.append(`<tr><td class='px-2 py-1'>${r.row}</td><td class='px-2 py-1'>${r.reasons.join('; ')}</td><td class='px-2 py-1'>${r.data?.descripcion || ''}</td></tr>`);
                            });
                            $('#bulk-discarded').removeClass('hidden');
                        } else {
                            $('#bulk-discarded').addClass('hidden');
                        }
                        $countCP.text(`(${generatedDtes.length})`); $previewCP.removeClass('hidden');
                    } catch (e) { alert(e.message || 'Error al procesar el Excel'); } finally { $('#loader').addClass('hidden'); }
                });

                $sendCP.on('click', async function() {
                    if (!generatedDtes.length) return alert('No hay DTEs generados');
                    if (!confirm('¿Enviar todos los DTEs generados?')) return;
                    $('#loader').removeClass('hidden'); $sendCP.prop('disabled', true);
                    try { await runCPQueue(2); } finally { $('#loader').addClass('hidden'); $sendCP.prop('disabled', false); }
                    // Limpiar sesión DTE para nuevo inicio
                    try { await http.post('/business/dte/clear-bulk-session', { _token: CSRF }, { headers: { 'X-CSRF-TOKEN': CSRF }}); } catch(e) {}
                });

                /* ================== MANEJO DE MODALES DINÁMICOS (Items DTE) ================== */
                // Delegación para abrir
                document.addEventListener('click', function(e){
                    const toggleBtn = e.target.closest('[data-modal-toggle]');
                    if(toggleBtn){
                        const id = toggleBtn.getAttribute('data-modal-target') || toggleBtn.getAttribute('data-modal-toggle');
                        const modal = document.getElementById(id);
                        if(modal){
                            modal.classList.remove('hidden');
                            // Evitar scroll de fondo opcional
                            document.documentElement.classList.add('overflow-y-hidden');
                        }
                    }
                    const hideBtn = e.target.closest('[data-modal-hide]');
                    if(hideBtn){
                        const id = hideBtn.getAttribute('data-modal-hide');
                        const modal = document.getElementById(id);
                        if(modal){
                            modal.classList.add('hidden');
                            document.documentElement.classList.remove('overflow-y-hidden');
                        }
                    }
                });
                // Cerrar al hacer click en el overlay (zona oscura fuera del contenido)
                document.addEventListener('click', function(e){
                    const modalEl = e.target;
                    if(modalEl && modalEl.classList && modalEl.classList.contains('fixed') && modalEl.getAttribute('id')?.startsWith('items-modal-')){
                        // click directo al overlay
                        modalEl.classList.add('hidden');
                        document.documentElement.classList.remove('overflow-y-hidden');
                    }
                });
            })();
        </script>
    @endpush
@endsection
