@extends('layouts.auth-template')
@section('title', 'Usuarios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Usuarios
            </h1>
        </div>
        <div class="mt-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar usuario" class="w-full" icon="search" id="input-search-data" />
                </div>
                <div class="flex-1">
                    <x-button type="button" icon="plus" typeButton="primary" text="Nuevo usuario" class="w-full"
                        data-modal-target="new-user" data-modal-toggle="new-user" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Usuario</x-th>
                        <x-th>Negocio(s)</x-th>
                        <x-th>Estado</x-th>
                        <x-th>Rol</x-th>
                        <x-th>Ultima conexión</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($users as $user)
                        <x-tr>
                            <x-td>
                                {{ $loop->iteration }}
                            </x-td>
                            <x-td>
                                <div class="flex flex-col justify-start gap-1">
                                    <span class="font-semibold text-gray-500 dark:text-gray-300">
                                        {{ $user->name }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        {{ $user->email }}
                                    </span>
                                </div>
                            </x-td>
                            <x-td>
                                @if ($user->businesses->count() > 0)
                                    <div class="flex flex-col gap-1">
                                        @foreach ($user->businesses as $business)
                                            <span class="text-sm text-gray-500 dark:text-gray-300">
                                                {{ $user->businesses->count() > 1 ? '-' : '' }}
                                                {{ $business->business->nombre }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-300">
                                        Sin negocios
                                    </span>
                                @endif
                            </x-td>
                            <x-td>
                                @if ($user->status)
                                    <span
                                        class="flex w-max items-center gap-1 rounded-full text-sm font-semibold text-green-500">
                                        <x-icon icon="circle-check" class="size-4 text-green-500" />
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="flex w-max items-center gap-1 rounded-full text-sm font-semibold text-red-500">
                                        <x-icon icon="alert-circle" class="size-4 text-red-500" />
                                        Inactivo
                                    </span>
                                @endif
                            </x-td>
                            <x-td>
                                <span
                                    class="flex w-max items-center gap-1 rounded-full text-sm font-semibold uppercase text-purple-500">
                                    <x-icon icon="{{ $roles[$user->role]['icon'] }}" class="size-4 text-purple-500" />
                                    {{ $roles[$user->role]['name'] }}
                                </span>
                            </x-td>
                            <x-td>
                                {{ $user->last_login_at ?? 'Nunca' }}
                            </x-td>
                            <x-td th :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-users-{{ $user->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-users-{{ $user->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <button type="button"
                                                    data-url="{{ Route('admin.users.edit', $user->id) }}"
                                                    data-action="{{ Route('admin.users.update', $user->id) }}"
                                                    data-type="users"
                                                    class="btn-edit flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </button>
                                            </li>
                                            <li>
                                                <a href="{{ Route('admin.users.businesses', $user->id) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="building-store" class="h-4 w-4" />
                                                    Negocios Asociados
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ Route('admin.users.destroy', $user->id) }}" method="POST"
                                                    id="form-delete-user-{{ $user->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" data-form="form-delete-user-{{ $user->id }}"
                                                        data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                                        class="buttonDelete flex w-full items-center gap-1 rounded-lg px-2 py-2 text-red-500 hover:bg-red-100 dark:text-red-500 dark:hover:bg-red-950/30">
                                                        <x-icon icon="trash" class="h-4 w-4" />
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforeach
                </x-slot>
            </x-table>
        </div>

        <div id="new-user" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ Route('admin.users.store') }}" method="POST">
                    @csrf
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Nuevo usuario
                            </h3>
                            <button type="button"
                                class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-modal-hide="new-user">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4">
                            <div class="flex flex-col gap-4">
                                <x-input type="text" label="Nombre" placeholder="Nombre" placeholder="Nombre del usuario"
                                    name="name" icon="user" />
                                <x-input type="email" icon="email" label="Correo electrónico"
                                    placeholder="example@exam.com" name="email" />
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="password" placeholder="Contrasñea" label="Nueva contraseña"
                                                icon="lock" name="password" id="password" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="password" placeholder="Confirmar" label="Confirmar contraseña"
                                                icon="lock-check" name="confirm_password" id="confirm-password" />
                                        </div>
                                    </div>
                                    <div id="error-password" class="hidden">
                                        <span class="flex items-center gap-2 text-sm text-red-500">
                                            <x-icon icon="alert-circle" class="h-4 w-4" />
                                            Las contraseñas no coinciden
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <x-input type="radio" label="Administrador" name="role" value="admin" />
                                    <x-input type="radio" label="Negocio" name="role" value="business" />
                                    <x-input type="radio" label="Cajero" name="role" value="atm" />
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-modal-hide="new-user" />
                            <x-button type="submit" text="Guardar" icon="device-floppy" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="edit-user" tabindex="-1" aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
            <div class="relative max-h-full w-full max-w-xl p-4">
                <form action="{{ Route('admin.users.store') }}" method="POST" id="form-edit-user">
                    @csrf
                    @method('PUT')
                    <!-- Modal content -->
                    <div
                        class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Editar usuario
                            </h3>
                            <button type="button"
                                class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white"
                                data-target="#edit-user">
                                <x-icon icon="x" class="h-5 w-5" />
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="flex flex-col gap-4 p-4">
                            <x-input type="text" label="Nombre" placeholder="Nombre" placeholder="Nombre del usuario"
                                icon="user" required name="name" id="name" />
                            <x-input type="email" icon="email" label="Correo electrónico"
                                placeholder="example@exam.com" required name="email" id="email" />
                            <x-input type="checkbox" label="Cambiar contraseña" name="change_password"
                                id="change-password" />
                            <div id="change-password-container" class="hidden">
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        <div class="flex-1">
                                            <x-input type="password" placeholder="Nueva contraseña"
                                                label="Nueva contraseña" icon="lock" name="password"
                                                id="password-new" />
                                        </div>
                                        <div class="flex-1">
                                            <x-input type="password" placeholder="Confirmar" label="Confirmar contraseña"
                                                icon="lock-check" name="confirm_password" id="confirm-password-new" />
                                        </div>
                                    </div>
                                    <div id="error-password-new" class="hidden">
                                        <span class="flex items-center gap-2 text-sm text-red-500">
                                            <x-icon icon="alert-circle" class="h-4 w-4" />
                                            Las contraseñas no coinciden
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <x-input type="radio" label="Administrador" name="role" value="admin"
                                    id="admin" />
                                <x-input type="radio" label="Negocio" name="role" value="business"
                                    id="business" />
                                <x-input type="radio" label="Cajero" name="role" value="atm" id="atm" />
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800 md:p-5">
                            <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                                data-target="#edit-user" class="hide-modal" />
                            <x-button type="submit" text="Editar" icon="pencil" typeButton="primary" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el usuario"
            message="No podrás recuperar este registro" />
    </section>
@endsection
    