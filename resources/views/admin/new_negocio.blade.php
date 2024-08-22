@extends('layouts.app')

@include('admin.layouts.navbar')
@section('content')
    <form method="POST" action="{{route("admin.negocios.store")}}" enctype="multipart/form-data">
        @csrf
        <div class="container-fluid">
            <h1 class="text-center">Registrar Negocio</h1>
            <div class="row">
                <div class="col-md-6 mt-2">
                    <div class="card shadow bg-light card-body">
                        <h2>Datos del Negocio</h2>
                        <div class="form-group mb-3">
                            <label for="nit">NIT:</label>
                            <input type="text" class="form-control" id="nit" name="nit" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nrc">NRC:</label>
                            <input type="text" class="form-control" id="nrc" name="nrc" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombre">Razón Social:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombreComercial">Nombre Comercial:</label>
                            <input type="text" class="form-control" id="nombreComercial" name="nombreComercial" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="actividad_economica">Actividad
                                Económica:</label>
                            <input type="text" class="form-control" id="actividad_economica" name="actividad_economica" autocomplete="off" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tipoEstablecimiento">Tipo de
                                Establecimiento:</label>
                            <select name="tipoEstablecimiento" id="tipoEstablecimiento" class="form-select" name="tipoEstablecimiento" required>
                                @foreach ($tipo_establecimiento as $tipo)
                                    <option value="{{ $tipo->codigo }}">{{ $tipo->valores }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3 row">
                            <div class="col-md-6">
                                <label for="codEstable">Código Establecimiento:</label>
                                <input type="text" class="form-control" id="codEstable" name="codEstable" value="0001" required>
                            </div>
                            <div class="col-md-6">
                                <label for="codEstableMH">Código Establecimiento (Ministerio de Hacienda):</label>
                                <input type="text" class="form-control" id="codEstableMH" name="codEstableMH">
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <div class="col-md-6">
                                <label for="codPuntoVenta">Código Punto de Venta:</label>
                                <input type="text" class="form-control" id="codEstable" name="codPuntoVenta" value="01" required>
                            </div>
                            <div class="col-md-6">
                                <label for="codPuntoVentaMH">Código Punto de Venta (Ministerio de Hacienda):</label>
                                <input type="text" class="form-control" id="codPuntoVentaMH" name="codPuntoVentaMH">
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <div class="col-md-6">
                                <label for="departamentoSelect" class="form-label">Departamento</label>
                                <select id="departamentoSelect" class="form-select" name="departamento" required>
                                    <option value="">Seleccione un departamento</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="municipioSelect" class="form-label">Municipio</label>
                                <select id="municipioSelect" class="form-select" name="municipio" required>
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="direccion">Dirección:</label>
                            <input type="text" class="form-control" id="direccion" name="complemento" required>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="correo">Correo electrónico:</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="telefono">Teléfono:</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-3">
                                    <label for="logo">Logo de la Empresa:</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <div class="card shadow bg-light card-body">
                        <h2>Configuración de Facturación Electrónica:</h2>
                        <div class="form-group mb-3">
                            <label for="plan_id">Plan Contratado:</label>
                            <select class="form-select" id="plan_id" name="plan_id">
                                @foreach ($planes as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="dtes">DTEs Habilitados:</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchFE" name="dtes[]" value="01">
                                <label class="form-check-label" for="switchFE">Factura Electrónica</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchCCF" name="dtes[]" value="03">
                                <label class="form-check-label" for="switchCCF">Comprobante de Crédito Fiscal</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchNC" name="dtes[]" value="05">
                                <label class="form-check-label" for="switchNC">Nota de Crédito</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchCR" name="dtes[]" value="07">
                                <label class="form-check-label" for="switchCR">Comprobante de Retención</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchFEX" name="dtes[]" value="11">
                                <label class="form-check-label" for="switchFEX">Factura de Exportación</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switchFSE" name="dtes[]" value="14">
                                <label class="form-check-label" for="switchFSE">Factura de Sujeto Excluido</label>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow bg-light card-body mt-3">
                        <h2>Datos del Responsable:</h2>
                        <div class="form-group mb-3 row">
                            <div class="col-md-6">
                                <label for="dui">Documento de Identidad:</label>
                                <input type="text" class="form-control" id="dui" name="dui" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono_responsable">Teléfono:</label>
                                <input type="number" class="form-control" id="telefono_responsable" name="telefono_responsable" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nombre_responsable">Nombre según Documento:</label>
                            <input type="text" class="form-control" id="nombre_responsable" name="nombre_responsable" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="correo_responsable">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="correo_responsable" name="correo_responsable" required>
                        </div>
                    </div>
                    <div class="card shadow bg-light card-body mt-3">
                        <h2>Datos de acceso a API y Certificado:</h2>
                        <div class="form-group mb-3">
                            <label for="crt_file">Certificado de Firma Electrónica</label>
                            <input type="file" class="form-control" id="crt_file" name="crt_file" accept="*.crt" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="certificate_password">Clave <b>PRIVADA</b> del Certificado</label>
                            <input type="password" class="form-control" id="certificate_password" name="certificate_password" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="api_password">Clave de <b>USUARIO API</b> registrada en Hacienda</label>
                            <input type="password" class="form-control" id="api_password" name="api_password" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="d-flex justify-content-center">
                    <button type="submit" class="mx-3 btn-lg btn btn-primary">Guardar Negocio</button>
                </div>
            </div>
        </div>
    </form>
    @vite('resources/js/admin.js')
@endsection
