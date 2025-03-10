@extends('layouts.template')
@section('title', 'Iniciar sesión')
@section('content')
    <section class="flex h-screen w-full items-center justify-center">
        <div class="w-[420px] rounded-lg border bg-white dark:bg-black shadow-lg p-6">
            <div class="overflow-hidden rounded-full">
                <img src="{{ asset('images/only-icon.png') }}" alt="Logo Konverza" class="mx-auto w-32 object-cover">
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
                    Facturación Konverza
                </h1>
                <p class="mb-4 text-base text-gray-600 dark:text-gray-300">Ingresa tus datos para continuar</p>
            </div>
            <form action="{{ Route('validate') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <x-input type="email" name="email" icon="email" placeholder="Ingresar tu correo electrónico"
                    label="Correo electrónico" value="{{ old('email') }}" />
                <x-input type="password" name="password" icon="lock" placeholder="Ingresar tu contraseña"
                    label="Contraseña" />
                <div class="flex items-center justify-between">
                    <div>
                        <x-input type="checkbox" name="remember" id="remember" label="Recuerdáme" />
                    </div>
                    <div>
                        <a href="{{ Route('reset-password') }}"
                            class="text-sm font-semibold text-primary-500 hover:underline dark:text-primary-300">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Iniciar sesión" icon="login" />
                </div>
            </form>
        </div>
    </section>
@endsection
