@extends('layouts.app')
@include('business.layouts.navbar')

@section('content')
    <div class="loading-overlay d-none" id="loadingOverlay">
        <div class="spinner-border text-white" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-sm">
                <h3>Comprobante de Retención</h3>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="fechaDTE">Fecha DTE:</label>
                    <input type="date" class="form-control" id="fechaDTE">
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="horaDTE">Hora:</label>
                    <input type="time" class="form-control" id="horaDTE">
                </div>
            </div>
            <div class="col-sm text-right">
                <br>
                <button type="button" class="btn btn-danger" id="cancelarDTE"><i class="fa fa-square-xmark"></i>
                    Cancelar</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card mb-3 shadow mt-3">
                    <div class="card-header">
                        <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fa-solid fa-file"></i>
                            Datos del Emisor y Receptor</h5>
                    </div>
                    <div class="card-body fw-bold bg-light">
                        <ul class="nav nav-tabs navbar-dark" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="receptor" data-bs-toggle="tab"
                                    data-bs-target="#receptor-pane" type="button" role="tab"
                                    aria-controls="receptor-pane" aria-selected="true">Sujeto de Retención</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="emisor" data-bs-toggle="tab" data-bs-target="#emisor-pane"
                                    type="button" role="tab" aria-controls="emisor-pane"
                                    aria-selected="false">Emisor</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="receptor-pane" role="tabpanel"
                                aria-labelledby="receptor" tabindex="0">
                                <!-- Contenido de receptor -->
                                <div class="row">
                                    <div class="col">
                                        <div class="bg-light mb-3 mt-3">
                                            <div class="card-body ">
                                                <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="receptor" role="tabpanel"
                                                        aria-labelledby="receptor">
                                                        <form>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <button type="button" id="aggCliente"
                                                                        class="btn btn-success" data-bs-toggle="modal"
                                                                        data-bs-target="#clienteExistenteModal">
                                                                        Seleccionar Cliente Existente
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="tipoDoc">Tipo de
                                                                            Documento:</label>
                                                                        <select class="form-select" id="tipoDoc">
                                                                            <option value="00">-- Seleccione --
                                                                            </option>
                                                                            <option value="36">NIT</option>
                                                                            <option value="13">DUI</option>
                                                                            <option value="03">PASAPORTE</option>
                                                                            <option value="02">CARNÉT RESIDENTE
                                                                            </option>
                                                                            <option value="37">OTRO</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="nitContribuyente">Número de
                                                                            Documento</label>
                                                                        <input type="text" class="form-control"
                                                                            id="nitContribuyente"
                                                                            placeholder="Número de documento">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="form-group">
                                                                        <label for="nrcContribuyente">Número de
                                                                            Registro de Contribuyente (NRC):</label>
                                                                        <input type="text" class="form-control"
                                                                            id="nrcContribuyente"
                                                                            placeholder="Número de documento">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="nombre">Nombre, denominación o razón
                                                                    social del contribuyente:</label>
                                                                <input type="text" class="form-control" id="nombre"
                                                                    placeholder="Nombre del receptor">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="nombreComercial">Nombre Comercial:</label>
                                                                <input type="text" class="form-control"
                                                                    id="nombreComercial"
                                                                    placeholder="Nombre Comercial del receptor">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label for="codActividad">Actividad
                                                                    Económica:</label>
                                                                <input type="text" name="codActividad"
                                                                    id="codActividad" class="form-control"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="departamento">Departamento:</label>
                                                                        <select class="form-select"
                                                                            id="departamentoContribuyente">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="municipio">Municipio:</label>
                                                                        <select class="form-select"
                                                                            id="municipioContribuyente">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="form-group">
                                                                <label for="direccionComplemento">
                                                                    Complemento:</label>
                                                                <textarea class="form-control" id="complementoContribuyente" placeholder="Digite el complemento de la dirección"></textarea>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="correo">Correo electrónico:</label>
                                                                        <input type="email" class="form-control"
                                                                            id="correoContribuyente"
                                                                            placeholder="Correo electrónico">
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="telefono">Teléfono:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="telefonoContribuyente"
                                                                            placeholder="Teléfono">
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="emisor-pane" role="tabpanel" aria-labelledby="emisor"
                                tabindex="0">
                                <!-- Contenido de emisor -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="bg-light mb-3 mt-3">
                                            <div class="card-body">
                                                <div class="tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="emisor" role="tabpanel"
                                                        aria-labelledby="emisor-tab">
                                                        <form>
                                                            <div class="form-group">
                                                                <input type="hidden" name="nit" id="nit"
                                                                    value="{{ $datos_empresa['nit'] }}">
                                                                <label for="actividadEconomica">Actividad
                                                                    Económica:</label>
                                                                <input type="text" class="form-control"
                                                                    id="actividadEconomica"
                                                                    value="{{ $datos_empresa['codActividad'] }} - {{ $datos_empresa['descActividad'] }}"
                                                                    readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tipoEstablecimiento">Tipo de
                                                                    Establecimiento:</label>
                                                                <input type="text" class="form-control"
                                                                    id="tipoEstablecimiento" value="Casa Matriz">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="direccion">Establecimiento /
                                                                    Dirección:</label>
                                                                <input type="text" class="form-control" id="direccion"
                                                                    value="{{ $datos_empresa['complemento'] }}">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="correo">Correo electrónico:</label>
                                                                        <input type="email" class="form-control"
                                                                            id="correo"
                                                                            value="{{ $datos_empresa['correo'] }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="telefono">Teléfono:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="telefono"
                                                                            value="{{ $datos_empresa['telefono'] }}">
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

                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                            Documentos </h5>
                    </div>
                    <div class="card-body bg-light ">
                        <div class="d-flex">
                            <div class="dropdown me-2">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Agregar Documento
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#docFisico">Físico</a></li>
                                    <li><a class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#docElectronico">Electrónico</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="container mt-5 table-responsive">
                                    <table class="table table-light border-less">
                                        <thead>
                                            <tr>
                                                <th>Tipo de Generación</th>
                                                <th>Tipo de Documento</th>
                                                <th>Número de Documento</th>
                                                <th>Código Retención</th>
                                                <th>Descripción</th>
                                                <th>Fecha de Generación</th>
                                                <th>Monto Sujeto a Retención</th>
                                                <th>IVA Retenido</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentosRelacionados"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end">
            <button class="btn btn-primary" id="generarDocumento">Generar Documento</button>
        </div>
    </div>

    <!-- Modal #docFisico -->
    <div class="modal fade" id="docFisico" tabindex="-1" aria-labelledby="docFisicoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="docFisicoLabel">Documento Físico</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tipoDocumentoFisico" class="form-label">Tipo de Documento</label>
                        <select name="tipoDocumentoFisico" id="tipoDocumentoFisico" class="form-select">
                            <option value="01">Factura</option>
                            <option value="03">Comprobante de Crédito Fiscal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numeroDocumentoFisico" class="form-label">Número de Documento</label>
                        <input type="text" class="form-control" id="numeroDocumentoFisico">
                    </div>
                    <div class="form-group">
                        <label for="fechaEmisionFisico" class="form-label">Fecha de Documento</label>
                        <input type="date" class="form-control" id="fechaEmisionFisico">
                    </div>
                    <div class="form-group">
                        <label for="descripcionDocumentoFisico" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcionDocumentoFisico">
                    </div>
                    <div class="form-group">
                        <label for="montoRetencionFisico" class="form-label">Monto Sujeto a Retención</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="montoRetencionFisico">
                    </div>
                    <div class="form-group">
                        <label for="tipoRetencionFisico" class="form-label">Tipo de Retención</label>
                        <select name="tipoRetencionFisico" id="tipoRetencionFisico" class="form-select">
                            <option value="22">Retención IVA 1%</option>
                            <option value="C9">Otras Retenciones IVA casos especiales</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="montoIVAFisico" class="form-label">IVA Retenido</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="montoIVAFisico">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="guardarDocFisico">Guardar</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal DTE ELectrónico -->
    <div class="modal modal-lg fade" id="docElectronico" tabindex="-1" aria-labelledby="docElectronicoLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="docElectronicoLabel">Documento Electrónico</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Criterios de Consulta</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="nitBusqueda" class="form-label">NIT del Receptor</label>
                                        <input type="text" class="form-control" id="nitBusqueda" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="tipoDocumentoElectronico" class="form-label">Tipo de Documento</label>
                                        <select name="tipoDocumentoElectronico" id="tipoDocumentoElectronico"
                                            class="form-select">
                                            <option value="01">Factura</option>
                                            <option value="03">Comprobante de Crédito Fiscal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="desdeBusqueda" class="form-label">Fecha Emitido Desde:</label>
                                        <input type="datetime-local" name="" id="desdeBusqueda"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="hastaBusqueda" class="form-label">Fecha Emitido Hasta:</label>
                                        <input type="datetime-local" name="" id="hastaBusqueda"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary" id="buscarDTE">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Resultados de Búsqueda -->
                    <div class="card mt-2">
                        <div class="card-header">
                            <h5 class="card-title">Resultados</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <th>Fecha de Emisión</th>
                                        <th>Código de Generación</th>
                                        <th>Monto</th>
                                        <th>Seleccionar</th>
                                    </thead>
                                    <tbody id="resultadosDte">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cliente Existente -->
    <div class="modal modal-xl fade" id="clienteExistenteModal" tabindex="-1"
        aria-labelledby="clienteExistenteModalLabel" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="clienteExistenteModalLabel">Seleccionar Cliente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table id="tablaClientes"
                                    class="table small table-bordered table-striped table-hover w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Identificación</th>
                                            <th>Nombre</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="cerrarModalCliente"
                        data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/comprobante_retencion.js')
@endsection
