@extends('layouts.template')
@section('title', 'Restablecer contraseña')
@section('content')
    <section class="flex h-screen w-full items-center justify-center">
        <div class="w-[420px] rounded-lg p-6">
            <div class="overflow-hidden rounded-full">
                <img src="{{ asset('images/only-icon.png') }}" alt="Logo Konverza" class="mx-auto w-32 object-cover">
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
                    Restablecer contraseña
                </h1>
            </div>
            <form action="{{ Route('password.change.post') }}" method="POST" id="resetPasswordForm">
                @csrf
                <input type="hidden" name="token_password" value="{{ $token }}">
                <div class="flex flex-col gap-2">
                    <x-input type="password" id="new-password" name="new_password" label="Nueva contraseña"
                        placeholder="Ingresa la nueva contraseña" class="text-zinc-800" icon="lock" />
                    <div id="password-strength-container" class="mt-2 h-2 w-full rounded bg-gray-200">
                        <div id="password-strength-bar" class="h-full w-0 rounded bg-red-500"></div>
                    </div>
                    <ul id="password-requirements" class="mt-2 list-disc pl-5 text-sm text-red-500"></ul>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    <x-input type="password" id="confirm-password" name="confirm_password" label="Confirma contraseña"
                        icon="lock-check" placeholder="Confirma la contraseña" class="text-zinc-800" />
                    <p id="password-match-message" class="text-sm text-red-500"></p>
                </div>
                <div class="mt-4 flex flex-col items-center justify-center gap-4">
                    <x-button type="button" text="Reestablecer contraseña" typeButton="primary" class="mt-4"
                        id="resetPasswordButton" />
                </div>
            </form>
        </div>

        <div class="confirmPasswordModal fixed inset-0 z-50 hidden items-center justify-center bg-zinc-800 bg-opacity-75 transition-opacity"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="inline-block transform animate-jump-in overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all animate-duration-300 animate-once sm:my-8 sm:w-full sm:max-w-lg sm:align-middle"
                    role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-12">
                                <x-icon icon="circle-check" class="h-6 w-6 text-green-600" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-medium leading-6 text-primary-500 dark:text-primary-300"
                                    id="modal-headline">
                                    Contraseña cambiada
                                </h3>
                                <div class="mt-2">
                                    <p class="font-dine-r text-sm text-gray-500">
                                        La contraseña se ha actualizado correctamente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 bg-gray-50 px-4 py-3">
                        <x-button type="a" href="{{ Route('login') }}" text="Aceptar" icon="check"
                            class="confirmDelete w-max text-sm" typeButton="primary" />
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @vite('resources/js/password.js')
@endpush
