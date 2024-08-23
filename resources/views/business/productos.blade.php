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
                    <table class="table table-bordered table-hover table-striped w-100 align-middle" id="productosTable">
                        <thead>
                            <tr class="align-middle text-center">
                                <th style="width: 5%;">Código</th>
                                <th style="width: 10%;">Descripción</th>
                                <th style="width: 15%;">Precios</th>
                                <th style="width: 30%;">Tributos</th>
                                <th style="width: 20%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr class="align-middle">
                                    <td>{{ $producto->codigo }}</td>
                                    <td>{{ $producto->descripcion }}</td>
                                    <td>
                                        <p class="m-0">Precio sin IVA: ${{ $producto->precioSinTributos }}</p>
                                        <p class="m-0">Precio con IVA: ${{ $producto->precioUni }}</p>
                                    </td>
                                    <td>
                                        @php
                                            $tributos_producto = json_decode($producto->tributos);
                                            $texto_tributos = [];
                                            // Buscar el código de tributo en la lista de tributos
                                            foreach ($tributos_producto as $tributo) {
                                                $tributo_encontrado = $tributos->where('codigo', $tributo)->first();
                                                if ($tributo_encontrado) {
                                                    $texto_tributos[] = $tributo_encontrado->descripcion;
                                                }
                                            }
                                        @endphp
                                        <ul>
                                            @foreach ($texto_tributos as $tributo)
                                                <li>{{ $tributo }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <form action="{{ route('business.productos.destroy', $producto->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="align-middle text-center">
                                <th>Código</th>
                                <th>Unidad de Medida</th>
                                <th>Descripción</th>
                                <th>Precios</th>
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/producto.js')
@endsection
