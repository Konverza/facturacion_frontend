@extends('layouts.app')
@include('admin.layouts.navbar')
@section('content')
    <div class="container col-md-6 mt-5">
        <div class="card shadow bg-light card-body">
            <h1>Mejorar Plan</h2>
                <form>
                    <div class="form-group mb-3">
                        <label for="negocioMejora">Negocio:</label>
                        <input type="text" class="form-control" id="negocioMejora" value="Industrias SI SA de CV" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="planContratadoMejora">Plan Contratado:</label>
                        <input type="text" class="form-control" id="planContratadoMejora" value="Plan Cloud #1" readonly>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow h-100">
                                    <div class="card-body">
                                        <h3>Plan Actual:</h3>
                                        <p>Límite de DTE's: 50 al mes</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow h-100">
                                    <div class="card-body">
                                        <h3>Nuevo Plan:</h3>
                                        <div class="form-group mb-3">
                                            <label for="nuevoPlan">Plan:</label>
                                            <select class="form-select" id="nuevoPlan">
                                                <option value="cloud1">Plan Cloud #1</option>
                                                <option value="cloud2">Plan Cloud #2</option>
                                                <option value="cloud3">Plan Emprendedor</option>
                                            </select>
                                        </div>
                                        <p>Límite de DTEs: 50 <i class="fas fa-arrow-right"></i> 100 al mes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
@endsection
