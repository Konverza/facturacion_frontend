@extends('layouts.app')

@include('admin.layouts.navbar')
@section('content')
    <div class="container-fluid">
        <div class="row mt-5 mb-2">
            <div class="col-md-12">
                <div class="header-content">
                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <h1 class="header-title text-center">Facturación Electrónica</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-3 text-center">
                <div class="card mb-2 ">
                    <div class="card-body bg-light btn shadow">
                        <div class="row py-2 justify-content-center">
                            <div class="col-3">
                                <br>
                                <i class="fas fa-briefcase text-danger fa-4x"></i>
                            </div>
                            <div class="col-8">
                                <h1 class="card-title">{{ count($clientes) }}</h1>
                                <p class="card-text h5">Clientes <br>Activos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="card mb-2">
                    <div class="card-body bg-light btn shadow">
                        <div class="row py-2 justify-content-center">
                            <div class="col-3">
                                <br>
                                <i class="fas fa-file text-info fa-4x"></i>
                            </div>
                            <div class="col-8">
                                <h1 class="card-title">{{ $octopus_statistics["total"] }}</h1>
                                <p class="card-text h5">DTEs <br> Emitidos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="card mb-2">
                    <div class="card-body bg-light btn shadow">
                        <div class="row py-2 justify-content-center">
                            <div class="col-3">
                                <br>
                                <i class="fas fa-sack-dollar text-warning fa-4x"></i>
                            </div>
                            <div class="col-8">
                                <h1 class="card-title">${{ number_format($octopus_statistics["total_facturado"], 2) }}</h1>
                                <p class="card-text h5">Ventas <br>Registradas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="card mb-2">
                    <div class="card-body bg-light btn shadow">
                        <div class="row py-2 justify-content-center">
                            <div class="col-3">
                                <br>
                                <i class="fas fa-chart-line text-success fa-4x"></i>
                            </div>
                            <div class="col-8">
                                <h1 class="card-title">$0.00</h1>
                                <p class="card-text h5">Ingresos por <br>Planes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-8">
                <div class="card-body mb-2 shadow card">
                    <h4>Clientes Activos:</h4>
                    <table class="table table-striped table-bordered table-lg">
                        <thead>
                            <tr>
                                <th>Negocio</th>
                                <th>Plan</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($clientes) > 0)
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->plan->nombre }}</td>
                                        <td class="text-success"><i class="fa-solid fa-check"></i> Activo</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">No hay clientes activos</td>
                                </tr>
                            @endif

                            {{-- <tr>
                                <td>Industrias A24</td>
                                <td>Plan Cloud #3</td>
                                <td class="text-success"><i class="fa-solid fa-check"></i> Al día - Último pago: 01/04/2024
                                </td>
                            </tr>
                            <tr>
                                <td>Industrias Maybe</td>
                                <td>Plan Cloud #4</td>
                                <td class="text-warning"><i class="fa-solid fa-circle-exclamation"></i> DTE's agotados -
                                    Último pago: 01/05/2024</td>
                            </tr>
                            <tr>
                                <td>Industrias Example</td>
                                <td>Plan Cloud #5</td>
                                <td class="text-danger"><i class="fa-solid fa-x"></i> Vencido - Último pago: 01/06/2024</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-2 shadow card-body">
                    <h4>Estado Ministerio de Hacienda</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td>Firmador</td>
                            <td><span class="text-success"><i class="fa-solid fa-check"></i> OK</span></td>
                        </tr>
                        <tr>
                            <td>API Local</td>
                            <td><span class="text-success"><i class="fa-solid fa-check"></i> OK</span></td>
                        </tr>
                        <tr>
                            <td>API MH</td>
                            <td><span class="text-success"><i class="fa-solid fa-check"></i> OK</span></td>
                        </tr>
                        <tr>
                            <td>Almacenamiento</td>
                            <td><span class="text-success"><i class="fa-solid fa-check"></i> OK</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
