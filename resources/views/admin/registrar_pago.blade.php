@extends('layouts.app')
@include('admin.layouts.navbar')
@section('content')
    <div class="container col-md-6 mt-5">
        <div class="card shadow bg-light card-body">
            <h1>Registrar Pago</h1>

            <form>
                <div class="form-group mb-3">
                    <label for="negocio">Negocio:</label>
                    <input type="text" class="form-control" id="negocio" value="Luna No te Vayas" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="planContratado">Plan Contratado:</label>
                    <input type="text" class="form-control" id="planContratado" value="Plan Cloud #1" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="cuota">Cuota a pagar:</label>
                    <select class="form-select" id="cuota">
                        <option value="junio2024">Cuota Mayo 2024</option>
                        <option value="junio2024">Cuota Junio 2024</option>
                        <option value="junio2024">Cuota Julio 2024</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="comprobante">Comprobante de Pago:</label>
                    <input type="file" class="form-control" id="comprobante">
                </div>

                <button type="submit" class="btn btn-primary">Guardar Pago</button>
            </form>
        </div>
    </div>
@endsection
