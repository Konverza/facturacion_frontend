@extends('layouts.auth-template')
@section('title', 'Detalle DTE Recibido')
@section('content')
    @php
        $documento = json_decode($dte['documento'] ?? '{}');
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
        
        // Obtener el tipo de DTE para uso en todo el template
        $tipoDte = $documento->identificacion->tipoDte;
    @endphp
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Detalle del DTE Recibido
            </h1>
            <div class="flex gap-2">
                <x-button type="button" typeButton="danger" icon="pdf" id="descargarPdfButton" text="Descargar PDF" />
                <x-button type="button" typeButton="primary" icon="file-code" id="descargarJsonButton" text="Descargar JSON" />
            </div>
        </div>

        {{-- Identificación --}}
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
                        {{ $dte['selloRecibido'] ?? '' }}
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
                            $status = $dte['estado'] ?? '';
                            $style = '';
                            if ($status == 'RECHAZADO') {
                                $style = 'bg-red-100 dark:bg-red-950/30';
                            }

                            if ($status == 'CONTINGENCIA' || $status == 'ANULADO') {
                                $style = 'bg-yellow-100 dark:bg-yellow-950/30';
                            }
                        @endphp
                        @if (($dte['estado'] ?? '') === 'PROCESADO' || ($dte['estado'] ?? '') === 'VALIDADO' || ($dte['estado'] ?? '') === 'OBSERVADO')
                            <span
                                class="flex items-center gap-1 bg-green-200 dark:bg-green-900/50 px-2 py-1 rounded-lg w-max font-bold text-green-800 dark:text-green-300 text-xs uppercase text-nowrap">
                                <x-icon icon="circle-check" class="size-4" />
                                Procesado
                            </span>
                        @elseif(($dte['estado'] ?? '') === 'RECHAZADO')
                            <span
                                class="flex items-center gap-1 bg-red-200 dark:bg-red-900/50 px-2 py-1 rounded-lg w-max font-bold text-red-800 dark:text-red-300 text-xs uppercase text-nowrap">
                                <x-icon icon="circle-x" class="size-4" />
                                Rechazado
                            </span>
                        @elseif(($dte['estado'] ?? '') === 'CONTINGENCIA')
                            <span
                                class="flex items-center gap-1 bg-yellow-200 dark:bg-yellow-900/50 px-2 py-1 rounded-lg w-max font-bold text-yellow-800 dark:text-yellow-300 text-xs uppercase text-nowrap">
                                <x-icon icon="warning" class="size-4" />
                                Contingencia
                            </span>
                        @elseif(($dte['estado'] ?? '') === 'ANULADO')
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
                        {{ $types[$dte['tipo_dte'] ?? ''] ?? 'Documento Tributario Electrónico' }}
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
                    @if (isset($emisor->nombreComercial) && $emisor->nombreComercial)
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
                    @if (isset($emisor->direccion) && $emisor->direccion)
                        <div>
                            <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                Dirección
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $emisor->direccion->complemento }},
                                {{ ucwords(Str::lower($catalogos['departamentos'][$emisor->direccion->departamento]['municipios'][$emisor->direccion->municipio]['nombre'] ?? '')) }},
                                {{ $catalogos['departamentos'][$emisor->direccion->departamento]['nombre'] ?? '' }}
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
                        @if (isset($receptor->direccion) && $receptor->direccion)
                            <div>
                                <div class="mb-1 text-xs font-semibold text-slate-500 dark:text-slate-300">
                                    Dirección
                                </div>
                                <div class="break-all text-sm text-slate-900 dark:text-white">
                                    {{ $receptor->direccion->complemento }},
                                    {{ ucwords(Str::lower($catalogos['departamentos'][$receptor->direccion->departamento]['municipios'][$receptor->direccion->municipio]['nombre'] ?? '')) }},
                                    {{ $catalogos['departamentos'][$receptor->direccion->departamento]['nombre'] ?? '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Card documentos Relacionados -->
        @if (isset($documento->documentoRelacionado) && $documento->documentoRelacionado)
            <x-card title="Documentos Relacionados" icon="document-multiple" :collapsible="false" :collapsed="false"
                class="mt-6">
                <div class="grid grid-cols-1 gap-4">
                    <x-table id="related-documents-table">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th :first="true">Tipo de Documento</x-th>
                                <x-th>Fecha de Emisión</x-th>
                                <x-th :last="true">Número de Documento/Código Generación</x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @forelse($documento->documentoRelacionado as $docRel)
                                <x-tr :last="$loop->last">
                                    <x-td :first="true">
                                        {{ $types[$docRel->tipoDocumento] ?? 'N/A' }}
                                    </x-td>
                                    <x-td>
                                        {{ \Carbon\Carbon::parse($docRel->fechaEmision)->format('d/m/Y') }}
                                    </x-td>
                                    <x-td :last="true" class="break-all font-mono">
                                        {{ $docRel->numeroDocumento }}
                                    </x-td>
                                </x-tr>
                            @empty
                                <x-tr>
                                    <x-td :first="true" :last="true" colspan="3" class="text-center">
                                        No hay documentos relacionados.
                                    </x-td>
                                </x-tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>
            </x-card>
        @endif

        {{-- Card de VentaTercero --}}
        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11']) && isset($documento->ventaTercero))
            <x-card title="Ventas por Cuenta de Terceros" icon="user-group" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            NIT del Tercero
                        </div>
                        <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                            {{ $documento->ventaTercero->nit }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Nombre o Razón Social
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            {{ $documento->ventaTercero->nombre }}
                        </div>
                    </div>
                </div>
            </x-card>
        @endif

        {{-- Card de Otros Documentos --}}
        @if (in_array($tipoDte, ['01', '03', '11', '15']) && isset($documento->otrosDocumentos) && is_array($documento->otrosDocumentos) && count($documento->otrosDocumentos) > 0)
            <x-card title="Otros Documentos Asociados" icon="document-multiple" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="space-y-6">
                    @foreach($documento->otrosDocumentos as $index => $otroDoc)
                        <div class="border-b border-slate-200 dark:border-slate-700 pb-4 last:border-b-0 last:pb-0">
                            <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                                Documento {{ $index + 1 }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Código de Documento
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $otroDoc->codDocAsociado }}
                                    </div>
                                </div>
                                
                                @if (isset($otroDoc->descDocumento) && $otroDoc->descDocumento)
                                    <div class="md:col-span-2">
                                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                            Descripción
                                        </div>
                                        <div class="text-sm text-slate-900 dark:text-white">
                                            {{ $otroDoc->descDocumento }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if (isset($otroDoc->detalleDocumento) && $otroDoc->detalleDocumento)
                                    <div class="md:col-span-3">
                                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                            Detalle
                                        </div>
                                        <div class="text-sm text-slate-900 dark:text-white">
                                            {{ $otroDoc->detalleDocumento }}
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Sección Médico (solo DTEs 01, 03 cuando codDocAsociado = 3) --}}
                                @if (in_array($tipoDte, ['01', '03']) && isset($otroDoc->medico))
                                    <div class="md:col-span-3 mt-4">
                                        <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 border-l-4 border-primary-500 pl-2">
                                            Información del Médico
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pl-4">
                                            <div>
                                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                    Nombre
                                                </div>
                                                <div class="text-sm text-slate-900 dark:text-white">
                                                    {{ $otroDoc->medico->nombre }}
                                                </div>
                                            </div>
                                            @if (isset($otroDoc->medico->nit) && $otroDoc->medico->nit)
                                                <div>
                                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                        NIT
                                                    </div>
                                                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                                        {{ $otroDoc->medico->nit }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($otroDoc->medico->docIdentificacion) && $otroDoc->medico->docIdentificacion)
                                                <div>
                                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                        Documento Extranjero
                                                    </div>
                                                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                                        {{ $otroDoc->medico->docIdentificacion }}
                                                    </div>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                    Tipo de Servicio
                                                </div>
                                                <div class="text-sm text-slate-900 dark:text-white">
                                                    {{ $catalogos["tipo_servicio"][$otroDoc->medico->tipoServicio] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Sección Transporte (solo DTE 11 cuando codDocAsociado = 4) --}}
                                @if ($tipoDte === '11' && isset($otroDoc->modoTransp))
                                    <div class="md:col-span-3 mt-4">
                                        <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 border-l-4 border-primary-500 pl-2">
                                            Información de Transporte
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pl-4">
                                            <div>
                                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                    Modo de Transporte
                                                </div>
                                                <div class="text-sm text-slate-900 dark:text-white">
                                                    @php
                                                        $modos = [
                                                            1 => 'Marítimo',
                                                            2 => 'Aéreo',
                                                            3 => 'Terrestre',
                                                            4 => 'Ferroviario',
                                                            5 => 'Otro',
                                                            6 => 'Multimodal',
                                                            7 => 'Postales'
                                                        ];
                                                    @endphp
                                                    {{ $modos[$otroDoc->modoTransp] ?? $otroDoc->modoTransp }}
                                                </div>
                                            </div>
                                            @if (isset($otroDoc->placaTrans) && $otroDoc->placaTrans)
                                                <div>
                                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                        Placa/Identificación
                                                    </div>
                                                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                                        {{ $otroDoc->placaTrans }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($otroDoc->numConductor) && $otroDoc->numConductor)
                                                <div>
                                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                        Doc. Conductor
                                                    </div>
                                                    <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                                        {{ $otroDoc->numConductor }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($otroDoc->nombreConductor) && $otroDoc->nombreConductor)
                                                <div>
                                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                                        Nombre Conductor
                                                    </div>
                                                    <div class="text-sm text-slate-900 dark:text-white">
                                                        {{ $otroDoc->nombreConductor }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Card del cuerpo del documento --}}
        <x-card title="Cuerpo del Documento" icon="document-text" :collapsible="false" :collapsed="false" class="mt-6">
            @php
                $cuerpoDocumento = $documento->cuerpoDocumento ?? [];
            @endphp

            @if ($tipoDte === '09')
                {{-- Documento Contable de Liquidación - Mostrar como campos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Periodo de Liquidación
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($cuerpoDocumento->periodoLiquidacionFechaInicio ?? '')->format('d/m/Y') }}
                            - {{ \Carbon\Carbon::parse($cuerpoDocumento->periodoLiquidacionFechaFin ?? '')->format('d/m/Y') }}
                        </div>
                    </div>
                    @if ($cuerpoDocumento->codLiquidacion ?? null)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Código de Liquidación
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $cuerpoDocumento->codLiquidacion }}
                            </div>
                        </div>
                    @endif
                    @if ($cuerpoDocumento->cantidadDoc ?? null)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Cantidad de Documentos
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ number_format($cuerpoDocumento->cantidadDoc, 0) }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Valor de Operaciones
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->valorOperaciones ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Monto sin Percepción
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->montoSinPercepcion ?? 0, 2) }}
                        </div>
                    </div>
                    @if ($cuerpoDocumento->descripSinPercepcion ?? null)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Descripción sin Percepción
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $cuerpoDocumento->descripSinPercepcion }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Subtotal
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->subTotal ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            IVA
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->iva ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Monto Sujeto a Percepción
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->montoSujetoPercepcion ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            IVA Percibido (1%)
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->ivaPercibido ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Comisión
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->comision ?? 0, 2) }}
                        </div>
                    </div>
                    @if ($cuerpoDocumento->porcentComision ?? null)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Porcentaje de Comisión
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $cuerpoDocumento->porcentComision }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            IVA de Comisión
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            ${{ number_format($cuerpoDocumento->ivaComision ?? 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Líquido a Pagar
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white font-bold">
                            ${{ number_format($cuerpoDocumento->liquidoApagar ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                            Total en Letras
                        </div>
                        <div class="break-all text-sm text-slate-900 dark:text-white">
                            {{ $cuerpoDocumento->totalLetras ?? '' }}
                        </div>
                    </div>
                    @if ($cuerpoDocumento->observaciones ?? null)
                        <div class="md:col-span-2 lg:col-span-3">
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Observaciones
                            </div>
                            <div class="break-all text-sm text-slate-900 dark:text-white">
                                {{ $cuerpoDocumento->observaciones }}
                            </div>
                        </div>
                    @endif
                </div>
            @else
                {{-- Todos los demás DTEs - Mostrar como tabla --}}
                <div class="overflow-x-auto">
                    <x-table id="body-document-table">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th :first="true" class="text-center">#</x-th>
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '14', '15']))
                                    <x-th class="text-center">Tipo Item</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '07', '08']))
                                    <x-th class="text-center">Número Documento</x-th>
                                @endif
                                
                                @if ($tipoDte === '07')
                                    <x-th class="text-center">Tipo DTE</x-th>
                                    <x-th class="text-center">Tipo Doc</x-th>
                                    <x-th class="text-center">Fecha Emisión</x-th>
                                @endif
                                
                                @if ($tipoDte === '08')
                                    <x-th class="text-center">Tipo DTE</x-th>
                                    <x-th class="text-center">Tipo Generación</x-th>
                                    <x-th class="text-center">Fecha Generación</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                    <x-th class="text-center">Código</x-th>
                                @endif
                                
                                <x-th class="text-center">Descripción</x-th>
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                    <x-th class="text-center">Cantidad</x-th>
                                    <x-th class="text-center">Unidad</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14']))
                                    <x-th class="text-center">Precio Unitario</x-th>
                                    <x-th class="text-center">Descuento</x-th>
                                @endif
                                
                                @if ($tipoDte === '15')
                                    <x-th class="text-center">Tipo Donación</x-th>
                                    <x-th class="text-center">Depreciación</x-th>
                                    <x-th class="text-center">Valor Unitario</x-th>
                                    <x-th class="text-center">Valor</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08']))
                                    <x-th class="text-center">Ventas No Sujetas</x-th>
                                    <x-th class="text-center">Ventas Exentas</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08', '11']))
                                    <x-th class="text-center">Ventas Gravadas</x-th>
                                @endif
                                
                                @if ($tipoDte === '08')
                                    <x-th class="text-center">Exportaciones</x-th>
                                @endif
                                
                                @if ($tipoDte === '14')
                                    <x-th class="text-center">Compra</x-th>
                                @endif
                                
                                @if (in_array($tipoDte, ['01', '03']))
                                    <x-th class="text-center">PSV</x-th>
                                    <x-th class="text-center">No Gravado</x-th>
                                @endif
                                
                                @if ($tipoDte === '11')
                                    <x-th class="text-center">No Gravado</x-th>
                                @endif
                                
                                @if ($tipoDte === '01' || $tipoDte === '08')
                                    <x-th class="text-center">IVA</x-th>
                                @endif
                                
                                @if ($tipoDte === '07')
                                    <x-th class="text-center">Monto Sujeto Gravado</x-th>
                                    <x-th class="text-center">Código Retención MH</x-th>
                                    <x-th class="text-center">IVA Retenido</x-th>
                                @endif
                                
                                @if ($tipoDte === '08')
                                    <x-th :last="true" class="text-center">Observaciones</x-th>
                                @endif
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @forelse($cuerpoDocumento as $item)
                                <x-tr :last="$loop->last">
                                    <x-td :first="true" class="font-semibold">{{ $item->numItem ?? $loop->iteration }}</x-td>
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '14', '15']))
                                        <x-td>
                                            @if (isset($item->tipoItem))
                                                @php
                                                    $tipoItemLabels = [1 => 'Bien', 2 => 'Servicio', 3 => 'Ambos', 4 => 'Servicio'];
                                                @endphp
                                                {{ $tipoItemLabels[$item->tipoItem] ?? $item->tipoItem }}
                                            @else
                                                -
                                            @endif
                                        </x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '07', '08']))
                                        <x-td class="font-mono text-xs">{{ $item->numeroDocumento ?? '-' }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '07')
                                        <x-td>{{ $types[$item->tipoDte] ?? $item->tipoDte }}</x-td>
                                        <x-td>{{ $item->tipoDoc == 1 ? 'Físico' : 'Electrónico' }}</x-td>
                                        <x-td>{{ \Carbon\Carbon::parse($item->fechaEmision ?? '')->format('d/m/Y') }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '08')
                                        <x-td>{{ $types[$item->tipoDte] ?? $item->tipoDte }}</x-td>
                                        <x-td>{{ $item->tipoGeneracion == 1 ? 'Físico' : 'Electrónico' }}</x-td>
                                        <x-td>{{ \Carbon\Carbon::parse($item->fechaGeneracion ?? '')->format('d/m/Y') }}</x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                        <x-td class="font-mono text-xs">{{ $item->codigo ?? '-' }}</x-td>
                                    @endif
                                    
                                    <x-td class="max-w-xs truncate" title="{{ $item->descripcion ?? '' }}">
                                        {{ $item->descripcion ?? '-' }}
                                    </x-td>
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14', '15']))
                                        <x-td class="text-right">{{ number_format($item->cantidad ?? 0, 2) }}</x-td>
                                        <x-td>{{ $catalogos["unidades_medidas"][$item->uniMedida] ?? '-' }}</x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '11', '14']))
                                        <x-td class="text-right">${{ number_format($item->precioUni ?? 0, 2) }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->montoDescu ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '15')
                                        <x-td>
                                            @php
                                                $tipoDonacionLabels = [1 => 'Dinero', 2 => 'Bien', 3 => 'Servicio'];
                                            @endphp
                                            {{ $tipoDonacionLabels[$item->tipoDonacion ?? 0] ?? '-' }}
                                        </x-td>
                                        <x-td class="text-right">${{ number_format($item->depreciacion ?? 0, 2) }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->valorUni ?? 0, 2) }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->valor ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08']))
                                        <x-td class="text-right">${{ number_format($item->ventaNoSuj ?? 0, 2) }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->ventaExenta ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '08', '11']))
                                        <x-td class="text-right">${{ number_format($item->ventaGravada ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '08')
                                        <x-td class="text-right">${{ number_format($item->exportaciones ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '14')
                                        <x-td class="text-right">${{ number_format($item->compra ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if (in_array($tipoDte, ['01', '03']))
                                        <x-td class="text-right">${{ number_format($item->psv ?? 0, 2) }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->noGravado ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '11')
                                        <x-td class="text-right">${{ number_format($item->noGravado ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '01' || $tipoDte === '08')
                                        <x-td class="text-right">${{ number_format($item->ivaItem ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '07')
                                        <x-td class="text-right">${{ number_format($item->montoSujetoGrav ?? 0, 2) }}</x-td>
                                        <x-td>{{ $item->codigoRetencionMH ?? '-' }}</x-td>
                                        <x-td class="text-right">${{ number_format($item->ivaRetenido ?? 0, 2) }}</x-td>
                                    @endif
                                    
                                    @if ($tipoDte === '08')
                                        <x-td :last="true" class="max-w-xs truncate" title="{{ $item->obsItem ?? '' }}">
                                            {{ $item->obsItem ?? '-' }}
                                        </x-td>
                                    @endif
                                </x-tr>
                            @empty
                                <x-tr>
                                    <x-td :first="true" :last="true" colspan="20" class="text-center">
                                        No hay ítems en el cuerpo del documento.
                                    </x-td>
                                </x-tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>
            @endif
        </x-card>

        {{-- Sección Resumen --}}
        @if ($tipoDte !== '09')
            <x-card title="Resumen del Documento" icon="clipboard-list" :collapsible="false" :collapsed="false"
                class="mt-6">
                @php
                    $resumen = $documento->resumen ?? null;
                    $condicionOperacionLabels = [
                        1 => 'Contado',
                        2 => 'Crédito',
                        3 => 'Otro'
                    ];
                @endphp

                @if ($resumen)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- DTE 07: Comprobante de Retención --}}
                        @if ($tipoDte === '07')
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Sujeto a Retención
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalSujetoRetencion ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total IVA Retenido
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalIVAretenido ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total IVA Retenido en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white">
                                    {{ $resumen->totalIVAretenidoLetras ?? '' }}
                                </div>
                            </div>

                        {{-- DTE 15: Comprobante de Donación --}}
                        @elseif ($tipoDte === '15')
                            <div class="md:col-span-2 lg:col-span-3">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Valor Total
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->valorTotal ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-2">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white uppercase">
                                    {{ $resumen->totalLetras ?? '' }}
                                </div>
                            </div>

                        {{-- DTE 14: Sujeto Excluido --}}
                        @elseif ($tipoDte === '14')
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Compra
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalCompra ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Descuento
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->descu ?? 0, 2) }}
                                </div>
                            </div>
                            @if (isset($resumen->totalDescu))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total Descuentos
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->totalDescu ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Sub-Total
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->subTotal ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    IVA Retenido (1%)
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->ivaRete1 ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Retención de Renta
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->reteRenta ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Condición de Operación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ $condicionOperacionLabels[$resumen->condicionOperacion] ?? $resumen->condicionOperacion }}
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-4">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total a Pagar
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->totalPagar ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-2">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white uppercase">
                                    {{ $resumen->totalLetras ?? '' }}
                                </div>
                            </div>
                            @if (isset($resumen->observaciones))
                                <div class="md:col-span-2 lg:col-span-3">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Observaciones
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $resumen->observaciones }}
                                    </div>
                                </div>
                            @endif

                        {{-- DTE 11: Factura de Exportación --}}
                        @elseif ($tipoDte === '11')
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Gravado
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalGravada ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Descuento
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->descuento ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    % Descuento
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ number_format($resumen->porcentajeDescuento ?? 0, 2) }}%
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Descuentos
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalDescu ?? 0, 2) }}
                                </div>
                            </div>
                            @if (isset($resumen->seguro))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Seguro
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->seguro ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($resumen->flete))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Flete
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->flete ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Monto Total Operación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->montoTotalOperacion ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total No Gravado
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalNoGravado ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Condición de Operación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ $condicionOperacionLabels[$resumen->condicionOperacion] ?? $resumen->condicionOperacion }}
                                </div>
                            </div>
                            @if (isset($resumen->codIncoterms))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Código INCOTERMS
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $resumen->codIncoterms }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($resumen->descIncoterms))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Descripción INCOTERMS
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $resumen->descIncoterms }}
                                    </div>
                                </div>
                            @endif
                            @if (isset($resumen->numPagoElectronico))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Número Pago Electrónico
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white font-mono">
                                        {{ $resumen->numPagoElectronico }}
                                    </div>
                                </div>
                            @endif
                            <div class="md:col-span-2 lg:col-span-3 mt-4">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total a Pagar
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->totalPagar ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-2">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white uppercase">
                                    {{ $resumen->totalLetras ?? '' }}
                                </div>
                            </div>
                            @if (isset($resumen->observaciones))
                                <div class="md:col-span-2 lg:col-span-3">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Observaciones
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $resumen->observaciones }}
                                    </div>
                                </div>
                            @endif

                        {{-- DTE 08: Comprobante de Liquidación --}}
                        @elseif ($tipoDte === '08')
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total No Sujetas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalNoSuj ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Exentas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalExenta ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Gravadas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalGravada ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Exportación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalExportacion ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Sub-Total Ventas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->subTotalVentas ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Monto Total Operación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->montoTotalOperacion ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    IVA Percibido (1%)
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->ivaPerci ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Condición de Operación
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ $condicionOperacionLabels[$resumen->condicionOperacion] ?? $resumen->condicionOperacion }}
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-4">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->total ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="md:col-span-2 lg:col-span-3 mt-2">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white uppercase">
                                    {{ $resumen->totalLetras ?? '' }}
                                </div>
                            </div>
                            @if (isset($resumen->tributos) && is_array($resumen->tributos) && count($resumen->tributos) > 0)
                                <div class="md:col-span-2 lg:col-span-3">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Tributos
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-slate-100 dark:bg-slate-800">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold">Código</th>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold">Descripción</th>
                                                    <th class="px-3 py-2 text-right text-xs font-semibold">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                                @foreach($resumen->tributos as $tributo)
                                                    <tr>
                                                        <td class="px-3 py-2">{{ $tributo->codigo ?? '-' }}</td>
                                                        <td class="px-3 py-2">{{ $tributo->descripcion ?? '-' }}</td>
                                                        <td class="px-3 py-2 text-right">${{ number_format($tributo->valor ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        {{-- DTEs 01, 03, 04, 05, 06: Documentos estándar de ventas --}}
                        @else
                            {{-- Totales de ventas --}}
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total No Sujetas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalNoSuj ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Exentas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalExenta ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Gravadas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalGravada ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Sub-Total Ventas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->subTotalVentas ?? 0, 2) }}
                                </div>
                            </div>

                            {{-- Descuentos --}}
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Desc. No Sujetas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->descuNoSuj ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Desc. Exentas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->descuExenta ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Desc. Gravadas
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->descuGravada ?? 0, 2) }}
                                </div>
                            </div>
                            @if (in_array($tipoDte, ['01', '03', '04']) && isset($resumen->porcentajeDescuento))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        % Descuento
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ number_format($resumen->porcentajeDescuento ?? 0, 2) }}%
                                    </div>
                                </div>
                            @endif
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total Descuentos
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->totalDescu ?? 0, 2) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Sub-Total
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    ${{ number_format($resumen->subTotal ?? 0, 2) }}
                                </div>
                            </div>

                            {{-- Retenciones y percepciones (no en 04) --}}
                            @if ($tipoDte !== '04')
                                @if (in_array($tipoDte, ['03', '05', '06']))
                                    <div>
                                        <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                            IVA Percibido (1%)
                                        </div>
                                        <div class="text-sm text-slate-900 dark:text-white">
                                            ${{ number_format($resumen->ivaPerci1 ?? 0, 2) }}
                                        </div>
                                    </div>
                                @endif
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        IVA Retenido (1%)
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->ivaRete1 ?? 0, 2) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Retención de Renta
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->reteRenta ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif

                            {{-- Campos adicionales según tipo --}}
                            @if (in_array($tipoDte, ['01', '03']))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total No Gravado
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->totalNoGravado ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif

                            @if ($tipoDte === '01' && isset($resumen->totalIva))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total IVA
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->totalIva ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif

                            @if (in_array($tipoDte, ['01', '03']) && isset($resumen->saldoFavor))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Saldo a Favor
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->saldoFavor ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif

                            {{-- @if (in_array($tipoDte, ['04', '05', '06']))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Monto Total Operación
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        ${{ number_format($resumen->montoTotalOperacion ?? 0, 2) }}
                                    </div>
                                </div>
                            @endif --}}

                            @if (in_array($tipoDte, ['01', '03', '05', '06']))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Condición de Operación
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white">
                                        {{ $condicionOperacionLabels[$resumen->condicionOperacion] ?? $resumen->condicionOperacion }}
                                    </div>
                                </div>
                            @endif

                            @if (in_array($tipoDte, ['06']) && isset($resumen->numPagoElectronico))
                                <div>
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Número Pago Electrónico
                                    </div>
                                    <div class="text-sm text-slate-900 dark:text-white font-mono">
                                        {{ $resumen->numPagoElectronico }}
                                    </div>
                                </div>
                            @endif

                            {{-- Tributos --}}
                            @if (isset($resumen->tributos) && is_array($resumen->tributos) && count($resumen->tributos) > 0)
                                <div class="md:col-span-2 lg:col-span-3">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Tributos
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-slate-100 dark:bg-slate-800">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold">Código</th>
                                                    <th class="px-3 py-2 text-left text-xs font-semibold">Descripción</th>
                                                    <th class="px-3 py-2 text-right text-xs font-semibold">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                                @foreach($resumen->tributos as $tributo)
                                                    <tr>
                                                        <td class="px-3 py-2">{{ $tributo->codigo ?? '-' }}</td>
                                                        <td class="px-3 py-2">{{ $tributo->descripcion ?? '-' }}</td>
                                                        <td class="px-3 py-2 text-right">${{ number_format($tributo->valor ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Pagos (presentes en varios tipos de DTE) --}}
                        @if (isset($resumen->pagos) && is_array($resumen->pagos) && count($resumen->pagos) > 0)
                            <div class="md:col-span-2 lg:col-span-3">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Formas de Pago
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-slate-100 dark:bg-slate-800">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-semibold">Código</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold">Monto</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold">Referencia</th>
                                                @if (in_array($tipoDte, ['01', '03', '11', '14']))
                                                    <th class="px-3 py-2 text-left text-xs font-semibold">Plazo</th>
                                                    <th class="px-3 py-2 text-right text-xs font-semibold">Periodo</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                            @foreach($resumen->pagos as $pago)
                                                <tr>
                                                    <td class="px-3 py-2">{{ $catalogos["formas_pago"][$pago->codigo] ?? '-' }}</td>
                                                    <td class="px-3 py-2 text-right">${{ number_format($pago->montoPago ?? 0, 2) }}</td>
                                                    <td class="px-3 py-2">{{ $pago->referencia ?? '-' }}</td>
                                                    @if (in_array($tipoDte, ['01', '03', '11', '14']))
                                                        <td class="px-3 py-2">
                                                            @if(isset($pago->plazo))
                                                                {{ ['01' => 'Días', '02' => 'Meses', '03' => 'Años'][$pago->plazo] ?? $pago->plazo }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-2 text-right">{{ $pago->periodo ?? '-' }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Total destacado --}}
                        @if (in_array($tipoDte, ['01', '03']))
                            <div class="md:col-span-2 lg:col-span-3 mt-4">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Total a Pagar
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->totalPagar ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (in_array($tipoDte, ['04','05', '06']))
                            <div class="md:col-span-2 lg:col-span-3 mt-4">
                                <div class="bg-primary-50 dark:bg-primary-950/20 rounded-lg p-4 border-l-4 border-primary-500">
                                    <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                        Monto Total de la Operación
                                    </div>
                                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        ${{ number_format($resumen->montoTotalOperacion ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Total en letras (solo para DTEs que no lo muestran en su sección específica) --}}
                        @if (!in_array($tipoDte, ['14', '07', '11', '08']))
                            <div class="md:col-span-2 lg:col-span-3 mt-2">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Total en Letras
                                </div>
                                <div class="text-base font-medium text-slate-900 dark:text-white uppercase">
                                    {{ $resumen->totalLetras ?? '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">No hay información de resumen disponible.</p>
                @endif
            </x-card>
        @endif

        {{-- Card de Extensión --}}
        @if (in_array($tipoDte, ['01', '03', '04', '05', '06', '07', '08', '09']) && isset($documento->extension))
            <x-card title="Extensión" icon="clipboard-list" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if (isset($documento->extension->nombEntrega) && $documento->extension->nombEntrega)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Nombre de Quien Entrega
                            </div>
                            <div class="text-sm text-slate-900 dark:text-white">
                                {{ $documento->extension->nombEntrega }}
                            </div>
                        </div>
                    @endif
                    
                    @if (isset($documento->extension->docuEntrega) && $documento->extension->docuEntrega)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                Documento de Quien Entrega
                            </div>
                            <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                {{ $documento->extension->docuEntrega }}
                            </div>
                        </div>
                    @endif
                    
                    {{-- DTE 09 tiene codEmpleado en lugar de nombRecibe/docuRecibe --}}
                    @if ($tipoDte === '09')
                        @if (isset($documento->extension->codEmpleado) && $documento->extension->codEmpleado)
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Código de Empleado
                                </div>
                                <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                    {{ $documento->extension->codEmpleado }}
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- DTEs distintos de 09 tienen nombRecibe y docuRecibe --}}
                        @if (isset($documento->extension->nombRecibe) && $documento->extension->nombRecibe)
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Nombre de Quien Recibe
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white">
                                    {{ $documento->extension->nombRecibe }}
                                </div>
                            </div>
                        @endif
                        
                        @if (isset($documento->extension->docuRecibe) && $documento->extension->docuRecibe)
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Documento de Quien Recibe
                                </div>
                                <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                    {{ $documento->extension->docuRecibe }}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Campo placaVehiculo solo en DTEs 01 y 03 --}}
                        @if (in_array($tipoDte, ['01', '03']) && isset($documento->extension->placaVehiculo) && $documento->extension->placaVehiculo)
                            <div>
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Placa del Vehículo
                                </div>
                                <div class="break-all font-mono text-sm text-slate-900 dark:text-white">
                                    {{ $documento->extension->placaVehiculo }}
                                </div>
                            </div>
                        @endif
                        
                        @if (isset($documento->extension->observaciones) && $documento->extension->observaciones)
                            <div class="md:col-span-2 lg:col-span-3">
                                <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                    Observaciones
                                </div>
                                <div class="text-sm text-slate-900 dark:text-white whitespace-pre-line">
                                    {{ $documento->extension->observaciones }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </x-card>
        @endif

        {{-- Card de Apéndice --}}
        @if (isset($documento->apendice) && is_array($documento->apendice) && count($documento->apendice) > 0)
            <x-card title="Apéndice" icon="squares-plus" :collapsible="false" :collapsed="false" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($documento->apendice as $campo)
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase text-slate-500 dark:text-slate-300">
                                {{ $campo->etiqueta }}
                            </div>
                            <div class="text-sm text-slate-900 dark:text-white">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">[{{ $campo->campo }}]</span>
                                <br>
                                {{ $campo->valor }}
                            </div>
                        </div>
                    @endforeach
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

                // Funcionalidad del botón descargar JSON
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

                // Funcionalidad del botón previsualizar PDF
                const descargarPdfBtn = document.getElementById('descargarPdfButton');
                if (descargarPdfBtn) {
                    descargarPdfBtn.addEventListener('click', function() {
                        // Abrir PDF en nueva pestaña para previsualización
                        const pdfUrl = '{{ route('business.received-documents.download-pdf', $codGeneracion) }}';
                        window.open(pdfUrl, '_blank');
                    });
                }
            });
        </script>
    @endpush
@endsection
