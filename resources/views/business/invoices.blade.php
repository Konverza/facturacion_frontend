@php
    $tiposDte = [
        '01' => 'Factura Electrónica',
        '03' => 'Crédito Fiscal',
        '05' => 'Nota de Crédito',
        '06' => 'Nota de Débito',
        '07' => 'Comprobante de Retención',
        '11' => 'Factura de Exportación',
        '14' => 'Factura de Sujeto Excluido',
    ];

    $receptores_nit = ['03', '05', '06'];
    $receptores_num = ['01', '07', '11', '14'];
@endphp
@extends('layouts.app')
@include('business.layouts.navbar')
@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'Aceptar'
                });
            </script>
        @endif
        @if (session("error"))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Aceptar'
                });
            </script>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row mt-5 mb-2">
                    <div class="col-md-12">
                        <div class="header-content">
                            <div class="row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                    <h1 class="header-title text-center">Documentos Emitidos</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row my-4">
                    <table class="table small table-bordered table-hover table-striped w-100 align-middle" id="invoicesTable">
                        <thead>
                            <tr class="align-middle text-center">
                                <th style="width: 5%;">ID</th>
                                <th style="width: 10%;">Tipo de Documento</th>
                                <th style="width: 15%;">Información Hacienda</th>
                                <th style="width: 15%;">Información Receptor</th>
                                <th style="width: 10%;">Fecha Procesamiento</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 15%;">Observaciones</th>
                                <th style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                @if ($invoice['estado'] == 'RECHAZADO')
                                    <tr class="table-danger">
                                    @elseif ($invoice['estado'] == 'CONTINGENCIA')
                                    <tr class="table-warning">
                                    @else
                                    <tr>
                                @endif
                                <td>{{ $invoice['id'] }}</td>
                                <td>{{ $tiposDte[$invoice['tipo_dte']] }}</td>
                                <td class="small">
                                    <p>
                                        <strong>Código Generacion:</strong><br>{{ $invoice['codGeneracion'] }}<br>
                                        <strong>Número de
                                            Control:</strong><br>{{ $invoice['documento']->identificacion->numeroControl }}<br>
                                        @if ($invoice['selloRecibido'])
                                            <strong>Sello de Recibido:</strong><br>{{ $invoice['selloRecibido'] }}
                                        @endif
                                    </p>
                                </td>
                                <td>
                                    @php
                                        $nombre = '';
                                        $documento = '';

                                        if ($invoice['tipo_dte'] == '14') {
                                            $receptor = $invoice['documento']->sujetoExcluido;
                                        } else {
                                            $receptor = $invoice['documento']->receptor;
                                        }

                                        $nombre = $receptor->nombre;
                                        if (in_array($invoice['tipo_dte'], $receptores_nit)) {
                                            $documento = $receptor->nit;
                                        } else {
                                            $documento = $receptor->numDocumento;
                                        }
                                    @endphp
                                    <p>
                                        @if ($nombre)
                                            <strong>Nombre:<br></strong> {{ $nombre }}<br>
                                        @endif
                                        @if ($documento)
                                            <strong>Identicacion:<br></strong> {{ $documento }}
                                        @endif
                                    </p>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($invoice['fhProcesamiento'])->format('d/m/Y H:i:s') }}
                                </td>
                                <td>{{ $invoice['estado'] }}</td>
                                <td class="small">
                                    {{-- {{ $invoice['observaciones'] }} --}}
                                    @php
                                        $decoded = json_decode($invoice['observaciones'], true);
                                    @endphp
                                    @if (is_array($decoded))
                                        @if (!empty($decoded))
                                            @if (array_key_exists('descripcionMsg', $decoded))
                                                <p>{{ $decoded['descripcionMsg'] }}</p>
                                            @else
                                                @foreach ($decoded as $observacion)
                                                    <p>{{ trim($observacion, '[]') }}</p>
                                                @endforeach
                                            @endif
                                        @endif
                                    @else
                                        @if (str_starts_with($invoice['observaciones'], '[') && str_ends_with($invoice['observaciones'], ']'))
                                            @php
                                                $json_string = str_replace("'", "\"", $invoice['observaciones']);
                                                // Decode the JSON string to a PHP array
                                                $array = json_decode($json_string, true);
                                            @endphp
                                            @if (is_array($array))
                                                @foreach ($array as $observacion)
                                                    <p>{{ $observacion }}</p>
                                                @endforeach
                                            @endif
                                        @else
                                            <p>{{ $invoice['observaciones'] }}</p>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($invoice['estado'] === 'CONTINGENCIA' || $invoice['estado'] === 'RECHAZADO')
                                    @else
                                    <div class="d-inline-flex align-items-center">
                                        <div class="dropdown d-inline-flex">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <li>
                                                    <a href="{{ $invoice['enlace_pdf'] }}" class="dropdown-item"
                                                        target="_blank">
                                                        <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ $invoice['enlace_json'] }}" class="dropdown-item"
                                                        target="_blank">
                                                        <i class="fas fa-file-code me-2"></i> Ver JSON
                                                    </a>
                                                </li>
                                                @if($invoice['enlace_rtf'])
                                                    <li>
                                                        <a href="{{ $invoice['enlace_rtf'] }}" class="dropdown-item"
                                                            target="_blank">
                                                            <i class="fas fa-file-alt me-2"></i> Ver Tiquete
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <button type="button" class="dropdown-item btn-modal"
                                                        data-bs-toggle="modal" data-bs-target="#mailModal"
                                                        data-id="{{ $invoice['codGeneracion'] }}">
                                                        <i class="fas fa-envelope me-2"></i> Reenviar Correo
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                        @if($invoice["estado"] == "PROCESADO")
                                            @php
                                                $fecha_procesado = \Carbon\Carbon::parse($invoice['fhProcesamiento']);
                                            @endphp
                                            @if(in_array($invoice['tipo_dte'], ['01', '11']) && $fecha_procesado->diffInDays() < 90)
                                                <button type="button" class="btn btn-danger btn-anular ms-2"data-bs-toggle="modal" data-bs-target="#anularModal"
                                                data-id="{{ $invoice['codGeneracion'] }}">
                                                    <i class="fas fa-times-circle me-2"></i> Anular
                                                </button>
                                            @elseif($fecha_procesado->diffInDays() > 1)
                                                <button type="button" class="btn btn-danger btn-anular ms-2"data-bs-toggle="modal" data-bs-target="#anularModal"
                                                data-id="{{ $invoice['codGeneracion'] }}">
                                                    <i class="fas fa-times-circle me-2"></i> Anular
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="align-middle text-center">
                                <th>ID</th>
                                <th>Tipo de Documento</th>
                                <th>Información Hacienda</th>
                                <th>Información Receptor</th>
                                <th>Fecha Procesamiento</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Mail Modal -->
    <div class="modal fade" id="mailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reenviar Correo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoices.send') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="mail">Dirección de Correo:</label>
                            <input type="email" class="form-control" id="mail" name="correo"
                                aria-describedby="emailHelp">
                            <small id="emailHelp" class="form-text text-muted">Correo electrónico del destinatario de este
                                DTE</small>
                            <input type="hidden" name="codGeneracion" value="" id="codGeneracion">
                        </div>
                        <div class="form-group mt-2">
                            <input type="submit" value="Enviar Correo" class="btn btn-success">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Anular Modal -->
    <div class="modal fade" id="anularModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Anular DTE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('business.anulacion') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="motivo">Motivo de Invalidación:</label>
                            <input type="text" name="motivo" id="motivo" class="form-control" required>
                            <small id="emailHelp" class="form-text text-muted">Motivo por el cual se invalida este DTE</small>
                        </div>
                        <div class="form-group mt-2">
                            <input type="hidden" name="codGeneracion" value="" id="codGeneracionAnular">
                            <input type="submit" value="Anular DTE" class="btn btn-success">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/invoices.js')
@endsection
