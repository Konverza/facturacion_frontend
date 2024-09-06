@php
    $tiposDte = [
        '01' => 'Factura Electrónica',
        '03' => 'Crédito Fiscal',
        '05' => 'Nota de Crédito',
        '07' => 'Comprobante de Retención',
        '11' => 'Factura de Exportación',
        '14' => 'Factura de Sujeto Excluido',
        '98' => 'Evento de Contingencia',
        '99' => 'Evento de Invalidación',
    ];
    $dtes_plan = json_decode($business_plan["dtes"], true);

@endphp
@extends('layouts.app')
@include('business.layouts.navbar', ['nombreComercial' => $datos_empresa['nombreComercial']])
@section('content')
    <div class="container-fluid">
        <div class="row mt-3 mb-2">
            <div class="col-md-12">
                <div class="header-content">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h1 class="header-title text-center">Bienvenido, {{ Auth::user()->name }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row my-4">
                <div class="col-md-3 text-center">
                    <a href="{{ route('business.dtes') }}" class="card text-decoration-none">
                        <div class="card-body shadow bg-light btn">
                            <div class="row py-2 justify-content-center">
                                <div class="col-3">
                                    <br>
                                    <i class="fas fa-file-lines text-info fa-4x"></i>
                                </div>
                                <div class="col-8">
                                    <h1 class="card-title">{{ $statistics['total'] }} de N/A</h1>
                                    <p class="card-text h4">Documentos<br>Emitidos</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 text-center">
                    <div class="card shadow ">
                        <div class="card-body bg-light btn">
                            <div class="row py-2 justify-content-center">
                                <div class="col-3">
                                    <br>
                                    <i class="fas fa-circle-check text-success fa-4x"></i>
                                </div>
                                <div class="col-8">
                                    <h1 class="card-title">Operativo</h1>
                                    <p class="card-text h4">Estado<br>Hacienda</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <a href="{{ route('business.clientes') }}" class="card text-decoration-none">
                        <div class="card-body shadow bg-light btn">
                            <div class="row py-2 justify-content-center">
                                <div class="col-3">
                                    <br>
                                    <i class="fas fa-user fa-4x"></i>
                                </div>
                                <div class="col-8">
                                    <h1 class="card-title">{{ $customers }}</h1>
                                    <p class="card-text h4">Clientes <br>Registrados </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 text-center">
                    <a href="{{ route('business.productos') }}" class="card text-decoration-none">
                        <div class="card-body shadow bg-light btn">
                            <div class="row py-2 justify-content-center">
                                <div class="col-3">
                                    <br>
                                    <i class="fa fa-box-open text-warning fa-4x"></i>
                                </div>
                                <div class="col-8">
                                    <h1 class="card-title">{{ $productos }}</h1>
                                    <p class="card-text h4">Productos<br>Registrados</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-md-7">
                    <div class="row mb-3">
                        <div class="card-body shadow card">
                            <h4 class="mb-3">Últimos Documentos Emitidos:</h4>
                            <table class="table table-striped table-bordered table-lg">
                                <thead>
                                    <tr>
                                        <th>Tipo de Documento</th>
                                        <th>Fecha de Emisión</th>
                                        <th>Código de Generación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_reverse(array_slice($dtes, -5)) as $invoice)
                                        <tr>
                                            <td>{{ $tiposDte[$invoice['tipo_dte']] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['fhProcesamiento'])->format('d/m/Y H:i:s') }}
                                            </td>
                                            <td>{{ $invoice['codGeneracion'] }}</td>
                                            @if ($invoice['estado'] == 'PROCESADO')
                                                <td class="text-success"><i class="fa-solid fa-check"></i> Procesado</td>
                                            @elseif ($invoice['estado'] == 'RECHAZADO')
                                                <td class="text-danger"><i class="fa-solid fa-times"></i> Rechazado</td>
                                            @else
                                                <td class="text-warning"><i class="fa-solid fa-clock"></i> Contingencia</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body shadow card">
                            <h4 class="mb-3">Pruebas Realizadas (Según el Ministerio de Hacienda):</h4>
                            <table class="table table-striped table-bordered table-lg">
                                <thead>
                                    <tr>
                                        <th>Tipo de Documento</th>
                                        <th>Pruebas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pruebas as $prueba)
                                        <tr>
                                            <td>{{ $tiposDte[$prueba['tipoDte']] }}</td>
                                            </td>
                                            <td>{{ $prueba['cantidad'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow card-body">
                        <h4 class="text-center mb-3">Acciones Rápidas</h4>
                        <div class="btn-toolbar justify-content-center" role="group" aria-label="Basic outlined example">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#nuevoDTEModal"
                                class="btn btn-info mx-1">
                                <i class="fa fa-cart-plus"></i> Generar DTE
                            </button>
                            <a href="{{ route('business.clientes') }}" type="button" class="btn btn-dark mx-1">
                                <i class="fa fa-user-plus"></i> Nuevo Cliente
                            </a>
                            <a href="{{ route('business.productos') }}" type="button" class="btn btn-warning mx-1">
                                <i class="fa fa-dolly"></i> Nuevo Producto
                            </a>
                        </div>
                    </div>
                    <div class="card shadow card-body mt-5">
                        <h4>Resumen:</h4>
                        <hr>
                        <p>
                            <b>NIT: </b> {{ $datos_empresa['nit'] }}<br>
                            <b>NRC: </b> {{ $datos_empresa['nrc'] }}<br>
                            <b>Nombre Comercial: </b> {{ $datos_empresa['nombreComercial'] }}<br>
                            <b>Giro: </b> {{ $datos_empresa['descActividad'] }}<br>
                            <b>Dirección: </b> {{ $datos_empresa['complemento'] }}<br>
                            <b>Teléfono: </b> {{ $datos_empresa['telefono'] }}<br>
                            <b>Correo Electrónico: </b> {{ $datos_empresa['correo'] }}<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="nuevoDTEModal" tabindex="-1" aria-labelledby="nuevoDTEModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoDTEModalLabel">Generar un Nuevo DTE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="dteGenerar" class="form-label">¿Qué documento desea emitir?</label>
                    <select name="dteGenerar" id="dteGenerar" class="form-select">
                        <option value="">-- Seleccione Uno --</option>
                        @foreach ($dtes_plan as $dte)
                            <option value="{{ $dte }}">{{ $tiposDte[$dte] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGenerar" disabled>Generar</button>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/business_dashboard.js')
@endsection
