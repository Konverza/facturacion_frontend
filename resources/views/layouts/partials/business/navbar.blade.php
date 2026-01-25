@if ($test_enviroment)
    <div class="fixed top-0 z-[90] flex h-12 w-full items-center justify-center bg-red-500 px-2 text-white sm:h-8">
        <span class="flex items-center gap-2 text-xs font-semibold sm:text-sm">
            <x-icon icon="info-circle" class="h-4 w-4 min-w-4 max-w-4" />
            Advertencia: Está en el ambiente de pruebas. Los documentos emitidos no tienen validez legal.
        </span>
    </div>
@endif
@if ($maintenance_notice)
    <div class="fixed top-0 z-[90] flex h-12 w-full items-center justify-center bg-red-500 px-2 text-white sm:h-8">
        <span class="flex items-center gap-2 text-xs font-semibold sm:text-sm">
            <x-icon icon="info-circle" class="h-4 w-4 min-w-4 max-w-4" />
            Aviso: Haremos un mantenimiento breve a las 12:30 PM, con una duración aproximada de <b class="uppercase">15
                minutos.</b> Por favor, no realice ninguna operación durante este tiempo. Agradecemos su comprensión.
        </span>
    </div>
@endif
@php
    $business_id = Session::get('business') ?? null;
    $business = \App\Models\Business::find($business_id);
    $business_user = \App\Models\BusinessUser::where('user_id', auth()->user()->id)
        ->where('business_id', $business_id)
        ->first();
@endphp
<nav class="@if ($test_enviroment || $maintenance_notice) mt-12 sm:mt-8 @endif z-20 ms-auto h-14 w-full border-b border-gray-300 bg-transparent dark:border-gray-800 lg:w-calc-full-minus-64"
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
                    typeButton="secondary" class="hidden lg:block lg:ms-8" />
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
                                    @if ($business_user->branch_selector)
                                        <li>
                                            <a href="{{ Route('business.select-sucursal') }}"
                                                class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                                role="menuitem">
                                                <x-icon icon="refresh" class="h-4 w-4" />
                                                Cambiar de sucursal
                                            </a>
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
    class="@if ($test_enviroment || $maintenance_notice) mt-12 sm:mt-8 @endif  fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full border-r border-secondary-300 bg-white dark:bg-gray-900 pt-4 transition-transform dark:border-secondary-800 lg:z-[35] lg:translate-x-0 lg:dark:bg-transparent"
    aria-label="Sidebar">
    <div class="flex h-full flex-col overflow-y-auto px-2 pb-2">
        <a href="#" class="ms-2 flex">
            <img src="{{ asset('images/logo.png') }}" class="me-3 h-8" alt="Logo Konverza" />
        </a>
        <div class="sidebar-search mt-3 px-1">
            <label for="sidebar-search" class="sr-only">Buscar en el menú</label>
            <div class="relative">
                <span
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
                    <x-icon icon="search" class="h-4 w-4" />
                </span>
                <input id="sidebar-search" type="search" placeholder="Buscar en el menú"
                    class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-xs text-gray-700 placeholder:text-gray-400 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-800 dark:bg-secondary-950 dark:text-gray-200 dark:placeholder:text-gray-500" />
            </div>
        </div>
        <ul class="mt-3 space-y-1 text-[15px] font-medium">
            <li data-menu-item data-menu-level="main">
                <a href="{{ Route('business.dashboard') }}" data-menu-text="Inicio" data-tooltip-target="tooltip-inicio"
                    data-menu-keywords="home inicio dashboard"
                    class="group flex items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-secondary-900">
                    <x-icon icon="home"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3">Inicio</span>
                </a>
                <div id="tooltip-inicio" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Inicio
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            <li data-menu-item data-menu-level="main">
                <a href="{{ Route('business.customers.index') }}" data-menu-text="Clientes"
                    data-tooltip-target="tooltip-clientes"
                    data-menu-keywords="clientes customer"
                    class="group flex items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-secondary-900">
                    <x-icon icon="users"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3">Clientes</span>
                </a>
                <div id="tooltip-clientes" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Clientes
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>

            @if ($business->posmode)
                <li data-menu-item data-menu-level="main">
                    <a href="{{ Route('business.pos.index') }}" data-menu-text="Punto de Venta"
                        data-tooltip-target="tooltip-pos"
                        data-menu-keywords="pos punto de venta caja"
                        class="group flex items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-secondary-900">
                        <x-icon icon="cash-register"
                            class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3">Punto de Venta</span>
                    </a>
                    <div id="tooltip-pos" role="tooltip"
                        class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Punto de Venta
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </li>
            @endif

            <li data-menu-item data-menu-level="main">
                <button type="button" data-accordion-trigger="dropdown-productos" data-panel-id="dropdown-productos"
                    aria-controls="dropdown-productos" aria-expanded="false" data-menu-text="Productos"
                    data-tooltip-target="tooltip-productos" data-menu-keywords="productos catalogo inventario"
                    class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                    <x-icon icon="package"
                        class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Productos</span>
                    <x-icon icon="arrow-down"
                        class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                </button>
                <div id="tooltip-productos" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Productos
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <ul id="dropdown-productos" data-menu-panel class="hidden space-y-1 py-1">
                    @if ($business->posmode)
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.categories.index') }}" data-menu-text="Categorías"
                                data-tooltip-target="tooltip-categorias" data-menu-keywords="categorias productos pos"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="layers"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Categorías</span>
                            </a>
                            <div id="tooltip-categorias" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Categorías
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    @endif
                    @if($business->price_variants_enabled)
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.price-variants.index') }}" data-menu-text="Variantes de Precio"
                            data-tooltip-target="tooltip-variantes-precio" data-menu-keywords="variantes precio tarifas"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="currency-dollar"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Variantes de Precio</span>
                        </a>
                        <div id="tooltip-variantes-precio" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Variantes de Precio
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                    @endif
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.products.index') }}" data-menu-text="Lista de Productos"
                            data-tooltip-target="tooltip-lista-productos" data-menu-keywords="lista productos"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="list"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Lista de Productos</span>
                        </a>
                        <div id="tooltip-lista-productos" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Lista de Productos
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.movements.index') }}" data-menu-text="Movimientos"
                            data-tooltip-target="tooltip-movimientos" data-menu-keywords="movimientos kardex"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="transfer"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Movimientos</span>
                        </a>
                        <div id="tooltip-movimientos" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Movimientos
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                </ul>
            </li>

            <li data-menu-item data-menu-level="main">
                <button type="button" data-accordion-trigger="dropdown-ventas" data-panel-id="dropdown-ventas"
                    aria-controls="dropdown-ventas" aria-expanded="false" data-menu-text="Ventas"
                    data-tooltip-target="tooltip-ventas" data-menu-keywords="ventas facturas dte"
                    class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                    <x-icon icon="receipt"
                        class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Ventas</span>
                    <x-icon icon="arrow-down"
                        class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                </button>
                <div id="tooltip-ventas" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Ventas
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <ul id="dropdown-ventas" data-menu-panel class="hidden space-y-1 py-1">
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.documents.index') }}" data-menu-text="Documentos Emitidos"
                            data-tooltip-target="tooltip-documentos-emitidos"
                            data-menu-keywords="documentos emitidos dte"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="document"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Documentos Emitidos</span>
                        </a>
                        <div id="tooltip-documentos-emitidos" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Documentos Emitidos
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                    @if ($business->invoice_bag_enabled)
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.invoice-bags.index') }}" data-menu-text="Bolsón de Facturas"
                                data-tooltip-target="tooltip-bolson-facturas"
                                data-menu-keywords="bolson facturas"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="files"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Bolsón de Facturas</span>
                            </a>
                            <div id="tooltip-bolson-facturas" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Bolsón de Facturas
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    @endif
                    @if (!auth()->user()->only_fcf)
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.cuentas-por-cobrar.index') }}"
                                data-menu-text="Cuentas por Cobrar" data-tooltip-target="tooltip-cuentas-cobrar"
                                data-menu-keywords="cxc cuentas por cobrar"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="moneybag"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Cuentas por Cobrar</span>
                            </a>
                            <div id="tooltip-cuentas-cobrar" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Cuentas por Cobrar
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    @endif
                </ul>
            </li>

            @if ($business->bulk_emission)
                <li data-menu-item data-menu-level="main">
                    <button type="button" data-accordion-trigger="dropdown-lotes" data-panel-id="dropdown-lotes"
                        aria-controls="dropdown-lotes" aria-expanded="false" data-menu-text="Facturación por Lotes"
                        data-tooltip-target="tooltip-lotes" data-menu-keywords="lotes masivo dte"
                        class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                        <x-icon icon="files"
                            class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Facturación por Lotes</span>
                        <x-icon icon="arrow-down"
                            class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    </button>
                    <div id="tooltip-lotes" role="tooltip"
                        class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Facturación por Lotes
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                    <ul id="dropdown-lotes" data-menu-panel class="hidden space-y-1 py-1">
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.bulk.index') }}" data-menu-text="Plantillas de DTE"
                                data-tooltip-target="tooltip-plantillas-dte" data-menu-keywords="plantillas dte"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="file-text"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Plantillas de DTE</span>
                            </a>
                            <div id="tooltip-plantillas-dte" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Plantillas de DTE
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.bulk.send') }}" data-menu-text="Envío Masivo"
                                data-tooltip-target="tooltip-envio-masivo" data-menu-keywords="envio masivo"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="send"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Envío Masivo</span>
                            </a>
                            <div id="tooltip-envio-masivo" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Envío Masivo
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    </ul>
                </li>
            @endif

            <li data-menu-item data-menu-level="main">
                <button type="button" data-accordion-trigger="dropdown-compras" data-panel-id="dropdown-compras"
                    aria-controls="dropdown-compras" aria-expanded="false" data-menu-text="Compras"
                    data-tooltip-target="tooltip-compras" data-menu-keywords="compras documentos recibidos"
                    class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                    <x-icon icon="shopping-cart"
                        class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Compras</span>
                    <x-icon icon="arrow-down"
                        class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                </button>
                <div id="tooltip-compras" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Compras
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <ul id="dropdown-compras" data-menu-panel class="hidden space-y-1 py-1">
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.received-documents.index') }}" data-menu-text="Documentos Recibidos"
                            data-tooltip-target="tooltip-documentos-recibidos" data-menu-keywords="documentos recibidos"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white"
                            tabindex="-1" aria-disabled="true">
                            <x-icon icon="inbox"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Documentos Recibidos</span>
                        </a>
                        <div id="tooltip-documentos-recibidos" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Documentos Recibidos
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                </ul>
            </li>

            @if ($business->pos_inventory_enabled)
                <li data-menu-item data-menu-level="main">
                    <button type="button" data-accordion-trigger="dropdown-inventario"
                        data-panel-id="dropdown-inventario" aria-controls="dropdown-inventario" aria-expanded="false"
                        data-menu-text="Inventario" data-tooltip-target="tooltip-inventario"
                        data-menu-keywords="inventario stock"
                        class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                        <x-icon icon="package"
                            class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                        <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Inventario</span>
                        <x-icon icon="arrow-down"
                            class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    </button>
                    <div id="tooltip-inventario" role="tooltip"
                        class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                        Inventario
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                    <ul id="dropdown-inventario" data-menu-panel class="hidden space-y-1 py-1">
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.inventory.pos.index') }}" data-menu-text="Inventario por POS"
                                data-tooltip-target="tooltip-inventario-pos" data-menu-keywords="inventario pos"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="device-pos"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Inventario por POS</span>
                            </a>
                            <div id="tooltip-inventario-pos" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Inventario por POS
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                        <li data-menu-item data-menu-level="sub">
                            <a href="{{ Route('business.inventory.transfers.index') }}" data-menu-text="Traslados"
                                data-tooltip-target="tooltip-traslados" data-menu-keywords="traslados transferencias"
                                class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                                <x-icon icon="transfer"
                                    class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                                <span class="ms-2">Traslados</span>
                            </a>
                            <div id="tooltip-traslados" role="tooltip"
                                class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                                Traslados
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    </ul>
                </li>
            @endif

            <li data-menu-item data-menu-level="main">
                <a href="{{ Route('business.reporting.general') }}" data-menu-text="Reportería General"
                    data-tooltip-target="tooltip-reporte-general" data-menu-keywords="reporteria general reportes"
                    class="group flex items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-secondary-900">
                    <x-icon icon="report-general"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">Reportería General</span>
                </a>
                <div id="tooltip-reporte-general" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Reportería General
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>
            <li data-menu-item data-menu-level="main">
                <a href="{{ Route('business.reporting.index') }}" data-menu-text="Reportería Contable"
                    data-tooltip-target="tooltip-reporte-contable" data-menu-keywords="reporteria contable"
                    class="group flex items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-secondary-900">
                    <x-icon icon="report-accounting"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3">Reportería Contable</span>
                </a>
                <div id="tooltip-reporte-contable" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Reportería Contable
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </li>

            <li data-menu-item data-menu-level="main">
                <button type="button" data-accordion-trigger="dropdown-configuracion"
                    data-panel-id="dropdown-configuracion" aria-controls="dropdown-configuracion"
                    aria-expanded="false" data-menu-text="Configuración" data-tooltip-target="tooltip-configuracion"
                    data-menu-keywords="configuracion ajustes"
                    class="group flex w-full items-center rounded-lg px-2 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                    <x-icon icon="settings"
                        class="h-5 w-5 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap text-left rtl:text-right">Configuración</span>
                    <x-icon icon="arrow-down"
                        class="h-4 w-4 shrink-0 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                </button>
                <div id="tooltip-configuracion" role="tooltip"
                    class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                    Configuración
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
                <ul id="dropdown-configuracion" data-menu-panel class="hidden space-y-1 py-1">
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.sucursales.index', $business_id) }}" data-menu-text="Sucursales"
                            data-tooltip-target="tooltip-sucursales" data-menu-keywords="sucursales"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="building-store"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Sucursales</span>
                        </a>
                        <div id="tooltip-sucursales" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Sucursales
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                    <li data-menu-item data-menu-level="sub">
                        <a href="{{ Route('business.profile.index') }}" data-menu-text="Datos del Negocio"
                            data-tooltip-target="tooltip-datos-negocio" data-menu-keywords="negocio perfil"
                            class="group flex items-center rounded-lg ps-8 pe-2 py-1 text-[13px] text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-white">
                            <x-icon icon="building"
                                class="h-5 w-5 text-gray-400 transition duration-75 group-hover:text-gray-700 dark:text-gray-500 dark:group-hover:text-white" />
                            <span class="ms-2">Datos del Negocio</span>
                        </a>
                        <div id="tooltip-datos-negocio" role="tooltip"
                            class="tooltip invisible absolute z-10 hidden rounded-lg bg-gray-900 px-3 py-2 text-xs font-medium text-white opacity-0 transition-opacity duration-300 dark:bg-gray-700">
                            Datos del Negocio
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                </ul>
            </li>
            <li data-menu-item data-menu-level="main">
                @include('layouts.partials.business.button-new-dte')
            </li>
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