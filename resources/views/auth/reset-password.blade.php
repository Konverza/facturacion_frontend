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
                <p class="mb-4 text-base text-gray-600 dark:text-gray-300">
                    Ingresa tu correo electrónico para restablecer tu contraseña
                </p>
            </div>
            <form action="{{ Route("send-email-reset-password") }}" method="POST">
                @csrf
                <x-input type="text" name="email" icon="email" placeholder="Ingresar tu correo electrónico"
                    label="Correo electrónico" />
                <div class="mt-6 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" text="Enviar correo" icon="email-forward" />
                </div>
            </form>
        </div>
    </section>
@endsection
