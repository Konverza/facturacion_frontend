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
                            <h1 class="header-title text-center">Sucursales Registradas</h1>
                        </div>
                        <div class="col-lg-4 text-right">
                            <!-- Button trigger modal Agregar DTE Físico-->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#aggSucursal">
                                Agregar Sucursal
                            </button>

                            <!-- Modal DTE Fisico -->
                            <div class="modal modal-lg fade" id="aggSucursal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="aggSucursal">Nueva Sucursal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title text-muted text-dark font-weight-normal">
                                                        Agregar Sucursal</h5>
                                                </div>
                                                <div class="card-body bg-light ">
                                                    <form>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="bg-light mb-3 mt-3">
                                                                    <div class="card-body fw-bold">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <div class="tab-pane fade show active"
                                                                                id="emisor" role="tabpanel"
                                                                                aria-labelledby="emisor-tab">
                                                                                <form>
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="actividadEconomica">Actividad
                                                                                            Económica:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="actividadEconomica"
                                                                                            value="58200 - Edición de programas informáticos (software)">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="tipoEstablecimiento">Tipo
                                                                                            de
                                                                                            Establecimiento:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="tipoEstablecimiento"
                                                                                            value="Casa Matriz">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label
                                                                                            for="direccion">Establecimiento
                                                                                            /
                                                                                            Dirección:</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            id="direccion"
                                                                                            value="AVE LAS PALMAS NO 7, CASA NO 4, COL SAN BENITO, SAN SALVADOR, EL SALVADOR">
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label for="correo">Correo
                                                                                                    electrónico:</label>
                                                                                                <input type="email"
                                                                                                    class="form-control"
                                                                                                    id="correo"
                                                                                                    value="consultas@konverza.digital">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col">
                                                                                            <div class="form-group">
                                                                                                <label
                                                                                                    for="telefono">Teléfono:</label>
                                                                                                <input type="text"
                                                                                                    class="form-control"
                                                                                                    id="telefono"
                                                                                                    value="7668-1479">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                        <a href="#" role="button">Actualizar</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-primary">Guardar Negocio</button>
                                        </div>
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
                <table class="table shadow table-striped table-bordered mb-2">
                    <thead>
                        <tr>
                            <th>Tipo de Establecimiento</th>
                            <th>Dirección Completa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Supermercado</td>
                            <td>Residencial Casa Bella, Pasaje 7 Norte, Santa Tecla, El Salvador</td>
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Hotel</td>
                            <td>HFM8+37V, Cdad. de Guatemala, Guatemala</td>
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        <tr>
                            <td>Centro comercial</td>
                            <td>HFM8+37V, Cdad. de Guatemala, Guatemala</td>
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Escuela</td>
                            <td>Residencial Casa Bella, Pasaje 7 Norte, Santa Tecla, El Salvador</td>
                            <td>
                                <button class="btn shadow btn-sm btn-primary">Editar</button>
                                <button class="btn shadow btn-sm btn-danger">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
