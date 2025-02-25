<nav class="navbar navbar-expand-sm bg-dark navbar-dark px-3">
    <a class="navbar-brand" href="#">Facturación Electrónica</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="{{ route('business.dashboard') }}">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('business.dtes') }}">Documentos Emitidos</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('business.productos') }}">Productos</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('business.clientes') }}">Clientes</a></li>
            <li class="nav-item d-none"><a class="nav-link" href="{{ route('business.sucursales') }}">Sucursales</a>
            </li>
            <li class="nav-item d-none"><a class="nav-link" href="#">Usuarios</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            @guest
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a href="" class="dropdown-item">Configuración</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center" role="alert">
                <p class="h4">
                    <strong>✨ ¡Algo nuevo está en camino! ✨</strong> <br>
                    Pronto renovaremos nuestra interfaz para ofrecerte más funcionalidades y una mejor experiencia. 🚀<br>
                    ¡Gracias por ser parte de esta evolución!
                </p>
            </div>
        </div>
    </div>
</div>
@if (env('APP_ENV') == 'local')
    <div class="container-fluid my-2">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    <p class="h4">
                        <strong>Advertencia: </strong> Está en el ambiente de pruebas. Los documentos emitidos no tienen
                        validez tributaria.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif