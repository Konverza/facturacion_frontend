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
                            <h1 class="header-title text-center">Negocios Registrados</h1>
                        </div>
                        <div class="col-lg-4 text-right">
                            <a href="{{ route('admin.negocios.new') }}" class="btn btn-primary mr-auto">Nuevo Negocio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <h4 class="mt-4"></h4>
                <table class="table shadow table-striped table-bordered mb-2">
                    <thead>
                        <tr>
                            <th>Negocio</th>
                            <th>Plan Contratado</th>
                            <th>Contacto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($clientes) > 0)
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->nombre }}</td>
                                    <td>{{ $cliente->plan->nombre }}</td>
                                    <td>
                                        Nombre: {{ $cliente->nombre_responsable }} <br>
                                        Tel.{{ $cliente->telefono }} <br>
                                        Correo: {{ $cliente->correo_responsable }}
                                    </td>
                                    <td>
                                        <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.negocios.pagos', 1) }}">
                                                    <i class="fa fa-dollar-sign me-2"></i>
                                                    Registrar Pago
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="">
                                                    <i class="fa fa-user-pen me-2"></i>
                                                    Ver Usuario
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="">
                                                    <i class="fa fa-pen-to-square me-2"></i>
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('admin.negocios.upgrade', 1) }}">
                                                    <i class="fa fa-cloud-arrow-up me-2"></i>
                                                    Mejorar Plan
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="">
                                                    <i class="fa fa-user-slash me-2"></i>
                                                    Desactivar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="">
                                                    <i class="fa fa-address-book me-2"></i>
                                                    Contactar
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No hay negocios registrados</td>
                            </tr>
                        @endif

                        {{-- <tr>
                            <td>Negocio S.A.S. de C.V.</td>
                            <td>Plan Emprendedor</td>
                            <td>Nombre: Juan Pérez Negocio <br> Tel.12345678 <br>Correo: juanperez123@mail.com</td>
                            <td>
                                <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Acciones
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.negocios.pagos', 1) }}">
                                            <i class="fa fa-dollar-sign me-2"></i>
                                            Registrar Pago
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="">
                                            <i class="fa fa-user-pen me-2"></i>
                                            Ver Usuario
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="">
                                            <i class="fa fa-pen-to-square me-2"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('admin.negocios.upgrade', 1) }}">
                                            <i class="fa fa-cloud-arrow-up me-2"></i>
                                            Mejorar Plan
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="">
                                            <i class="fa fa-user-slash me-2"></i>
                                            Desactivar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="">
                                            <i class="fa fa-address-book me-2"></i>
                                            Contactar
                                        </a>
                                    </li>
                                </ul>

                            </td>
                        </tr> --}}
                    </tbody>
                </table>

            </div>
        </div>
        <div class="row my-4">
            <div class="col-md-6">
                <div class="card shadow card-body mt-3 mb-2">
                    <h4 class="mt-4">Pagos Pendientes</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Negocio</th>
                                <th>Fecha de Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center">No hay pagos pendientes</td>
                            </tr>
                            {{-- <tr>
                                <td>Negocio S.A.S. de C.V.</td>
                                <td>08/06/2024</td>
                                <td>$29.99</td>
                            </tr>
                            <tr>
                                <td>Negocio 2 Industries</td>
                                <td>09/06/2024</td>
                                <td>$59.99</td>
                            </tr>
                            <tr>
                                <td>Negocio 3 S.A. de C.V.</td>
                                <td>10/06/2024</td>
                                <td>$99.99</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow card-body mt-3 mb-2">
                    <h4 class="mt-4">Uso de DTE's</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Negocio</th>
                                <th>Uso</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($clientes) > 0)
                                @foreach($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->dtes_emitidos ?? 0 }} Emitidas de {{ $cliente->plan->limite }} ({{ round(($cliente->dtes_emitidos / $cliente->plan->limite) * 100) }}% Usado)<div class="progress">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ round(($cliente->dtes_emitidos / $cliente->plan->limite) * 100) }}%;"
                                                    aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-center">No hay negocios registrados</td>
                                </tr>
                            @endif

                            {{-- <tr>
                                <td>Negocio S.A.S. de C.V.</td>
                                <td>35 Emitidas de 50 (70% Usado)<div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 70%;"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Negocio 2 Industries</td>
                                <td>50 Emitidas de 50 (100% Usado)<div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Negocio 3 S.A. de C.V.</td>
                                <td>10 Emitidas de 50 (20% Usado)<div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 20%;"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
