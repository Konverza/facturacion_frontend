@extends('layouts.auth-template')
@section('title', 'Anuncios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Anuncios
            </h1>
        </div>
        <div class="mt-4 pb-4">
            <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                <div class="flex-[4]">
                    <x-input type="text" placeholder="Buscar anuncio" class="w-full" icon="search" id="input-search-ad" />
                </div>
                <div class="flex-1">
                    <x-button type="a" href="{{ route('admin.ads.create') }}" icon="plus" typeButton="primary"
                        text="Nuevo anuncio" />
                </div>
            </div>
            <x-table id="table-data">
                <x-slot name="thead">
                    <x-tr>
                        <x-th class="w-10">#</x-th>
                        <x-th>Anuncio</x-th>
                        <x-th>Imagen</x-th>
                        <x-th :last="true">Accciones</x-th>
                    </x-tr>
                </x-slot>
                <x-slot name="tbody">
                    @foreach ($ads as $ad)
                        <x-tr>
                            <x-td>{{ $loop->iteration }}</x-td>
                            <x-td>
                                {{ $ad->name }}<br>
                                Link: {{ $ad->link_url }}
                            </x-td>
                            <x-td>
                                <img src="{{ asset($ad->image_path) }}" alt="{{ $ad->name }}" class="h-16 object-cover">
                            </x-td>
                            <x-td th :last="true">
                                <div class="relative">
                                    <x-button type="button" icon="arrow-down" typeButton="primary" text="Acciones"
                                        class="show-options" data-target="#options-business-{{ $ad->id }}"
                                        size="small" />
                                    <div class="options absolute right-0 top-0 z-10 mt-1 hidden w-max rounded-lg border border-gray-300 bg-white p-2 dark:border-gray-800 dark:bg-gray-950"
                                        id="options-business-{{ $ad->id }}">
                                        <ul class="flex flex-col text-xs">
                                            <li>
                                                <a href="{{ Route('admin.ads.edit', $ad->id) }}"
                                                    class="flex w-full items-center gap-1 rounded-lg px-2 py-2 text-gray-600 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">
                                                    <x-icon icon="pencil" class="h-4 w-4" />
                                                    Editar
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ Route('admin.ads.destroy', $ad->id) }}" method="POST"
                                                    id="form-delete-ad-{{ $ad->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" data-form="form-delete-ad-{{ $ad->id }}"
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
        <div class="flex w-full justify-between mt-4">
            <h1 class="text-4xl font-bold text-primary-500 dark:text-primary-300">
                Vista Previa
            </h1>
        </div>

        <div class="my-2">
            <x-ad-carousel :ads="$ads" />
        </div>
        <hr>
        <x-delete-modal modalId="deleteModal" title="¿Estás seguro de eliminar el anuncio?"
            message="No podrás recuperar este registro" />
    </section>
@endsection
