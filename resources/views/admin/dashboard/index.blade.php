@extends('layouts.auth-template')
@section('title', 'Dashboard')
@section('content')
    <section class="my-4 px-4 pb-4">
        <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
            Facturación electrónica
        </h1>
        <div class="flex flex-col flex-wrap gap-4 md:flex-row">
            <div class="flex flex-1 flex-col">
                <div class="mt-4 flex flex-col gap-4 lg:flex-row">
                    <div
                        class="flex w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-6 dark:border-gray-800">
                        <span class="rounded-full bg-green-100 p-4 dark:bg-green-950/30">
                            <x-icon icon="user" class="size-12 text-green-500 sm:size-14 md:size-16" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-4xl font-bold text-green-500">
                                {{ $customers->count() }}
                            </p>
                            <h1 class="text-gray-500 dark:text-gray-300">
                                Clientes activos
                            </h1>
                            <x-button type="a" href="{{ Route('admin.business.index') }}" typeButton="success" text="Ver clientes" size="normal" />
                        </div>
                    </div>
                    {{-- <div
                        class="flex flex-[2] items-center justify-center gap-4 rounded-lg border border-gray-300 p-6 dark:border-gray-800">
                        <span class="rounded-full bg-yellow-100 p-4 dark:bg-yellow-950/30">
                            <x-icon icon="moneybag" class="size-12 text-yellow-400 sm:size-14 md:size-16" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-2xl font-bold text-yellow-400 sm:text-3xl md:text-4xl">
                                ${{ number_format($octopus_statistics['total_facturado'], 2) }}
                            </p>
                            <h1 class="text-gray-500 dark:text-gray-300">
                                Ventas registradas
                            </h1>
                            <x-button type="submit" typeButton="warning" text="Ver ventas" size="normal" />
                        </div>
                    </div> --}}
                </div>
                <div class="mt-4 flex flex-wrap gap-4">
                    <div
                        class="flex w-max flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-6 dark:border-gray-800">
                        <span class="rounded-full bg-blue-100 p-4 dark:bg-blue-950/30">
                            <x-icon icon="file" class="size-12 text-blue-500 sm:size-14 md:size-16" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-4xl font-bold text-blue-500">
                                {{ $octopus_statistics['total'] }}
                            </p>
                            <h1 class="text-gray-500 dark:text-gray-300">
                                DTEs emitidos
                            </h1>
                            <x-button type="submit" typeButton="info" text="Ver todos" size="normal" />
                        </div>
                    </div>
                    {{-- <div
                        class="flex w-max flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-6 dark:border-gray-800">
                        <span class="rounded-full bg-red-100 p-4 dark:bg-red-950/30">
                            <x-icon icon="chartline" class="size-12 text-red-500 sm:size-14 md:size-16" />
                        </span>
                        <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                            <p class="text-2xl font-bold text-red-500">
                                $0.00
                            </p>
                            <h1 class="text-gray-500 dark:text-gray-300">
                                Ingreso por planes
                            </h1>
                            <x-button type="submit" typeButton="danger" text="Ver planes" size="normal" />
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="flex-1">
                <div class="mt-4 rounded-lg border border-gray-300 p-6 dark:border-gray-800">
                    <h2 class="text-xl font-bold text-gray-600 dark:text-white sm:text-2xl">
                        Estado Ministerio de Hacienda
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <p class="text-gray-600 dark:text-gray-300">Firmador</p>
                            </div>
                            <div class="flex-1">
                                <span
                                    class="flex w-max items-center gap-2 rounded-full px-2 py-1 text-sm font-semibold text-green-500">
                                    <x-icon icon="circle-check" class="size-5 text-green-500" />
                                    OK
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <p class="text-gray-600 dark:text-gray-300">
                                    API Local
                                </p>
                            </div>
                            <div class="flex-1">
                                <span
                                    class="flex w-max items-center gap-2 rounded-full px-2 py-1 text-sm font-semibold text-green-500">
                                    <x-icon icon="circle-check" class="size-5 text-green-500" />
                                    OK
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <p class="text-gray-600 dark:text-gray-300">
                                    API MH
                                </p>
                            </div>
                            <div class="flex-1">
                                <span
                                    class="flex w-max items-center gap-2 rounded-full px-2 py-1 text-sm font-semibold text-green-500">
                                    <x-icon icon="circle-check" class="size-5 text-green-500" />
                                    OK
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <p class="text-gray-600 dark:text-gray-300">
                                    Almacenamiento
                                </p>
                            </div>
                            <div class="flex-1">
                                <span
                                    class="flex w-max items-center gap-2 rounded-full px-2 py-1 text-sm font-semibold text-green-500">
                                    <x-icon icon="circle-check" class="size-5 text-green-500" />
                                    OK
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <h2 class="text-xl font-bold text-gray-600 dark:text-white sm:text-2xl">
                Últimos clientes registrados
            </h2>
            <div class="mt-2">
                <x-table id="table-business">
                    <x-slot name="thead">
                        <x-tr>
                            <x-th class="w-10">#</x-th>
                            <x-th>Negocio</x-th>
                            <x-th :last="true">Plan contratado</x-th>
                        </x-tr>
                    </x-slot>
                    <x-slot name="tbody">
                        @foreach ($business as $busines)
                            <x-tr>
                                <x-td>{{ $loop->iteration }}</x-td>
                                <x-td>{{ $busines->nombre }}</x-td>
                                <x-td>
                                    <span
                                        class="flex w-max items-center gap-1 rounded-full px-2 py-1 text-sm font-bold text-primary-500 dark:text-primary-300">
                                        <x-icon icon="star" class="size-5 text-current" />
                                        {{ $busines->plan->nombre }}
                                    </span>
                                </x-td>
                            </x-tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </div>
        </div>
    </section>
@endsection
