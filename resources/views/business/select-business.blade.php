@extends('layouts.template')
@section('title', 'Selecciona un negocio')
@section('content')
    <section class="flex h-screen w-full flex-col items-center justify-center">
        <h1 class="text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
            Selecciona un negocio
        </h1>
        <p class="mt-2 text-center text-gray-500 dark:text-gray-400">
            Tienes más de un negocio asociado a tu cuenta, selecciona uno para continuar
        </p>
        <div class="mt-4 flex flex-col gap-4">
            @foreach ($businesses as $business)
                <form action="{{ Route('business.select') }}" method="POST">
                    @csrf
                    <input type="hidden" name="business" value="{{ $business->id }}" />
                    <button type="submit"
                        class="w-96 rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                        <div class="flex justify-start gap-4">
                            <img src="{{ asset('images/only-icon.png') }}" class="size-12 rounded-full object-cover"
                                alt="logo empresa" />
                            <div class="flex flex-col items-start">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-300">
                                    {{ $business->nombre }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    NIT: {{ $business->nit }}
                                </p>
                                <p class="text-wrap text-left text-sm text-gray-500 dark:text-gray-400">
                                    Correo responsable: {{ $business->correo_responsable }}
                                </p>
                            </div>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    </section>
@endsection
