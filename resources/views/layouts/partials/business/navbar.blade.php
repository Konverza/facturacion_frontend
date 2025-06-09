@if ($test_enviroment)
    <div class="fixed top-0 z-[90] flex h-12 w-full items-center justify-center bg-red-500 px-2 text-white sm:h-8">
        <span class="flex items-center gap-2 text-xs font-semibold sm:text-sm">
            <x-icon icon="info-circle" class="h-4 w-4 min-w-4 max-w-4" />
            Advertencia: Está en el ambiente de pruebas. Los documentos emitidos no tienen validez legal.
        </span>
    </div>
@endif

<nav class="@if ($test_enviroment) mt-12 sm:mt-8 @endif z-20 ms-auto h-14 w-full border-b border-gray-300 bg-transparent dark:border-gray-800 lg:w-calc-full-minus-64"
    id="navbar">
    <div class="flex h-full items-center px-3 lg:px-5 lg:pl-3">
        <div class="flex w-full items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="sidebar" data-drawer-toggle="sidebar" aria-controls="sidebar" type="button"
                    class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-900 dark:focus:ring-gray-700 lg:hidden"
                    id="btn-open-drawer-nav">
                    <span class="sr-only">Open sidebar</span>
                    <x-icon icon="menu" class="h-6 w-6" />
                </button>
            </div>
            <div class="flex w-full items-center justify-end lg:justify-between">
                <x-button type="button" size="small" id="toggle-sidebar" onlyIcon icon="arrow-bar-left"
                    typeButton="secondary" class="hidden lg:block" />
                <div class="flex">
                    <x-toggle-theme />
                    <div class="ms-3 flex items-center">
                        <div>
                            <button type="button"
                                class="flex rounded-full border border-gray-300 p-2 text-sm focus:ring-4 focus:ring-gray-300 dark:border-gray-800 dark:focus:ring-gray-700"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <x-icon icon="user" class="size-4 rounded-full text-gray-900 dark:text-gray-300" />
                            </button>
                        </div>
                        <div class="z-50 my-4 hidden list-none" id="dropdown-user">
                            <div
                                class="me-4 animate-fade-down divide-y divide-gray-300 rounded-lg border border-gray-300 bg-white text-base animate-duration-300 dark:divide-gray-800 dark:border-gray-800 dark:bg-gray-950">
                                <div class="px-4 py-3" role="none">
                                    <p class="text-sm font-semibold text-primary-500 dark:text-primary-300"
                                        role="none">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="w-40 truncate text-ellipsis text-xs font-medium text-gray-900 dark:text-gray-300"
                                        title="{{ auth()->user()->email }}" role="none">
                                        {{ auth()->user()->email }}
                                    </p>
                                </div>
                                <ul class="p-1" role="none">
                                    <li>
                                        <a href="{{ Route('admin.dashboard') }}"
                                            class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                            role="menuitem">
                                            <x-icon icon="dashboard" class="h-4 w-4" />
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ Route('business.profile.index') }}"
                                            class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                            role="menuitem">
                                            <x-icon icon="user" class="h-4 w-4" />
                                            Perfil
                                        </a>
                                    </li>
                                    @if ($businesses->count() > 1)
                                        <li>
                                            <button type="button"
                                                class="flex w-full items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                                data-modal-target="select-business" data-modal-toggle="select-business"
                                                role="menuitem">
                                                <x-icon icon="refresh" class="h-4 w-4" />
                                                Cambiar de negocio
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                                <div class="p-1">
                                    <form action="{{ Route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                            role="menuitem">
                                            <x-icon icon="logout" class="h-4 w-4" />
                                            Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="sidebar"
    class="@if ($test_enviroment) mt-12 sm:mt-8 @endif fixed left-0 top-0 z-40 h-screen w-64 -translate-x-full border-r border-gray-300 bg-white pt-4 transition-transform dark:border-gray-800 dark:bg-gray-950 lg:z-[35] lg:translate-x-0 lg:dark:bg-transparent"
    aria-label="Sidebar">
    <div class="overflow-y-auto px-2 pb-4">
        <div id="icon-complete">
            <a href="#" class="ms-2 flex">
                <img src="{{ asset('images/logo.png') }}" class="me-3 h-8" alt="FlowBite Logo" />
            </a>
        </div>
        <div class="hidden" id="only-icon">
            <a href="#" class="ms-2 flex">
                <img src="{{ asset('images/only-icon.png') }}" class="me-3 h-8" alt="FlowBite Logo" />
            </a>
        </div>
        <ul class="mt-2 space-y-2 font-medium">
            <li>
                <a href="{{ Route('business.dashboard') }}" data-tooltip-target="tooltip-dashboard"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="dashboard"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3">Dashboard</span>
                </a>
                <div id="tooltip-dashboard" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Dashboard
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            <li>
                <a href="{{ Route('business.documents.index') }}" data-tooltip-target="tooltip-documents"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="files"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Documentos emitidos
                    </span>
                </a>
                <div id="tooltip-documents" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden text-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Documentos emitidos
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>            
            @php
                $business_id = Session::get('business') ?? null;
                $business = \App\Models\Business::find($business_id);
            @endphp
            <li>
                <a href="{{ Route('business.sucursales.index', $business_id) }}" data-tooltip-target="tooltip-sucursales"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="building-store"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Sucursales
                    </span>
                </a>
                <div id="tooltip-sucursales" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden text-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Sucursales
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            @if ($business->posmode)
                <li>
                    <a href="{{ Route('business.categories.index') }}" data-tooltip-target="tooltip-categories"
                        class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                        <x-icon icon="address-book"
                            class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3 flex-1 whitespace-nowrap">
                            Categorías
                        </span>
                    </a>
                    <div id="tooltip-categories" role="tooltip"
                        class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Categorías
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </li>
            @endif
            <li>
                <a href="{{ Route('business.products.index') }}" data-tooltip-target="tooltip-products"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="box"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Productos
                    </span>
                </a>
                <div id="tooltip-products" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Productos
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            <li>
                <a href="{{ Route('business.movements.index') }}" data-tooltip-target="tooltip-movements"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="arrow-sort"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Movimientos
                    </span>
                </a>
                <div id="tooltip-movements" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Movimientos
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            {{--      <li>
                <a href="{{ Route('business.customers.index') }}" data-tooltip-target="tooltip-cuentas-por-pagar"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="home-dollar"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Cuentas por pagar
                    </span>
                </a>
                <div id="tooltip-cuentas-por-pagar" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden text-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Cuentas por pagar
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li> --}}
            <li>
                <a href="{{ Route('business.customers.index') }}" data-tooltip-target="tooltip-customers"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="users"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Clientes
                    </span>
                </a>
                <div id="tooltip-customers" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Clientes
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            @if (!auth()->user()->only_fcf)
                <li>
                    <a href="{{ Route('business.cuentas-por-cobrar.index') }}"
                        data-tooltip-target="tooltip-cuentas-por-cobrar"
                        class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                        <x-icon icon="coin"
                            class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3 flex-1 whitespace-nowrap">
                            Cuentas por cobrar
                        </span>
                    </a>
                    <div id="tooltip-cuentas-por-cobrar" role="tooltip"
                        class="shadow-xs tooltip invisible absolute z-10 hidden text-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Cuentas por cobrar
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </li>
            @endif
            @if ($business->posmode)
                <li>
                    <a href="{{ Route('business.pos.index') }}" data-tooltip-target="tooltip-pos"
                        class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                        <x-icon icon="cash-register"
                            class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3 flex-1 whitespace-nowrap">
                            Punto de venta
                        </span>
                    </a>
                    <div id="tooltip-pos" role="tooltip"
                        class="shadow-xs tooltip invisible absolute z-10 hidden text-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Punto de venta
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </li>
            @endif
            <li>
                @include('layouts.partials.business.button-new-dte')
            </li>
            {{--   <li>
                <a href="{{ Route('admin.configuration.index') }}" data-tooltip-target="tooltip-configuration"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="settings"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">Configuración</span>
                </a>
                <div id="tooltip-configuration" role="tooltip"
                    class="shadow-xs tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Configuración
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li> --}}
        </ul>
    </div>
</aside>

@if (Auth::check() && auth()->user()->hasRole('business'))
    @include('layouts.partials.business.modal-new-dte')
    <div id="select-business" tabindex="-1" aria-hidden="true"
        class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-lg p-4">
            <!-- Modal content -->
            <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                <div class="flex flex-col">
                    <!-- Modal header -->
                    <div
                        class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Seleccionar negocio
                        </h3>
                        <button type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                            data-modal-hide="select-business">
                            <x-icon icon="x" class="h-5 w-5" />
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4">
                        <div class="flex flex-col gap-4">
                            @foreach ($businesses as $business)
                                @php
                                    $logo = Http::get(
                                        env('OCTOPUS_API_URL') . '/datos_empresa/nit/' . $business->nit . '/logo',
                                    )->json();
                                @endphp
                                <form action="{{ Route('business.select') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="business" value="{{ $business->id }}" />
                                    <button type="submit"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                                        <div class="flex justify-start gap-4">
                                            <img src="{{ $logo['url'] }}"
                                                class="size-12 rounded-full bg-white object-contain p-1"
                                                alt="logo empresa" />
                                            <div class="flex flex-col items-start">
                                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                                                    {{ $business->nombre }}
                                                </h2>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    NIT: {{ $business->nit }}
                                                </p>
                                                <p
                                                    class="text-wrap text-left text-sm text-gray-500 dark:text-gray-400">
                                                    Correo responsable: {{ $business->correo_responsable }}
                                                </p>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div
                        class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                        <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                            data-modal-hide="select-business" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
