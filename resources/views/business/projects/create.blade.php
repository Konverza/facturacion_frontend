@extends('layouts.auth-template')
@section('title', 'Nuevo proyecto')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">Nuevo proyecto</h1>
            <x-button type="a" href="{{ Route('business.projects.index') }}" typeButton="secondary" text="Volver"
                icon="arrow-left" class="w-full sm:w-auto" />
        </div>

        <form action="{{ Route('business.projects.store') }}" method="POST"
            class="mt-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800">
            @csrf
            <x-input type="text" name="name" id="project_name" label="Nombre del proyecto"
                placeholder="Ej. Remodelación sucursal centro" value="{{ old('name') }}" required />

            <div class="mt-4 flex justify-end">
                <x-button type="submit" typeButton="primary" text="Crear proyecto" icon="plus"
                    class="w-full sm:w-auto" />
            </div>
        </form>
    </section>
@endsection
