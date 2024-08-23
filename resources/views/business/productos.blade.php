@extends('layouts.app')
@include('business.layouts.navbar')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Productos</h1>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-5">
                <div class="card shadow bg-light card-body">
                    <h3>Añadir Producto</h3>
                    <form method="POST" action="{{route("business.productos.store")}}" id="formProducto">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="tipo">Tipo de Producto:</label>
                            <select id="tipo" class="form-select" name="tipoItem" required>
                                <option selected value="1">1 - Bien</option>
                                <option value="2">2 - Servicio</option>
                                <option value="3">3 - Bien y Servicio</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="codigo">Código:</label>
                            <input type="text" class="form-control" id="codigo" name="codigo" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="uniMedida">Unidad de Medida:</label>
                            <input type="text" class="form-control" id="uniMedida" name="uniMedida" autocomplete="off"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion">Descripción:</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion">
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6">
                                <label for="precioSinTributos">Precio sin IVA:</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.00000001" class="form-control" id="precioSinTributos"
                                        name="precioSinTributos" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="precioUni">Precio con IVA:</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.00000001" class="form-control" id="precioUni"
                                        name="precioUni" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <span class="form-text">
                                    Puede ingresar el precio con IVA y el sistema calculará el precio sin IVA, o viceversa.
                                </span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <p class="form-label">Tributos que aplican a este producto:</p>
                            @foreach ($tributos as $tributo)
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" value="{{ $tributo->codigo }}"
                                        id="{{ $tributo->codigo }}" name="tributos[]"
                                        @if ($tributo->codigo == '20') checked disabled @endif>
                                    <label class="form-check-label" for="{{ $tributo->codigo }}">
                                        {{ $tributo->descripcion }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Guardar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card shadow bg-light card-body">
                    <p class="h4">Productos Registrados</p>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/producto.js')
@endsection
