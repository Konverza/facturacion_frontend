@extends('layouts.auth-template')
@section('title', 'Documentos emitidos')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Documentos emitidos
            </h1>
        </div>
        <div class="mt-4 pb-8">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[6]">
                    <x-input type="text" placeholder="Buscar" class="w-full" icon="search" id="input-search-special" />
                </div>
                @if(!auth()->user()->only_fcf)
                    <div class="flex-1">
                    <button type="button"
                        class="show-modal bg-green-500 text-white hover:bg-green-600 dark:bg-green-500 dark:text-white dark:hover:bg-green-600 font-medium rounded-lg flex items-center justify-center gap-1 transition-colors duration-300 text-nowrap  px-4 py-2.5 w-full"
                        data-target="#download-dtes">
                        <x-icon icon="file" class="h-4 w-4" />
                        <span class="text-sm">Descargar DTEs</span>
                    </button>
                </div>
                <div class="flex-1">
                    <button type="button"
                        class="show-modal bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-500 dark:text-white dark:hover:bg-blue-600 font-medium rounded-lg flex items-center justify-center gap-1 transition-colors duration-300 text-nowrap  px-4 py-2.5 w-full"
                        data-target="#download-anexos">
                        <x-icon icon="download" class="h-4 w-4" />
                        <span class="text-sm">Descargar Anexos</span>
                    </button>
                </div>
                @endif
            </div>
            <x-table id="table-special">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Tipo</x-th>
                        <x-th>Hacienda</x-th>
                        <x-th>Receptor</x-th>
                        <x-th>Fecha</x-th>
                        <x-th>Estado</x-th>
                        <x-th>Observaciones</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($invoices as $invoice)
                        @php
                            $status = $invoice['estado'];
                            $style = '';
                            if ($status == 'RECHAZADO') {
                                $style = 'bg-red-100 dark:bg-red-950/30';
                            }

                            if ($status == 'CONTINGENCIA' || $status == 'ANULADO') {
                                $style = 'bg-yellow-100 dark:bg-yellow-950/30';
                            }
                        @endphp

                        <x-tr class="{{ $style }}" :last="$loop->last">
                            <x-td>{{ $invoice['id'] }}</x-td>
                            <x-td>{{ $types[$invoice['tipo_dte']] }}</x-td>
                            <x-td>
                                <div class="flex flex-col gap-1 text-xs">
                                    <span class="font-semibold">Código generación:</span>
                                    <span>{{ $invoice['codGeneracion'] }}</span>
                                    <span class="font-semibold">Número de control:</span>
        <span>{{ $invoice['documento']->identificacion->numeroControl }}</span>
                                    <span class="font-semibold">Sello de recibido:</span>
                                    <span>{{ $invoice['selloRecibido'] }}</span>
                                </div>
                            </x-td>
                            <x-td>
                                @php
                                    $nombre = '';
                                    $documento = '';

                                    if ($invoice['tipo_dte'] === '14') {
                                        $receptor = $invoice['documento']->sujetoExcluido;
                                    } else {
                                        $receptor = $invoice['documento']->receptor;
                                    }

                                    $nombre = $receptor->nombre;

                                    $documento = (in_array($invoice['tipo_dte'], $receptores_nit)) ? $receptor->nit : $receptor->numDocumento;
                                @endphp
                                @if ($nombre)
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Nombre:</span>
                                        <span>{{ $nombre }}</span>
                                    </div>
                                @endif

                                @if ($documento)
                                    <div class="flex flex-col gap-1 text-xs">
                                        <span class="font-semibold">Identificación:</span>
                                        <span>{{ $documento }}</span>
                                    </div>
                                @endif
                            </x-td>
                            <x-td>
                                <span class="text-xs">
                                    {{ \Carbon\Carbon::parse($invoice['fhProcesamiento'])->format('d/m/Y h:i:s A') }}
                                </span>
                            </x-td>
                            <x-td class="uppercase">
                                @if ($invoice['estado'] === 'PROCESADO')
                                    <span class="flex items-center gap-1 text-xs font-semibold text-green-500">
                                        <x-icon icon="check" class="h-4 w-4" />
                                        Procesado
                                    </span>
                                @elseif($invoice['estado'] === 'RECHAZADO')
                                    <span class="flex items-center gap-1 text-xs font-semibold text-red-500">
                                        <x-icon icon="x" class="h-4 w-4" />
                                        Rechazado
                                    </span>
                                @elseif($invoice['estado'] === 'CONTINGENCIA')
                                    <span class="flex items-center gap-1 text-xs font-semibold text-yellow-500">
                                        <x-icon icon="alert-triangle" class="h-4 w-4" />
                                        Contingencia
                                    </span>
                                @elseif($invoice['estado'] === 'ANULADO')
                                    <span class="flex items-center gap-1 text-xs font-semibold text-yellow-500">
                                        <x-icon icon="circle-off" class="h-4 w-4" />
                                        Anulado
                                    </span>
                                @endif
                            </x-td>
                            <x-td>
                                <small class="text-[10px] text-gray-500 dark:text-gray-300">
                                    @if ($invoice['observaciones'] != '[]')
                                        {{ $invoice['observaciones'] }}
                                    @endif
                                </small>
                            </x-td>
                            <x-td th :last="true">
                                @if ($invoice['estado'] !== 'RECHAZADO')
                                    <div class="relative">
                                        <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                            class="show-options" data-target="#options-dtes-{{ $loop->iteration }}"
                                            size="small" />
                                        <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-1 dark:border-gray-800 dark:bg-gray-950"
                                            id="options-dtes-{{ $loop->iteration }}">
                                            <ul class="flex flex-col text-xs">
                                                <li>
                                                    <a href="{{ $invoice['enlace_pdf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="pdf" class="h-4 w-4" />
                                                        Ver PDF
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $invoice['enlace_json'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="file-code" class="h-4 w-4" />
                                                        Ver JSON
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $invoice['enlace_rtf'] }}" target="_blank"
                                                        class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                        <x-icon icon="file-barcode" class="h-4 w-4" />
                                                        Ver tiquete
                                                    </a>
                                                </li>
                                                @if ($invoice['estado'] !== 'ANULADO')
                                                    <li>
                                                        <button type="button" data-target="#send-email"
                                                            class="show-modal btn-send-email flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                            data-id="{{ $invoice['codGeneracion'] }}">
                                                            <x-icon icon="email-forward"
                                                                class="h-4 max-h-4 min-h-4 w-4 min-w-4 max-w-4" />
                                                            Reenviar correo
                                                        </button>
                                                    </li>

                                                    {{-- <li>
                                                                    <button type="button" data-target="#send-whatsapp"
                                                                        class="show-modal btn-send-whatsapp flex w-full items-center gap-1 text-nowrap rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900"
                                                                        data-id="{{ $invoice['codGeneracion'] }}">
                                                                        <x-icon icon="whatsapp" class="h-4 max-h-4 min-h-4 w-4 min-w-4 max-w-4" />
                                                                        Enviar por WhatsApp
                                                                    </button>
                                                                </li> --}}
                                                    <li>
                                                        <button type="button"
                                                            class="show-modal btn-anular-dte flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30"
                                                            data-target="#anular-dte"
                                                            data-id="{{ $invoice['codGeneracion'] }}">
                                                            <x-icon icon="x" class="h-4 w-4" />
                                                            Anular
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </x-td>
                        </x-tr>
                    @endforeach
                </x-slot>
            </x-table>
        </div>

        <div id="anular-dte" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.dte.anular') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Anular DTE
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#anular-dte">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="codGeneracion" id="cod-generacion-anular">
                                <x-input type="textarea" placeholder="Ingresa el motivo de la anulación del dte"
                                    name="motivo" label="Motivo de anulación" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#anular-dte" />
                                <x-button type="submit" text="Anular dte" icon="circle-off" typeButton="danger" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="send-email" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.dte.send-email') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Reenviar correo
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#send-email">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="codGeneracion" id="cod-generacion-email">
                                <x-input type="email" icon="email"
                                    placeholder="Ingresa el correo electrónico del destinatario" name="email"
                                    label="Correo electrónico" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#send-email" />
                                <x-button type="submit" text="Reenviar correo" icon="email-forward"
                                    typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="send-whatsapp" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.dte.send-whatsapp') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Reenviar correo
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#send-whatsapp">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <input type="hidden" name="codGeneracion" id="cod-generacion-whatsapp">
                                <x-input type="text" icon="phone" placeholder="503XXXXXXXX" name="phone"
                                    label="Telefono del destinatario" required />
                                <small class="text-xs text-gray-500 dark:text-gray-300">
                                    El número de teléfono debe incluir el código de país y el número de teléfono sin
                                    espacios ni guiones.
                                </small>
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#send-whatsapp" />
                                <x-button type="submit" text="Enviar mensaje" icon="send" typeButton="primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="download-anexos" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.dte.anexos') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Descargar Anexos
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#download-anexos">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <x-select id="tipo" :options="[
                                    '1' => 'F07 - Detalle de Ventas al Contribuyente',
                                    '2' => 'F07 - Detalle de Ventas al Consumidor Final',
                                ]" label="Tipo de Anexo" name="tipo"
                                    required />
                                <x-input type="date" icon="calendar" name="desde" label="Desde" required />
                                <x-input type="date" icon="calendar" name="hasta" label="Hasta" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#download-anexos" />
                                <x-button type="button" text="Descargar" icon="download" typeButton="primary" id="downloadAnexos" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="download-dtes" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <!-- Modal content -->
                <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                    <div class="flex flex-col">
                        <!-- Modal header -->
                        <form action="{{ Route('business.dte.download-dtes') }}" method="POST">
                            @csrf
                            <div
                                class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Descargar DTEs
                                </h3>
                                <button type="button"
                                    class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                    data-target="#download-dtes">
                                    <x-icon icon="x" class="h-5 w-5" />
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="flex flex-col gap-4 p-4">
                                <x-input type="date" icon="calendar" name="desde" label="Desde" required />
                                <x-input type="date" icon="calendar" name="hasta" label="Hasta" required />
                            </div>
                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                                <x-button type="button" class="hide-modal" text="Cancelar" icon="x"
                                    typeButton="secondary" data-target="#download-dtes" />
                                <x-button type="button" text="Descargar" icon="download" typeButton="primary" id="downloadFiles" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @push('scripts')
        <script>
            document.getElementById("downloadAnexos").addEventListener("click", async function(event) {
                event.preventDefault();
                console.log("Descarga iniciada");
                document.getElementById("loader").classList.remove("hidden");

                const form = document.querySelector("#download-anexos form");
                const formData = new FormData(form);
                const filename = (formData.get("tipo") == "1" ? "f07-contribuyente" : "f07-consumidor-final") + "_" + formData.get("desde") + "_" + formData.get("hasta") + ".csv";

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        body: formData
                    });

                    if (response.headers.get("X-Download-Started")) {
                        document.getElementById("loader").classList.add("hidden");
                    }

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                } catch (error) {
                    console.error("Error en la descarga:", error);
                    document.getElementById("loader").classList.add("hidden");
                }
            });

            document.getElementById("downloadFiles").addEventListener("click", async function(event) {
                event.preventDefault();
                console.log("Descarga iniciada");
                document.getElementById("loader").classList.remove("hidden");

                const form = document.querySelector("#download-dtes form");
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: "POST",
                        body: formData
                    });

                    if (response.headers.get("X-Download-Started")) {
                        document.getElementById("loader").classList.add("hidden");
                    }

                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = response.headers.get("X-File-Name");
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                } catch (error) {
                    console.error("Error en la descarga:", error);
                    document.getElementById("loader").classList.add("hidden");
                }
            });
        </script>
    @endpush
@endsection
