@extends('layouts.auth-template')
@section('title', 'Negocios')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Configuraciones
            </h1>
        </div>
        <form action="">
            <div class="mt-4 flex flex-col gap-4 lg:flex-row">
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                        Conexión con Ministerio de Hacienda
                    </h2>
                    <div class="mt-2 flex flex-col gap-4">
                        <x-input type="text" label="URL de autenticación" name="" id="" icon="link"
                            placeholder="https://" />
                        <x-input type="text" label="URL de recepción" icon="link" name="" id=""
                            placeholder="https://" />
                        <x-input type="text" label="URL de consultas" icon="link" name="" id=""
                            placeholder="https://" />
                        <x-input type="text" label="URL de contingencia" icon="link" name="" id=""
                            placeholder="https://" />
                        <x-input type="text" label="URL para anulación" icon="link" name="" id=""
                            placeholder="https://" />
                    </div>
                </div>
                <div class="h-max flex-1">
                    <h2 class="text-lg font-semibold text-primary-500 dark:text-primary-300">
                        Conexión con servicios internos
                    </h2>
                    <div class="mt-2 flex flex-col gap-4">
                        <x-input type="text" label="API" name="" id="" icon="api" />
                        <x-input type="text" label="Firmador" name="" id="" />
                        <x-input type="text" label="Generador de PDFs" icon="pdf" name="" id="" />
                    </div>
                </div>
            </div>
            <x-button type="submit" typeButton="primary" text="Guardar configuraciones"
                class="mx-auto mt-4 w-full sm:w-auto" icon="settings" />
        </form>
    </section>
@endsection
