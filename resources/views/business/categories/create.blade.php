@extends('layouts.auth-template')
@section('title', 'Nueva Categoría')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Nueva Categoría
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

            <form action="{{ Route('business.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="text" icon="address-book" required label="Nombre" name="name"
                            value="{{ old('name') }}" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-select id="parent_id" name="parent_id" :options="$categories"
                            value="{{ old('parent_id') }}" selected="{{ old('parent_id') }}"
                            label="Categoría Padre (opcional)" />
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <x-input type="file" label="Imagen de Categoría" name="image" id="image"
                                    accept=".png, .jpg, .jpeg, .webp" maxSize="3072" />
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-center">
                    <x-button type="submit" typeButton="primary" class="w-full sm:w-auto" text="Guardar Categoría"
                        icon="save" />
                </div>
            </form>
        </div>
    </section>
@endsection
