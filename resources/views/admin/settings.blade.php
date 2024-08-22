@extends('layouts.app')
@include('admin.layouts.navbar')
@section('content')
    <div class="container col-md-10 mt-5" id="configuracion">
        <form>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light shadow card-body h-100">
                        <h2>Conexión con Ministerio de Hacienda</h2>
                        <hr>
                        <div class="form-group mb-3">
                            <label for="urlAutenticacion">URL de Autenticación</label>
                            <input type="url" class="form-control" id="urlAutenticacion" placeholder="https://...">
                        </div>
                        <div class="form-group mb-3">
                            <label for="urlRecepcion">URL de Recepción</label>
                            <input type="url" class="form-control" id="urlRecepcion" placeholder="https://...">
                        </div>
                        <div class="form-group mb-3">
                            <label for="urlConsultas">URL para Consultas</label>
                            <input type="url" class="form-control" id="urlConsultas" placeholder="https://...">
                        </div>
                        <div class="form-group mb-3">
                            <label for="urlContingencia">URL para Contingencia</label>
                            <input type="url" class="form-control" id="urlContingencia" placeholder="https://...">
                        </div>
                        <div class="form-group mb-3">
                            <label for="urlAnulacion">URL para Anulación</label>
                            <input type="url" class="form-control" id="urlAnulacion" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow bg-light card-body h-100">
                        <h2>Conexión con Servicios Internos</h2>
                        <hr>
                        <div class="form-group mb-3">
                            <label for="api">API</label>
                            <input type="text" class="form-control" id="api">
                        </div>
                        <div class="form-group mb-3">
                            <label for="firmador">Firmador</label>
                            <input type="text" class="form-control" id="firmador">
                        </div>
                        <div class="form-group mb-3">
                            <label for="generadorPdfs">Generador de PDFs</label>
                            <input type="text" class="form-control" id="generadorPdfs">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center p-3">
                    <button type="submit" class="btn btn-primary btn-lg"> <i class="fa fa-gear"></i> Guardar
                        Configuración</button>
                </div>
            </div>
        </form>
    </div>
@endsection
