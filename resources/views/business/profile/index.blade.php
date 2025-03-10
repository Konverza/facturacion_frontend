@extends('layouts.auth-template')
@section('title', 'Perfil')
@section('content')
    <section class="mt-4 px-4 pb-4">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="-mb-px flex flex-nowrap overflow-x-auto text-center text-sm font-medium" id="default-styled-tab"
                data-tabs-toggle="#default-styled-tab-content"
                data-tabs-active-classes="text-primary-500 hover:text-primary-600 dark:text-primary-300 dark:hover:text-primary-400 border-primary-500 dark:border-primary-300"
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                role="tablist">
                <li class="me-2" role="presentation">
                    <button
                        class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                        id="perfil-empresa-styled-tab" data-tabs-target="#styled-perfil-empresa" type="button"
                        role="tab" aria-controls="perfil-empresa" aria-selected="false">
                        Perfil de la empresa
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button
                        class="inline-block text-nowrap rounded-t-lg border-b-2 p-4 hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300"
                        id="mi-perfil-styled-tab" data-tabs-target="#styled-mi-perfil" type="button" role="tab"
                        aria-controls="mi-perfil" aria-selected="false">
                        Mi perfil
                    </button>
                </li>
            </ul>
        </div>
        <div id="default-styled-tab-content">
            <div class="hidden" id="styled-perfil-empresa" role="tabpanel" aria-labelledby="perfil-empresa-tab">
                <h1
                    class="mb-4 text-lg font-bold text-primary-500 dark:text-primary-300 sm:text-xl md:text-2xl lg:text-3xl">
                    Perfil de la empresa
                </h1>
                <form action="{{ Route('business.datos-empresa.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $datos_empresa['id'] }}" />
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between md:space-x-4">
                        <div class="flex flex-1 flex-col items-center">
                            <div
                                class="group relative mx-auto h-32 w-32 overflow-hidden rounded-full border border-gray-300 dark:border-gray-800 md:mx-0 md:mr-4">
                                <img src="{{ $logo ? $logo['url'] : asset('images/only-icon.png') }}" alt="Profile"
                                    class="h-full w-full bg-white object-contain p-4" id="logo-preview">
                                <label for="logo"
                                    class="absolute bottom-0 right-0 hidden h-full w-full cursor-pointer items-center justify-center rounded-full bg-gray-200/50 p-1 group-hover:flex dark:bg-gray-900/50">
                                    <input type="file" id="logo" name="logo" class="hidden">
                                    <span class="text-xs text-gray-800 dark:text-gray-100">
                                        <x-icon icon="pencil" class="size-5" />
                                    </span>
                                </label>
                            </div>
                            <h2 class="mt-4 text-center text-sm font-bold uppercase text-gray-800 dark:text-gray-100">
                                Logo
                            </h2>
                        </div>
                        <div class="flex-[4]">
                            <x-input type="text" name="nombre" label="Nombre" value="{{ $datos_empresa['nombre'] }}"
                                id="nombre" />
                            <div class="mt-4 flex flex-col items-center gap-4 sm:flex-row">
                                <div class="w-full sm:flex-1">
                                    <x-input type="text" name="nit" label="NIT"
                                        value="{{ $datos_empresa['nit'] }}" id="nit" />
                                </div>
                                <div class="w-full sm:flex-1">
                                    <x-input type="text" name="nrc" label="NRC"
                                        value="{{ $datos_empresa['nrc'] }}" id="nrc" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-select name="actividad_economica" label="Actividad económica" id="codigo_actividad"
                                    :options="$actividades_economicas" value="{{ $datos_empresa['codActividad'] }}"
                                    selected="{{ $datos_empresa['codActividad'] }}" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex flex-col items-center gap-4 md:flex-row">
                            <div class="w-full md:flex-[2]">
                                <x-input type="text" name="nombre_comercial" label="Nombre comercial"
                                    value="{{ $datos_empresa['nombreComercial'] }}" id="nombre_comercial" />
                            </div>
                            <div class="w-full sm:flex-1">
                                <x-select name="tipo_establecimiento" label="Tipo de establecimiento"
                                    id="tipo_establecimiento" :options="$tipos_establecimientos"
                                    value="{{ $datos_empresa['tipoEstablecimiento'] }}"
                                    selected="{{ $datos_empresa['tipoEstablecimiento'] }}" :search="false" />
                            </div>
                            <div class="w-full sm:flex-1">
                                <x-input type="text" name="codigo_establecimiento" label="Código de establecimiento"
                                    value="{{ $datos_empresa['codEstable'] }}" id="codigo_establecimiento" />
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="text" label="Código establecimiento (Ministerio de Hacienda)"
                                    name="codigo_establecimiento_mh" value="{{ $datos_empresa['codEstableMH'] }}" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" label="Código punto de venta" name="codigo_punto_venta"
                                    placeholder="01" value="01" value="{{ $datos_empresa['codPuntoVenta'] }}" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" label="Código punto de venta (Ministerio de Hacienda)"
                                    name="codigo_punto_venta_mh" value="{{ $datos_empresa['codPuntoVentaMH'] }}" />
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-select name="department" label="Departamento" id="departamento" name="departamento"
                                    required :options="$departamentos" value="{{ $datos_empresa['departamento'] }}"
                                    selected="{{ $datos_empresa['departamento'] }}"
                                    data-action="{{ Route('business.get-municipios') }}" />
                            </div>
                            <div class="flex-1" id="select-municipio">
                                <x-select name="municipio" label="Municipio" id="municipio" required :options="$municipios ?? [
                                    'Seleccione un departamento' => 'Seleccione un departamento',
                                ]"
                                    selected="{{ $datos_empresa['municipio'] }}"
                                    value="{{ $datos_empresa['municipio'] }}" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input type="textarea" name="complemento" label="Complemento"
                                value="{{ $datos_empresa['complemento'] }}" id="complemento" />
                        </div>
                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <div class="flex-1">
                                <x-input type="email" name="correo" label="Correo electrónico"
                                    value="{{ $datos_empresa['correo'] }}" id="correo" icon="email" />
                            </div>
                            <div class="flex-1">
                                <x-input type="text" name="telefono" label="Teléfono"
                                    value="{{ $datos_empresa['telefono'] }}" id="telefono" icon="phone" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-center">
                        <x-button type="submit" typeButton="primary" text="Guardar cambios" icon="save" />
                    </div>
                </form>
            </div>
            <div class="hidden" id="styled-mi-perfil" role="tabpanel" aria-labelledby="mi-perfil-tab">
                <h1
                    class="mb-4 text-lg font-bold text-primary-500 dark:text-primary-300 sm:text-xl md:text-2xl lg:text-3xl">
                    Mi perfil
                </h1>
                <div class="mt-4">
                    <x-input type="email" name="correo" label="Correo electrónico" value="{{ $user->email }}"
                        id="correo" icon="email" />
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <p
                        class="block overflow-hidden truncate whitespace-nowrap text-wrap text-sm font-medium text-gray-500 dark:text-gray-300">
                        Contraseña:
                    </p>
                    <div class="flex items-center gap-1.5">
                        @for ($i = 0; $i < 10; $i++)
                            <div class="size-1.5 rounded-full bg-gray-800 dark:bg-gray-400"></div>
                        @endfor
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-start w-full">
                    <x-button type="a" href="{{ Route('reset-password') }}" typeButton="secondary"
                        text="Cambiar contraseña" icon="lock" class="w-full sm:w-max" />
                </div>
            </div>
        </div>

    </section>
@endsection
