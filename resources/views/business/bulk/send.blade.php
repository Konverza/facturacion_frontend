@extends('layouts.auth-template')
@section('title', 'Envío Masivo')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Envío Masivo
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="mt-4 border-t border-gray-300 px-4 pt-4 dark:border-gray-800">
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
    </section>
    @push('scripts')
        <script>
            (function () {
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
                    post: (url, data, cfg={}) => $.ajax({ url, method: 'POST', data, processData: false, contentType: false, headers: cfg.headers }),
                    get: (url, cfg={}) => $.ajax({ url, method: 'GET', headers: cfg.headers })
                };

                let customers = [];
                let templateDte = null;
                let templateName = '';

                function badge(status, extra) {
                    const map = {
                        PROCESADO: { c: 'text-green-500', i: 'check', t: 'Procesado' },
                        CONTINGENCIA: { c: 'text-yellow-600', i: 'alert-triangle', t: 'Contingencia' },
                        RECHAZADO: { c: 'text-red-600', i: 'x', t: 'Rechazado' },
                        PENDIENTE: { c: 'text-gray-500', i: 'loader', t: extra || 'Pendiente' }
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
                    const res = await http.post('/business/dte/import-customers-excel', fd, { headers: { 'X-CSRF-TOKEN': CSRF } });
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

                function deepClone(obj) { return JSON.parse(JSON.stringify(obj)); }

                function mergeDte(base, customer) {
                    const dte = deepClone(base);
                    dte.customer = { ...dte.customer, ...customer };
                    // Garantizar type presente
                    dte.type = dte.type || dte.tipo || '01';
                    return dte;
                }

                async function sendOne(idx, customer) {
                    const payload = { dte: mergeDte(templateDte, customer) };
                    try {
                        const res = await (window.axios ? window.axios.post('/business/dte/submit-from-json', payload, { headers: { 'X-CSRF-TOKEN': CSRF } })
                            : $.ajax({ url: '/business/dte/submit-from-json', method: 'POST', data: JSON.stringify(payload), contentType: 'application/json', headers: { 'X-CSRF-TOKEN': CSRF } }));
                        const data = res.data || res;
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
                    let ok = 0, fail = 0, done = 0;
                    $countTotal.text(items.length);
                    const pool = new Array(Math.min(concurrency, items.length)).fill(null);
                    async function next(i) {
                        if (i >= items.length) return;
                        const success = await sendOne(i, items[i]);
                        ok += success ? 1 : 0; fail += success ? 0 : 1; done += 1;
                        $countOk.text(ok); $countFail.text(fail);
                        $progress.css('width', `${Math.round((done / items.length) * 100)}%`);
                        return next(i + pool.length);
                    }
                    await Promise.all(pool.map((_, k) => next(k)));
                }

                // Eventos
                $file.on('change', async function () {
                    const file = this.files?.[0];
                    if (!file) return;
                    $("#loader").removeClass('hidden');
                    try {
                        customers = await uploadCustomers(file);
                        // Pre-render tabla
                        $tbody.empty();
                        customers.forEach((c, idx) => { $tbody.append(renderRow(idx, c)); });
                        if (customers.length > 0) { $results.removeClass('hidden'); }
                    } catch (e) {
                        window.alert(e.message || 'Error al importar clientes');
                    } finally {
                        $("#loader").addClass('hidden');
                    }
                });

                $template.on('Changed', async function () {
                    const id = $(this).val();
                    if (!id) { templateDte = null; templateName = ''; return; }
                    $("#loader").removeClass('hidden');
                    try {
                        const data = await fetchTemplate(id);
                        templateDte = data.dte; templateName = data.name || '';
                    } catch (e) {
                        window.alert(e.message || 'Error al obtener la plantilla');
                    } finally {
                        $("#loader").addClass('hidden');
                    }
                });

                $sendBtn.on('click', async function () {
                    if (!customers.length) { return window.alert('Primero importe el archivo de clientes'); }
                    if (!templateDte) { return window.alert('Seleccione una plantilla de DTE'); }
                    $("#loader").removeClass('hidden');
                    try {
                        // Reset contadores y estados
                        $countOk.text('0'); $countFail.text('0'); $countTotal.text(customers.length);
                        $progress.css('width', '0%');
                        // Enviar en cola
                        await runQueue(customers, 3);
                    } finally {
                        $("#loader").addClass('hidden');
                        $results.removeClass('hidden');
                    }
                });
            })();
        </script>
    @endpush
@endsection
