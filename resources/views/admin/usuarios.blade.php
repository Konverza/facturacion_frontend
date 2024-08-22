@extends('layouts.app')
@include('admin.layouts.navbar')
@section('content')
    <div class="container mt-4 col-md-10">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Registrar Usuario</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route("admin.usuarios.store")}}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ingrese su nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="correo" name="correo"
                                    placeholder="Ingrese su correo electrónico" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena"
                                    placeholder="Ingrese su contraseña" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña:</label>
                                <input type="password" class="form-control" id="confirmar_contrasena"
                                    name="confirmar_contrasena" placeholder="Confirme su contraseña" required>
                            </div>
                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipoUsuario"
                                        id="usuarioKonverza" value="1">
                                    <label class="form-check-label" for="usuarioKonverza">
                                        Administrador de Konverza
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipoUsuario"
                                        id="usuarioNegocio" value="2">
                                    <label class="form-check-label" for="usuarioNegocio">
                                        Usuario de Negocio
                                    </label>
                                </div>
                            </div>
                            <div id="negocioLocal" class="d-none">
                                <div class="form-group mb-3">
                                    <label for="userType">Tipo de Usuario:</label>
                                    <select class="form-select" id="userType" name="userType" aria-label="Default select example">
                                        <option value="negocio">Representante Empresa</option>
                                        <option value="vendedor">Cajero</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="nombreNegocio">Negocio:</label>
                                    <input type="text" class="form-control" id="nombreNegocio" name="nit_negocio" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Usuarios</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Rol</th>
                                    <th>Últ. Conexión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            {{ $usuario->name }}<br>
                                            <small>{{ $usuario->email }}</small>
                                        </td>
                                        <td> Activo </td>
                                        <td>{{ $usuario->roles[0]->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($usuario->updated_at)->diffForHumans() }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning">Editar</button>
                                            <button class="btn btn-sm btn-danger">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- <tr>
                                    <td>usuario1decliente1</td>
                                    <td>Activo</td>
                                    <td>Usuario</td>
                                    <td>Hoy a las 9:41 a.m.</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">Editar</button>
                                        <button class="btn btn-sm btn-danger">Eliminar</button>
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite("resources/js/clients.js")
@endsection
