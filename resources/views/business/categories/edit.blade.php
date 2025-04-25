@extends('layouts.auth-template')
@section('title', 'Editar Categoría')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Editar Categoría
            </h1>
            <a href="{{ Route('business.products.index') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-4 rounded-lg pb-4">

            @if ($errors->any())
                <div class="border-l-4 border-red-500 bg-red-100 p-4 text-red-700" role="alert">
                    <p class="font-bold">Error</p>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ Route('business.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-[2]">
                        <x-input type="text" icon="address-book" required label="Nombre" name="name"
                            value="{{ old('name', $category->name) }}" />
                    </div>
                </div>
                <div class="w-100 mt-2 flex justify-center">
                    <div
                        class="group relative mx-auto h-32 w-32 overflow-hidden rounded-full border border-gray-300 dark:border-gray-800 md:mx-0 md:mr-4">
                        <img src="{{ $category->image_url ? $category->image_url : asset('images/only-icon.png') }}"
                            alt="Profile" class="h-full w-full bg-white object-contain p-4" id="logo-preview">
                        <label for="logo"
                            class="absolute bottom-0 right-0 hidden h-full w-full cursor-pointer items-center justify-center rounded-full bg-gray-200/50 p-1 group-hover:flex dark:bg-gray-900/50">
                            <input type="file" id="logo" name="image" class="hidden">
                            <span class="text-xs text-gray-800 dark:text-gray-100">
                                <x-icon icon="pencil" class="size-5" />
                            </span>
                        </label>
                    </div>
                </div>
                <h2 class="mt-4 text-center text-sm font-bold uppercase text-gray-800 dark:text-gray-100">
                    Logo
                </h2>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" class="w-full sm:w-auto" text="Guardar Categoría"
                        icon="save" />
                </div>
            </form>
        </div>
    </section>
@endsection
