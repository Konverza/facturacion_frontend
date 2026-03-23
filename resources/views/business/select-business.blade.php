@extends('layouts.template')
@section('title', 'Selecciona un negocio')
@section('content')
    <section class="flex w-full flex-col items-center justify-center mt-5">
        <h1 class="text-2xl font-bold uppercase text-primary-500 dark:text-primary-300">
            Selecciona un negocio
        </h1>
        <p class="mt-2 text-center text-gray-500 dark:text-gray-400">
            Tienes más de un negocio asociado a tu cuenta, selecciona uno para continuar
        </p>
        <div class="my-4 w-full px-4">
            <div class="grid justify-center gap-4 [grid-template-columns:repeat(auto-fit,minmax(24rem,24rem))]">
                @foreach ($businesses as $business)
                    <form action="{{ Route('business.select') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="business" value="{{ $business->id }}" />
                        <button type="submit"
                            class="flex h-full w-96 rounded-lg border border-gray-300 bg-gray-50 p-4 shadow-lg transition-colors hover:bg-gray-100 hover:shadow-xl dark:border-gray-900 dark:bg-gray-950 dark:hover:bg-gray-900/50">
                            <div class="flex w-full items-start justify-start gap-4">
                                <img src="{{ asset('images/only-icon.png') }}" class="size-12 rounded-full object-cover"
                                    alt="logo empresa" />
                                <div class="flex min-w-0 flex-col items-start">
                                    <h2 class="text-md font-bold text-gray-800 dark:text-gray-300">
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
        </div>
    </section>
@endsection
