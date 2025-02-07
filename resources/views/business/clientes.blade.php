@extends('layouts.app')
@include('business.layouts.navbar')

@section('content')
    <div class="container-fluid">
        <div class="row mt-5 mb-2">
            <div class="col-md-12">
                <div class="header-content">
                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <h1 class="header-title text-center">Clientes Registrados</h1>
                        </div>
                        <div class="col-lg-4 text-right">
                            <!-- Button trigger modal Agregar DTE Físico-->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#aggCliente">
                                Agregar Cliente
                            </button>

                            <!-- Modal DTE Fisico -->
                            <div class="modal modal-lg fade" id="aggCliente" tabindex="-1" aria-hidden="true"
                                data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="aggCliente">Nuevo Cliente</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{route("business.clientes.store")}}" method="POST" id="formCliente">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="tipoDocumento">Tipo de Documento:</label>
                                                            <select name="tipoDocumento" id="tipoDocumento"
                                                                class="form-select" required>
                                                                <option value="">-- Seleccione Uno --</option>
                                                                @foreach ($tiposDocs as $tipoDoc)
                                                                    <option value="{{ $tipoDoc->codigo }}">
                                                                        {{ $tipoDoc->valores }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label for="numDocumento">Número de Documento:</label>
                                                            <input type="text" class="form-control" id="numDocumento"
                                                                name="numDocumento" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="nombre">Nombre/Razón Social:</label>
                                                            <input type="text" class="form-control" id="nombre"
                                                                name="nombre" required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="nrc">NRC:</label>
                                                            <input type="text" class="form-control" id="nrc"
                                                                name="nrc">
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="nombreComercial">Nombre
                                                        Comercial:</label>
                                                    <input type="text" class="form-control" id="nombreComercial"
                                                        name="nombreComercial">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="codActividad">Actividad
                                                        Económica:</label>
                                                    <input type="text" name="codActividad" id="codActividad"
                                                        class="form-control" autocomplete="off">
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="departamento">Departamento:</label>
                                                            <select class="form-select" id="departamento" name="departamento" required>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="municipio">Municipio:</label>
                                                            <select class="form-select" id="municipio" name="municipio" required>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="complemento">Dirección:</label>
                                                    <textarea class="form-control" id="complemento" name="complemento" required></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="correo">Correo
                                                                electrónico:</label>
                                                            <input type="email" class="form-control" id="correo"
                                                                name="correo" required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="telefono">Teléfono:</label>
                                                            <input type="text" class="form-control" id="telefono"
                                                                name="telefono" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value=""
                                                            id="clienteExportacion">
                                                        <label class="form-check-label" for="clienteExportacion">
                                                            Rellenar Datos de Exportación
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row" id="personaExportacion">
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="codPais">País:</label>
                                                            <input type="text" class="form-control" id="codPais" name="codPais" autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group mb-3">
                                                            <label for="tipoPersona">Tipo de Persona:</label>
                                                            <select class="form-select" id="tipoPersona" name="tipoPersona">
                                                                <option value="">-- Seleccione Uno --</option>
                                                                <option value="1">Persona Natural</option>
                                                                <option value="2">Persona Jurídica</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="justify-content-center row">
            <div class="col-md-10">
                <h4 class="mt-4"></h4>
                <table class="table shadow table-striped table-bordered mb-2" id="customerTable">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>NRC</th>
                            <th>Nombre del Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($business_customers as $customer)
                            <tr>
                                <td>{{ $customer->numDocumento }}</td>
                                <td>{{ $customer->nrc }}</td>
                                <td>{{ $customer->nombre }}</td>
                                <td>
                                    <button class="btn shadow btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#aggCliente" data-id="{{$customer->id}}">Editar</button>
                                    <form action="{{ route('business.clientes.destroy', $customer->id) }}" method="POST"
                                        class="d-inline frm-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Identificación</th>
                            <th>NRC</th>
                            <th>Nombre del Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
    @vite('resources/js/customers.js')
@endsection
