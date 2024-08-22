<nav class="navbar navbar-expand-md bg-dark navbar-dark px-3">
    <a class="navbar-brand" href="#">Konverza Digital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="{{ route("admin.dashboard") }}">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route("admin.negocios") }}">Negocios</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route("admin.planes") }}">Planes</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route("admin.usuarios") }}">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route("admin.settings") }}">Configuración</a></li>
        </ul>
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ms-auto">
            <!-- Authentication Links -->
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
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>
            @endguest
        </ul>
    </div>
</nav>