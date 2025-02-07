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
                <h3>Factura de Exportación</h3>
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
                                    aria-controls="receptor-pane" aria-selected="true">Receptor</button>
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
                                                            <div class="row mb-3">
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
                                                                            </option>
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
                                                            <div class="form-group mb-3">
                                                                <label for="nombre">Nombre, denominación o razón
                                                                    social del contribuyente:</label>
                                                                <input type="text" class="form-control" id="nombre"
                                                                    placeholder="Nombre del receptor">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label for="codActividad">Actividad
                                                                    Económica:</label>
                                                                <input type="text" name="codActividad"
                                                                    id="codActividad" class="form-control"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="departamento">Tipo Persona:</label>
                                                                        <select class="form-select" id="tipoPersona">
                                                                            <option value="01">Jurídica</option>
                                                                            <option value="02">Natural</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="form-group">
                                                                        <label for="codActividad">País:</label>
                                                                        <input type="text" name="codPais"
                                                                            id="codPais" class="form-control"
                                                                            autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-3">
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
                            Detalle de la Factura de Exportación</h5>
                    </div>
                    <div class="card-body bg-light ">
                        <div class="form-group mb-3">
                            <label for="tipoItemExpor">Tipo de Item a Exportar:</label>
                            <select class="form-select" id="tipoItemExpor">
                                <option value="1">Bienes</option>
                                <option value="2">Servicios</option>
                                <option value="3">Bienes y Servicios</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="recinto">Recinto Fiscal</label>
                            <select class="form-select" id="recinto">
                                @foreach ($catalogos['CAT_027'] as $recinto)
                                    <option value="{{ $recinto->codigo }}">{{ $recinto->codigo }} -
                                        {{ $recinto->valores }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="recinto">Régimen de Exportación</label>
                            <select class="form-select" id="regimen">
                                @foreach ($catalogos['CAT_028'] as $regimen)
                                    <option value="{{ $regimen->codigo }}">{{ $regimen->codigo }} -
                                        {{ $regimen->valores }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="recinto">INCOTERMS</label>
                            <select class="form-select" id="incoterms">
                                @foreach ($catalogos['CAT_031'] as $regimen)
                                    <option value="{{ $regimen->codigo }}">{{ $regimen->codigo }} -
                                        {{ $regimen->valores }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-header">
                        <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                            Detalle de la Factura de Exportación</h5>
                    </div>
                    <div class="card-body bg-light ">
                        <h4>Cuerpo del Documento</h4>
                        <!-- Button trigger modal Agregar Ítem-->
                        <div class="d-flex">
                            <button class="btn btn-primary me-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#aggitem">
                                Agregar Producto
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#prodExistenteModal">Añadir Producto Existente</button>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="container mt-5">
                                    <table class="table table-light">
                                        <thead>
                                            <tr>
                                                <th>Unidad de Medida</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Descuento por ítem</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items">
                                        </tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">Sub Total</td>
                                            <td id="subTotalGeneral"></td>
                                        </tr>
                                        <tbody id="tributos">
                                        </tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">Monto Total de la operación</td>
                                            <td id="montoTotalOperacion"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">Seguro</td>
                                            <td>
                                                <input type="number" class="form-control" id="seguro" value="0"
                                                    step="0.01">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">Flete</td>
                                            <td>
                                                <input type="number" class="form-control" id="flete" value="0"
                                                    step="0.01">
                                            </td>
                                        </tr>
                                        <tr class="table-active">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">Total Pagar</td>
                                            <td id="totalPagar"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                            Observaciones</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <textarea class="form-control" id="observacionesDoc" placeholder="Observaciones al documento"></textarea>
                        </div>
                    </div>
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
                                Datos del Producto / Servicio
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
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="cantidad" class="form-label">Cantidad:</label>
                                            <input type="number" class="form-control" id="cantidad" value=""
                                                step="0.000001">
                                            <small class="form-text text-danger">Requerido.</small>
                                        </div>
                                    </div>
                                    <div class="col-2">
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
                                <div class="mb-3">
                                    <label class="form-label"><b>Información de los
                                            tributos:</b></label>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <p class="form-label">Tributos que aplican a este producto:</p>
                                                <p class="fw-bold">Impuesto al Valor Agregado (exportaciones): 0%</p>
                                            </div>
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
    <!-- Modal Producto Existente -->
    <div class="modal modal-xl fade" id="prodExistenteModal" tabindex="-1" aria-labelledby="prodExistenteModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="prodExistenteModalLabel">Seleccionar Producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <table id="tablaProds" class="table table-bordered table-striped table-hover w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Precio</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <hr>
                            <div class="col-12 d-none" id="prodSeleccionado">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="h4">Producto: <span id="prodDesc"></span></p>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="cantidadExistente" class="form-label">Cantidad:</label>
                                            <input type="number" class="form-control" id="cantidadExistente"
                                                value="" step="0.000001">
                                            <small class="form-text text-danger">Requerido.</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="descuentoExistente" class="form-label">Descuento</label>
                                            <input type="number" class="form-control" id="descuentoExistente"
                                                placeholder="Descuento">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="totalExistente" class="form-label">Total</label>
                                            <input type="number" class="form-control" id="totalExistente"
                                                placeholder="Total" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-success" id="btnAgregarProd">Añadir Producto</button>
                                    </div>
                                </div>
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
    @vite('resources/js/exportacion.js')
@endsection
