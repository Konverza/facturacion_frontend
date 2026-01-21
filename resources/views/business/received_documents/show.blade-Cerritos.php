@extends('layouts.auth-template')
@section('title', 'Detalle DTE Recibido')
@section('content')
    @php
        $documento = json_decode($dte['documento']);
        $types = [
            '01' => 'Factura Consumidor Final',
            '03' => 'Comprobante de crédito fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de crédito',
            '06' => 'Nota de débito',
            '07' => 'Comprobante de retención',
            '11' => 'Factura de exportación',
            '14' => 'Factura de sujeto excluido',
            '15' => 'Comprobante de Donación',
        ];

        // Determinar si usar emisor o donante según el tipo de DTE
        $emisor = isset($documento->emisor)
            ? $documento->emisor
            : (isset($documento->donante)
                ? $documento->donante
                : null);
        $tipoEmisor = isset($documento->donante) ? 'Donante' : 'Emisor';

        // Determinar el tipo de receptor según el tipo de DTE
        $receptor = isset($documento->receptor)
            ? $documento->receptor
            : (isset($documento->sujetoExcluido)
                ? $documento->sujetoExcluido
                : (isset($documento->donatario)
                    ? $documento->donatario
                    : null));
        $tipoReceptor = isset($documento->sujetoExcluido)
            ? 'Sujeto Excluido'
            : (isset($documento->donatario)
                ? 'Donatario'
                : 'Receptor');
    @endphp
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Detalle del DTE Recibido
            </h1>
            <div>
                <x-button type="button" typeButton="primary" icon="file-code" id="descargarJsonButton" text="Descargar JSON" />
            </div>
        </div>

        <x-card title="Identificación" icon="user-square" :collapsible="false" :collapsed="false" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Código de generación
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        {{ $codGeneracion }}
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Número de Control
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        {{ $documento->identificacion->numeroControl }}
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Sello de Recepción
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        {{ $dte['selloRecibido'] }}
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Fecha de Emisión
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($documento->identificacion->fecEmi)->format('d/m/Y') }}
                        {{ $documento->identificacion->horEmi }}
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Estado
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        @php
                            $status = $dte['estado'];
                            $style = '';
                            if ($status == 'RECHAZADO') {
                                $style = 'bg-red-100 dark:bg-red-950/30';
                            }

                            if ($status == 'CONTINGENCIA' || $status == 'ANULADO') {
                                $style = 'bg-yellow-100 dark:bg-yellow-950/30';
                            }
                        @endphp
                        @if ($dte['estado'] === 'PROCESADO' || $dte['estado'] === 'VALIDADO' || $dte['estado'] === 'OBSERVADO')
                            <span
                                class="flex items-center gap-1 bg-green-200 dark:bg-green-900/50 px-2 py-1 rounded-lg w-max font-bold text-green-800 dark:text-green-300 text-xs uppercase text-nowrap">
                                <x-icon icon="circle-check" class="size-4" />
                                Procesado
                            </span>
                        @elseif($dte['estado'] === 'RECHAZADO')
                            <span
                                class="flex items-center gap-1 bg-red-200 dark:bg-red-900/50 px-2 py-1 rounded-lg w-max font-bold text-red-800 dark:text-red-300 text-xs uppercase text-nowrap">
                                <x-icon icon="circle-x" class="size-4" />
                                Rechazado
                            </span>
                        @elseif($dte['estado'] === 'CONTINGENCIA')
                            <span
                                class="flex items-center gap-1 bg-yellow-200 dark:bg-yellow-900/50 px-2 py-1 rounded-lg w-max font-bold text-yellow-800 dark:text-yellow-300 text-xs uppercase text-nowrap">
                                <x-icon icon="warning" class="size-4" />
                                Contingencia
                            </span>
                        @elseif($dte['estado'] === 'ANULADO')
                            <span
                                class="flex items-center gap-1 bg-yellow-200 dark:bg-yellow-900/50 px-2 py-1 rounded-lg w-max font-bold text-yellow-800 dark:text-yellow-300 text-xs uppercase text-nowrap">
                                <x-icon icon="circle-minus" class="size-4" />
                                Anulado
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                        Tipo de DTE
                    </div>
                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                        {{ $types[$dte['tipo_dte']] }}
                    </div>
                </div>
            </div>
        </x-card>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Card Emisor/Donante -->
            <x-card :title="$tipoEmisor" icon="building" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                            Nombre, Razón Social o Denominación
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            {{ $emisor->nombre }}
                        </div>
                    </div>
                    @if ($emisor->nombreComercial)
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Nombre Comercial
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $emisor->nombreComercial }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                            Identificación
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            @if (isset($emisor->numDocumento) && $emisor->numDocumento)
                                {{ $catalogos['tipos_documentos'][$emisor->tipoDocumento] ?? 'N/A' }} -
                                {{ $emisor->numDocumento }}
                            @elseif(isset($emisor->nit))
                                NIT: {{ $emisor->nit }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    @if (isset($emisor->nrc))
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                NRC
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $emisor->nrc }}
                            </div>
                        </div>
                    @endif
                    @if (isset($emisor->telefono))
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Teléfono
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $emisor->telefono }}
                            </div>
                        </div>
                    @endif
                    @if (isset($emisor->correo))
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Correo Electrónico
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $emisor->correo }}
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Card Receptor/Sujeto Excluido/Donatario -->
            @if ($receptor)
                <x-card :title="$tipoReceptor" icon="user" :collapsible="false" :collapsed="false" class="mt-6">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Nombre, Razón Social o Denominación
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $receptor->nombre }}
                            </div>
                        </div>
                        @if (isset($receptor->nombreComercial) && $receptor->nombreComercial)
                            <div>
                                <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                    Nombre Comercial
                                </div>
                                <div class="break-all text-sm text-slate-900 dark:text-white">
                                    {{ $receptor->nombreComercial }}
                                </div>
                            </div>
                        @endif
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Identificación
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                @if (isset($receptor->numDocumento) && $receptor->numDocumento)
                                    {{ $catalogos['tipos_documentos'][$receptor->tipoDocumento] ?? 'N/A' }} -
                                    {{ $receptor->numDocumento }}
                                @elseif(isset($receptor->nit))
                                    NIT: {{ $receptor->nit }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        @if (isset($receptor->nrc) && $receptor->nrc)
                            <div>
                                <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                    NRC
                                </div>
                                <div class="break-all text-sm text-slate-900 dark:text-white">
                                    {{ $receptor->nrc }}
                                </div>
                            </div>
                        @endif
                        @if (isset($receptor->telefono) && $receptor->telefono)
                            <div>
                                <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                    Teléfono
                                </div>
                                <div class="break-all text-sm text-slate-900 dark:text-white">
                                    {{ $receptor->telefono }}
                                </div>
                            </div>
                        @endif
                        @if (isset($receptor->correo) && $receptor->correo)
                            <div>
                                <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                    Correo Electrónico
                                </div>
                                <div class="break-all text-sm text-slate-900 dark:text-white">
                                    {{ $receptor->correo }}
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Card documentos Relacionados -->
        @if($documento->documentoRelacionado)
            <x-card title="Documentos Relacionados" icon="document-multiple" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="grid grid-cols-1 gap-4">
                    <x-table>
                        <x-slot name="thead">
                            <x-tr>
                                <x-th>Tipo de Documento</x-th>
                                <x-th>Fecha de Emisión</x-th>
                                <x-th>Número de Documento/Código Generación</x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @foreach($documento->documentoRelacionado as $docRel)
                                <x-tr>
                                    <x-td>
                                        {{ $types[$docRel->tipoDocumento] ?? 'N/A' }}
                                    </x-td>
                                    <x-td>
                                        {{ \Carbon\Carbon::parse($docRel->fechaEmision)->format('d/m/Y') }}
                                    </x-td>
                                    <x-td class="break-all">
                                        {{ $docRel->numeroDocumento }}
                                    </x-td>
                                </x-tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                </div>
            </x-card>
        @endif
        <!-- Card colapsable para el JSON -->
        <x-card title="Contenido del DTE (JSON)" icon="code" :collapsible="true" :collapsed="true" class="my-6"
            id="json-card">
            <pre id="jsonData" class="hidden">{{ $dte['documento'] }}</pre>
            <div id="jsonViewer" class="overflow-x-auto"></div>
        </x-card>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const jsonData = document.getElementById('jsonData').textContent;
                const jsonViewer = document.getElementById('jsonViewer');
                let isJsonLoaded = false;

                // Escuchar el evento de toggle del card
                document.addEventListener('card-toggled', function(e) {
                    if (e.detail.id === 'json-card' && e.detail.expanded && !isJsonLoaded) {
                        loadJson();
                        isJsonLoaded = true;
                    }
                });

                // Función para formatear JSON con colores
                function formatJson(obj, indent = 0) {
                    const indentStr = '&nbsp;'.repeat(indent * 2);
                    let html = '';

                    if (obj === null) {
                        return `<span class="json-null">null</span>`;
                    }

                    if (typeof obj === 'string') {
                        return `<span class="json-string">"${escapeHtml(obj)}"</span>`;
                    }

                    if (typeof obj === 'number') {
                        return `<span class="json-number">${obj}</span>`;
                    }

                    if (typeof obj === 'boolean') {
                        return `<span class="json-boolean">${obj}</span>`;
                    }

                    if (Array.isArray(obj)) {
                        if (obj.length === 0) {
                            return '<span class="json-bracket">[]</span>';
                        }
                        html += '<span class="json-bracket">[</span>\n';
                        obj.forEach((item, index) => {
                            html +=
                                `<div class="json-line">${'&nbsp;'.repeat((indent + 1) * 2)}${formatJson(item, indent + 1)}`;
                            if (index < obj.length - 1) {
                                html += ',';
                            }
                            html += '</div>';
                        });
                        html += `\n<div class="json-line">${indentStr}<span class="json-bracket">]</span></div>`;
                        return html;
                    }

                    if (typeof obj === 'object') {
                        const keys = Object.keys(obj);
                        if (keys.length === 0) {
                            return '<span class="json-bracket">{}</span>';
                        }
                        html += '<span class="json-bracket">{</span>\n';
                        keys.forEach((key, index) => {
                            html +=
                                `<div class="json-line">${'&nbsp;'.repeat((indent + 1) * 2)}<span class="json-key">"${escapeHtml(key)}"</span>: ${formatJson(obj[key], indent + 1)}`;
                            if (index < keys.length - 1) {
                                html += ',';
                            }
                            html += '</div>';
                        });
                        html += `\n<div class="json-line">${indentStr}<span class="json-bracket">}</span></div>`;
                        return html;
                    }

                    return String(obj);
                }

                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return text.replace(/[&<>"']/g, m => map[m]);
                }

                // Función para cargar y mostrar el JSON
                function loadJson() {
                    try {
                        const parsedJson = JSON.parse(jsonData);
                        jsonViewer.innerHTML = `<div class="json-viewer">${formatJson(parsedJson)}</div>`;
                    } catch (error) {
                        jsonViewer.innerHTML =
                            `<div class="text-red-500">Error al parsear JSON: ${error.message}</div>`;
                    }
                }

                // Funcionalidad del botón descargar
                const descargarBtn = document.getElementById('descargarJsonButton');
                if (descargarBtn) {
                    descargarBtn.addEventListener('click', function() {
                        try {
                            const parsedJson = JSON.parse(jsonData);
                            const blob = new Blob([JSON.stringify(parsedJson, null, 2)], {
                                type: 'application/json'
                            });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `{{ $codGeneracion }}.json`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        } catch (error) {
                            alert('Error al descargar el archivo JSON');
                            console.error(error);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
