@extends('layouts.auth-template')
@section('title', 'Editar Anuncio')
@section('content')
    <section class="my-4 px-4 sm:px-6">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Editar Anuncio
            </h1>
            <a href="{{ Route('admin.ads.index') }}" class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="mt-1">
            <form class="mt-4 flex flex-col pb-4" action="{{ Route('admin.ads.update', $ad->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-input type="text" name="name" label="Nombre del anuncio" class="w-full mb-3" required value="{{ $ad->name }}" />
                <x-input type="text" name="link_url" label="URL del enlace" class="w-full mb-3" required value="{{ $ad->link_url }}" />
                <img src="{{ $ad->image_path }}" alt="Current Banner" class="mb-3" />
                <x-input type="file" label="Imagen de Banner" name="image_file" id="image_file"
                    accept=".png, .jpg, .jpeg, .webp" maxSize="3072" class="mb-3" />
                <x-button type="submit" typeButton="primary" text="Actualizar Anuncio" class="mt-3" />
            </form>
        </div>
    </section>
@endsection
