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
                <h3>Nota de Crédito</h3>
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
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="nitContribuyente">NIT/DUI</label>
                                                                        <input type="text" class="form-control"
                                                                            id="nitContribuyente"
                                                                            placeholder="Número de documento">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
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
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                            Documentos Relacionados</h5>
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
                                <div class="container mt-5">
                                    <table class="table table-light border-less">
                                        <thead>
                                            <tr>
                                                <th>Tipo de Documento</th>
                                                <th>Tipo de Generación</th>
                                                <th>Código de Generación / Correlativo</th>
                                                <th>Fecha de Emisión</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentosRelacionados"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-header">
                                <h5 class="card-title text-muted text-dark font-weight-normal"><i class="fas fa-info"></i>
                                    Detalle de la Nota de Crédito</h5>
                            </div>
                            <div class="card-body bg-light ">
                                <h4>Cuerpo del Documento</h4>
                                <!-- Button trigger modal Agregar Ítem-->
                                <div class="d-flex">
                                    <div class="dropdown me-2">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Agregar Detalle
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#aggitem"
                                                    href="#aggitem">Producto o Servicio</a></li>
                                            <li><a class="dropdown-item" href="#">Monto No Afecto</a></li>
                                            <li><a class="dropdown-item">Impuestos/Tasas con afectación al IVA</a></li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#prodExistenteModal">Añadir Producto Existente</button>
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
                                                        <th>V. Gravada</th>
                                                        <th>V. Exenta</th>
                                                        <th>V. No Sujeta</th>
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
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="checkIvaRete1">
                                                            <label class="form-check-label" for="checkIvaRete1">
                                                                ¿Retener IVA?
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold">Retención IVA (1%)</td>
                                                    <td id="reteIVA"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="checkIvaPerci1">
                                                            <label class="form-check-label" for="checkIvaPerci1">
                                                                ¿Percibir IVA?
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold">Percepción IVA (1%)</td>
                                                    <td id="perciIVA"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="checkReteRenta">
                                                            <label class="form-check-label" for="checkReteRenta">
                                                                ¿Retener Renta?
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold">Retención Renta</td>
                                                    <td id="reteRenta"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
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
                            <div class="card-header">
                                <h5 class="card-title text-muted text-dark font-weight-normal"><i
                                        class="fa-solid fa-arrow-down"></i> Otra información del DTE</h5>
                            </div>
                            <div class="card-body bg-light">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="documentos" data-bs-toggle="tab"
                                    data-bs-target="#documentos-pane" type="button" role="tab"
                                    aria-controls="documentos-pane" aria-selected="true">Documentos
                                    Relacionados</button>
                            </li> --}}
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="terceros" data-bs-toggle="tab"
                                            data-bs-target="#terceros-pane" type="button" role="tab"
                                            aria-controls="terceros-pane" aria-selected="false">Venta a cuenta de
                                            terceros</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="otrosdoc1" data-bs-toggle="tab"
                                            data-bs-target="#otrosdoc-pane" type="button" role="tab"
                                            aria-controls="otrosdoc-pane" aria-selected="false">Otros Documentos
                                            Asociados</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabOtraInfo">
                                    <div class="tab-pane fade" id="documentos-pane" role="tabpanel"
                                        aria-labelledby="documentos-pane" tabindex="0">
                                        <!-- Contenido de Documentos -->
                                        <div class="mb-3 mt-3">
                                            <!-- Button trigger modal Agregar DTE-->
                                            <div class="dropdown">
                                                <button class="btn btn-primary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Agregar DTE
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#aggdteFis" href="#">Físico</a></li>
                                                    <li><a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#aggdteDig" href="#">Digital</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="terceros-pane" role="tabpanel"
                                        aria-labelledby="terceros-pane" tabindex="0">
                                        <!-- Contenido de Ventas a Terceros -->
                                        <div class="card-body bg-light">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="mb-3" for="nitVentaTerceros">NIT:</label>
                                                        <input type="email" class="form-control" id="nitVentaTerceros"
                                                            placeholder="NIT del contribuyente">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label class="mb-3" for="nombreVentaTerceros">Nombre:</label>
                                                        <input type="text" class="form-control"
                                                            id="nombreVentaTerceros"
                                                            placeholder="Nombre del contribuyente">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Contenido de Otros Documentos Asociados -->
                                    <div class="tab-pane fade" id="otrosdoc-pane" role="tabpanel"
                                        aria-labelledby="otrosdoc-pane" tabindex="0">
                                        <div class="mb-3 mt-3">
                                            <!-- Button trigger modal Otros Doc Asociados-->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#otrosdoc">
                                                Agregar Documentos
                                            </button>
                                            <hr>
                                            <h5>Otros Documentos Asociados</h5>
                                            <hr>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Código de Documento</th>
                                                            <th>Descripción del Documento</th>
                                                            <th>Detalle del Documento</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Emisor</td>
                                                            <td>0001</td>
                                                            <td>Factura de Venta</td>
                                                            <td><button class="btn btn-danger">X</button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal"><i
                                            class="fas fa-info"></i>
                                        Observaciones</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <textarea class="form-control" id="observacionesDoc" placeholder="Observaciones al documento"></textarea>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal"><i
                                            class="fas fa-info"></i>
                                        Condición de la Operación</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="condicionOperacion" class="form-label">Condición de la
                                            Operación:</label>
                                            <select id="condicionOperacion" class="form-select">
                                                <option value="1">Contado</option>
                                                <option value="2">Crédito</option>
                                                <option value="3">Otro</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal"><i
                                            class="fas fa-dollar-sign"></i>
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
                                                <input type="number" class="form-control" id="monto"
                                                    value="">
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
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal"><i
                                            class="fas fa-gift"></i>
                                        Datos adicionales entrega</h5>
                                </div>
                                <div class="card-body fw-bold bg-light">
                                    <div class="form-group row">
                                        <div class="col-md-2">
                                            <label class="col-form-label" for="docuEntrega">No. Documento</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" placeholder="Responsable de emitir el documento"
                                                class="form-control" id="docuEntrega">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="col-form-label" for="nombEntrega">Nombre</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" placeholder="Responsable de emitir el documento"
                                                class="form-control" id="nombEntrega">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-2">
                                            <label class="col-form-label" for="docuRecibe">No. Documento</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" placeholder="Responsable de recibir el documento"
                                                class="form-control" id="docuRecibe">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="col-form-label" for="nombRecibe">Nombre</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" placeholder="Responsable de recibir el documento"
                                                class="form-control" id="nombRecibe">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-primary" id="generarDocumento">Generar Documento</button>
                                </div>
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
                            <h5 class="modal-title">Ítem DTE</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                                            <div class="col-md-2">
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
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="cantidad" class="form-label">Cantidad:</label>
                                                    <input type="number" class="form-control" id="cantidad"
                                                        value="" step="0.000001">
                                                    <small class="form-text text-danger">Requerido.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
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
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="producto" class="form-label">Producto:</label>
                                                    <input type="text" class="form-control" id="producto"
                                                        value="">
                                                    <small class="form-text text-danger">Requerido.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="tipoVenta" class="form-label">Tipo
                                                        Venta:</label>
                                                    <select id="tipoVenta" class="form-select">
                                                        <option value="gravada" selected>Gravada</option>
                                                        <option value="exenta">Exenta</option>
                                                        <option value="noSujeta">No Sujeta</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="precio" class="form-label">Precio (sin IVA):</label>
                                                    <input type="number" class="form-control" id="precio"
                                                        step="0.00001">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><b>Información de los
                                                    tributos:</b></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <p class="form-label">Tributos que aplican a este producto:</p>
                                                        @foreach ($tributos as $tributo)
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $tributo->codigo }}"
                                                                    id="{{ $tributo->codigo }}" name="tributos[]"
                                                                    data-valor="{{ $tributo->valor }}"
                                                                    data-porcentaje="{{ $tributo->es_porcentaje }}"
                                                                    @if ($tributo->codigo == '20') checked disabled @endif>
                                                                <label class="form-check-label"
                                                                    for="{{ $tributo->codigo }}">
                                                                    {{ $tributo->descripcion }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="tributosAplicados">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="mb-3">
                                                    <label for="documentoRelacionado" class="form-label">Documento Relacionado</label>
                                                    <select id="documentoRelacionado" class="form-select">
                                                        <option value="">-- Seleccionar --</option>
                                                    </select>
                                                </div>
                                            </div>
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
                                                    <input type="text" class="form-control" id="total"
                                                        value="" readonly>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                                            <label for="telefono">Ventas
                                                Gravadas:</label>
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

            <!-- Modal DTE Fisico -->
            <div class="modal modal-lg fade" id="aggdteFis" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="aggdteFis">Agregar DTE Físico</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal">
                                        Relación Doc. Físico</h5>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="get">
                                        <div class="form-group">
                                            <label for="tipdoc">Tipo de Documento:</label>
                                            <select class="form-select" id="tipdoc">
                                                <option value="remision">Nota de Remisón
                                                </option>
                                                <option value="comliquidacion">Comprobante
                                                    de Liquidación</option>
                                                <option value="docliquidacion">Documento
                                                    Contable de Liquidación</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="telefono">Número de
                                                Documento:</label>
                                            <input type="text" class="form-control" id="telefonoDoc" required
                                                placeholder="Número Doc.">
                                        </div>
                                        <label for="fechaDTE">Fecha de Generación:</label>
                                        <input type="date" class="form-control" id="fechaDTE" required>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary">Guardar DTE</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal DTE Digital -->
            <div class="modal modal-lg fade" id="aggdteDig" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="aggdteDig">Agregar DTE Digital</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-muted text-dark font-weight-normal">
                                        Relación Doc. Digital</h5>
                                </div>
                                <div class="card-body">
                                    <form action="#" method="get">
                                        <div class="form-group">
                                            <label for="tipdoc">Tipo de Documento:</label>
                                            <select class="form-select" id="tipdoc">
                                                <option value="remision">Nota de Remisón
                                                </option>
                                                <option value="comliquidacion">Comprobante
                                                    de Liquidación</option>
                                                <option value="docliquidacion">Documento
                                                    Contable de Liquidación</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="telefono">Número de
                                                Documento:</label>
                                            <input type="text" class="form-control" id="telefonoDoc" required
                                                placeholder="Número Doc.">
                                        </div>
                                        <label for="fechaDTE">Fecha de Generación:</label>
                                        <input type="date" class="form-control" id="fechaDTE" required>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary">Guardar DTE</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Otros Doc Asociados -->
            <div class="modal modal-lg fade" id="otrosdoc" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="otrosdoc">Información del Documento
                                Asociado
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form class="col-12 p-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title text-muted text-dark font-weight-normal">
                                            Datos generales del documento</h5>
                                    </div>
                                    <div class="card-body shadow bg-light">
                                        <div class="form-group">
                                            <label for="otrosdoc">Documento
                                                Asociado:</label>
                                            <select class="form-control" id="otrosdoc">
                                                <option value="emisor2">Emisor</option>
                                                <option value="receptor2">Receptor</option>
                                                <option value="medico">Médico</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="telefono">Identificación del
                                                Documento:</label>
                                            <input type="text" class="form-control" id="otrosdoc" required
                                                placeholder="Identificación del nombre del documento asociado">
                                        </div>
                                        <div class="form-group">
                                            <label for="nombreComercial">Descripción del
                                                documento:</label>
                                            <input type="text" class="form-control" id="otrosdoc"
                                                placeholder="Descripción de datos importantes del documento asociado">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Agregar</button>
                                            <button type="reset" class="btn btn-danger">Cancelar</button>
                                        </div>
                                        <!-- Parte selección médico
                                                                            <div class="form-group">
                                                                                <label for="nombreComercial">Tipo de Servicio:</label>
                                                                                <select class="form-control" id="otrosdoc">
                                                                                    <option value="cirugia">Cirugía</option>
                                                                                    <option value="receptor2">Operación</option>
                                                                                    <option value="medico">Tratamiento Médico</option>
                                                                                    <option value="medico">Cirugía Instituto Salvadoreño de Bienestar Magisterial</option>
                                                                                    <option value="medico">Cirugía Instituto Salvadoreño de Bienestar Magisterial</option>
                                                                                    <option value="medico">Tratamiento Médico Instituo Salvadoreño de Bienestar Magisterial</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="nombre">Nombre:</label>
                                                                                <input type="text" class="form-control" id="otrosdoc" required placeholder="Nombre del Médico">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div class="row">
                                                                                    <div class="col-sm-6">
                                                                                        <label for="tiponit">Tipo de Documento:</label>
                                                                                        <select class="form-control" id="tiponit">
                                                                                            <option value="NIT">NIT</option>
                                                                                            <option value="otro">Otro</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-sm-6">
                                                                                        <label for="nit">NIT:</label>
                                                                                        <input type="text" class="form-control" id="nit" required placeholder="Número documento de identificación">
                                                                                    </div>
                                                                                </div>
                                                                            </div> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar DTE</button>
                </div>
            </div>

            <!-- Modal Producto Existente -->
            <div class="modal modal-xl fade" id="prodExistenteModal" tabindex="-1"
                aria-labelledby="prodExistenteModalLabel" aria-hidden="true" data-bs-backdrop="static"
                data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="prodExistenteModalLabel">Seleccionar Producto</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <table id="tablaProds"
                                            class="table table-bordered table-striped table-hover w-100">
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
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cantidadExistente" class="form-label">Cantidad:</label>
                                                    <input type="number" class="form-control" id="cantidadExistente"
                                                        value="" step="0.000001">
                                                    <small class="form-text text-danger">Requerido.</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="tipoVentaExistente" class="form-label">Tipo
                                                        Venta:</label>
                                                    <select id="tipoVentaExistente" class="form-select">
                                                        <option value="gravada" selected>Gravada</option>
                                                        <option value="exenta">Exenta</option>
                                                        <option value="noSujeta">No Sujeta</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="descuentoExistente" class="form-label">Descuento</label>
                                                    <input type="number" class="form-control" id="descuentoExistente"
                                                        placeholder="Descuento">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="totalExistente" class="form-label">Total</label>
                                                    <input type="number" class="form-control" id="totalExistente"
                                                        placeholder="Total" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="documentoRelacionadoExistente" class="form-label">Documento Relacionado</label>
                                                    <select id="documentoRelacionadoExistente" class="form-select">
                                                        <option value="">-- Seleccionar --</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-success" id="btnAgregarProd">Añadir
                                                    Producto</button>
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
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <table id="tablaClientes"
                                            class="table table-bordered table-striped table-hover w-100">
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

            <!-- Modal #docFisico -->
            <div class="modal fade" id="docFisico" tabindex="-1" aria-labelledby="docFisicoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="docFisicoLabel">Documento Físico</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="tipoDocumentoFisico" class="form-label">Tipo de Documento</label>
                                <select name="tipoDocumentoFisico" id="tipoDocumentoFisico" class="form-select">
                                    <option value="03">Comprobante de Crédito Fiscal</option>
                                    <option value="07">Comprobante de Retención</option>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="guardarDocFisico">Guardar</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal DTE ELectrónico -->
            <div class="modal modal-lg fade" id="docElectronico" tabindex="-1" aria-labelledby="docElectronicoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="docElectronicoLabel">Documento Electrónico</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                                                <select name="tipoDocumentoElectronico" id="tipoDocumentoElectronico" class="form-select">
                                                    <option value="03">Comprobante de Crédito Fiscal</option>
                                                    <option value="07">Comprobante de Retención</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="desdeBusqueda" class="form-label">Fecha Emitido Desde:</label>
                                                <input type="datetime-local" name="" id="desdeBusqueda" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="hastaBusqueda" class="form-label">Fecha Emitido Hasta:</label>
                                                <input type="datetime-local" name="" id="hastaBusqueda" class="form-control">
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            @vite('resources/js/nota_credito.js')
        @endsection
