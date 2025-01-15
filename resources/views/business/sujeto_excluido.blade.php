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
                <h3>Factura de Sujeto Excluido</h3>
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

        <div class="col-sm-12">
            <div class="card mb-3 shadow mt-3">
                {{-- Emisor y Receptor --}}
                <div class="card-header">
                    <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fa-solid fa-file"></i>
                        Datos del Emisor y Receptor</h5>
                </div>
                <div class="card-body fw-bold bg-light">
                    <ul class="nav nav-tabs navbar-dark" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="receptor" data-bs-toggle="tab"
                                data-bs-target="#receptor-pane" type="button" role="tab" aria-controls="receptor-pane"
                                aria-selected="true">Receptor</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emisor" data-bs-toggle="tab" data-bs-target="#emisor-pane"
                                type="button" role="tab" aria-controls="emisor-pane"
                                aria-selected="false">Emisor</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="receptor-pane" role="tabpanel" aria-labelledby="receptor"
                            tabindex="0">
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
                                                            <div class="col-sm-6">
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
                                                                        </option>https://admin.factura.gob.sv/dashboard
                                                                        <option value="37">OTRO</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="nitContribuyente">Número de
                                                                        Documento:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="nitContribuyente"
                                                                        placeholder="Número de documento">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="nombreComercial">Nombre, denominación o razón
                                                                social del contribuyente:</label>
                                                            <input type="text" class="form-control"
                                                                id="nombreContribuyente"
                                                                placeholder="Nombre completo del receptor">
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
                                                                        id="telefonoContribuyente" placeholder="Teléfono">
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

                {{-- Detalle de la factura --}}
                <div class="card-header">
                    <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                        Detalle de factura</h5>
                </div>
                <div class="card-body bg-light ">
                    <h4>Datos de la factura</h4>
                    <!-- Button trigger modal Agregar Ítem-->
                    <div class="d-flex">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#aggitem">
                            Agregar Detalle
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="container mt-5">
                                <table class="table table-light border-less">
                                    <thead>
                                        <tr>
                                            <th>Unidad de Medida</th>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Descuento por ítem</th>
                                            <th>Ventas</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items">
                                    </tbody>
                                    <tbody id="tributos">
                                    </tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end fw-bold">Monto Total de la operación</td>
                                        <td id="montoTotalOperacion"></td>
                                    </tr>
                                    {{-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="checkIvaRete1">
                                                <label class="form-check-label" for="checkIvaRete1">
                                                    ¿Retener IVA?
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">Retención IVA (1%)</td>
                                        <td id="reteIVA"></td>
                                    </tr> --}}
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end fw-bold">Retención Renta</td>
                                        <td id="reteRenta"></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end fw-bold">Descuento a Operación</td>
                                        <td id="descuentosTotal"></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end fw-bold">Total Pagar</td>
                                        <td id="totalPagar"></td>
                                    </tr>
                                </table>
                                <!-- Button trigger modal Agregar Descuento -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#aggDescuentoModal">
                                    Agregar Descuento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Observaciones --}}
                <div class="card-header">
                    <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                        Observaciones</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea class="form-control" id="observacionesDoc" placeholder="Observaciones al documento"></textarea>
                    </div>
                </div>
                {{-- Condiciones de la operación --}}
                <div class="card-header">
                    <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                        Condición de la Operación</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="condicionOperacion" class="form-label">Condición de la Operación:</label>
                        <select id="condicionOperacion" class="form-select">
                            <option>Contado</option>
                            <option>Crédito</option>
                            <option>Otro</option>
                        </select>
                    </div>
                </div>
                {{-- Forma de Pago --}}
                <div class="card-header">
                    <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-dollar-sign"></i>
                        Forma de Pago</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="fw-bold col-3">
                                <label for="condicionOperacion" class="form-label">Forma de Pago</label>
                                <select id="condicionOperacion" class="form-select">
                                    <option>Billetes y Monedas</option>
                                    <option>Tarjeta Débito</option>
                                    <option>Tarjeta Crédito</option>
                                    <option>Cheque</option>
                                    <option>Transferencia Depósito Bancario</option>
                                    <option>Vales o Cupones</option>
                                    <option>Dinero Electrónico</option>
                                    <option>Monedero Electrónico</option>
                                    <option>Certificado o Tarjeta de Regalo</option>
                                    <option>Bitcoin</option>
                                    <option>Bitcoin</option>
                                    <option>Otras Criptomonedas</option>
                                    <option>Cuentas por Pagar del Receptor</option>
                                    <option>Giro Bancario</option>
                                    <option>Otros (se debe indicar el medio de pago)</option>
                                </select>
                            </div>
                            <div class="fw-bold col-3">
                                <label for="monto" class="form-label">Monto:</label>
                                <input type="number" class="form-control" id="monto" value="">
                            </div>
                            <div class="fw-bold col-3">
                                <label for="numDoc" class="form-label">N° Doc.</label>
                                <input type="number" class="form-control" id="numDoc">
                            </div>
                            {{-- <div class="col-3">
                                <label for="formPago" class="form-label"></label><br>
                                <button type="button" class="btn btn-primary"><i
                                        class="fas fa-money-bill-alt"></i> Agregar Forma de Pago</button>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Forma de Pago</th>
                                    <th>Monto</th>
                                    <th>Número de Documento</th>
                                    <th>Plazo</th>
                                    <th>Período</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Billetes y Monedas</td>
                                    <td>$100.00</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><button class="btn btn-danger">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> --}}
                </div>
            </div>
            <div class="text-end">
                <button class="btn btn-primary" id="generarDocumento">Generar Documento</button>
            </div>
        </div>
    </div>

    <!-- Modal Producto o Servicio  -->
    <div class="modal modal-xl fade" id="aggitem" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aggitem">Ítem DTE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="col-12 p-3">
                        <div class="card">
                            <div class="card-header bg-light">
                                Adición de detalle DTE
                            </div>
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-1">
                                        <label for="tipoItem" class="form-label">Tipo:</label>
                                    </div>
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <select id="tipoItem" class="form-select">
                                                <option value="1" selected>1 - Bien</option>
                                                <option value="2">2 - Servicio</option>
                                                <option value="3">3 - Bien y Servicio</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="cantidad" class="form-label">Cantidad:</label>
                                            <input type="number" class="form-control" id="cantidad" value=""
                                                step="0.000001">
                                            <small class="form-text text-danger">Requerido.</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="unidad" class="form-label">Unidad:</label>
                                            <select id="unidad" class="form-select">
                                                <option value="">-- Seleccionar --</option>
                                                @foreach ($catalogos['CAT_014'] as $uni_medida)
                                                    <option value="{{ $uni_medida->codigo }}">
                                                        {{ $uni_medida->valores }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="producto" class="form-label">Producto:</label>
                                            <input type="text" class="form-control" id="producto" value="">
                                            <small class="form-text text-danger">Requerido.</small>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="precio" class="form-label">Precio:</label>
                                            <input type="number" class="form-control" id="precio" step="0.00001">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-3"></div>
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="descuento" class="form-label">Descuento</label>
                                            <input type="text" class="form-control" id="descuento"
                                                placeholder="Descuento">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="total" class="form-label">Total:</label>
                                            <input type="text" class="form-control" id="total" value=""
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light d-flex justify-content-start">
                    <button type="button" class="btn btn-primary" id="agregar_item">Agregar
                        Ítem</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Descuento -->
    <div class="modal modal-xl fade" id="aggDescuentoModal" tabindex="-1" aria-labelledby="aggdescuento"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aggdescuento">Descuentos Generales al
                        Resumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-muted text-dark font-weight-normal">
                                Descuentos al total del documento</h5>
                        </div>
                        <div class="card-body bg-light">
                            <form action="#" method="get">
                                <div class="form-group">
                                    <label for="telefono">Ventas:</label>
                                    <input type="text" class="form-control" id="descVentasGravadas" required
                                        placeholder="Número de ventas gravadas" value="0">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" id="guardarDescuento" class="btn btn-primary">Guardar</button>
                    </div>
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
                            <div class="col-12">
                                <table id="tablaClientes" class="table table-bordered table-striped table-hover w-100">
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
    @vite('resources/js/sujeto_excluido.js')
@endsection
