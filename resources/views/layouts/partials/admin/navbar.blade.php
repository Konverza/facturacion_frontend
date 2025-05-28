
<nav
    class="z-20 ms-auto h-14 w-full border-b border-gray-300 bg-transparent bg-white dark:border-gray-800 dark:bg-gray-950 md:w-calc-full-minus-56 md:bg-transparent md:dark:bg-transparent">
    <div class="flex h-full items-center px-3 lg:px-5 lg:pl-3">
        <div class="flex w-full items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="sidebar" data-drawer-toggle="sidebar" aria-controls="sidebar" type="button"
                    class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-900 dark:focus:ring-gray-700 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <x-icon icon="menu" class="h-6 w-6" />
                </button>
            </div>
            <div class="flex w-full items-center justify-end">
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
                                <p class="text-sm font-semibold text-primary-500 dark:text-primary-300" role="none">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="w-40 truncate text-ellipsis text-xs font-medium text-gray-900 dark:text-gray-300"
                                    title="{{ auth()->user()->email }}" role="none">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>
                            <ul class="p-2" role="none">
                                <li>
                                    <a href="{{ Route('admin.dashboard') }}"
                                        class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                        role="menuitem">
                                        <x-icon icon="dashboard" class="h-4 w-4" />
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white"
                                        role="menuitem">
                                        <x-icon icon="user" class="h-4 w-4" />
                                        Perfil
                                    </a>
                                </li>
                            </ul>
                            <div class="p-2">
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
</nav>

<aside id="sidebar"
    class="e fixed left-0 top-0 z-[35] h-screen w-56 -translate-x-full border-r border-gray-300 bg-white pt-4 transition-transform dark:border-gray-800 dark:bg-gray-950 md:translate-x-0 md:bg-transparent dark:md:bg-transparent"
    aria-label="Sidebar">
    <div class="h-full overflow-y-auto px-2 pb-4">
        <a href="#" class="ms-2 flex">
            <img src="{{ asset('images/logo.png') }}" class="me-3 h-8" alt="FlowBite Logo" />
        </a>
        <ul class="mt-2 space-y-2 font-medium">
            <li>
                <a href="{{ Route('admin.dashboard') }}"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="dashboard"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ Route('admin.business.index') }}"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="building-store"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">
                        Negocios
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ Route('admin.plans.index') }}"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="businessplan"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">Planes</span>
                </a>
            </li>
            <li>
                <a href="{{ Route('admin.users.index') }}"
                    class="group flex items-center rounded-lg p-2 text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900">
                    <x-icon icon="users"
                        class="h-5 w-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                    <span class="ms-3 flex-1 whitespace-nowrap">Usuarios</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
