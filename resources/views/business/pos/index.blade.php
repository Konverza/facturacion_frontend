@extends('layouts.auth-template')
@section('title', 'Punto de Venta')
@section('content')
    <section class="my-4 px-4">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl md:text-4xl">
                Punto de Venta
            </h1>
            <a href="{{ Route('business.dashboard') }}"
                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                <x-icon icon="arrow-back" class="size-5" />
                Regresar
            </a>
        </div>
        <div class="flex w-full flex-col sm:flex-row gap-2">
            <div
                class="flex-[3] mt-4 flex-row gap-2 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                <form method="get" action="{{ Route('business.pos.index') }}">
                    <div class="mb-4 flex w-full flex-col gap-4 sm:flex-row">
                        <div class="flex-[6]">
                            <x-input type="text" placeholder="Buscar producto por nombre o código" class="w-full"
                                icon="search" name="search" required value="{{ $search }}" />
                        </div>
                        <div class="flex-1">
                            <button type="submit"
                                class="bg-green-500 text-white hover:bg-green-600 dark:bg-green-500 dark:text-white dark:hover:bg-green-600 font-medium rounded-lg flex items-center justify-center gap-1 transition-colors duration-300 text-nowrap  px-4 py-2.5 w-full">
                                <span class="text-sm">Buscar</span>
                            </button>
                        </div>
                        <div class="flex-1">
                            <x-button type="a" href="{{ Route('business.pos.index') }}" typeButton="primary"
                                class="w-full sm:w-auto" text="Limpiar" />
                        </div>
                    </div>
                </form>
                @if ($categories->count() > 0)
                    <div class="flex w-full items-center justify-between border-b border-gray-300 p-4 dark:border-gray-800">
                        @if ($category)
                            <h1 class="text-xl font-bold text-primary-500 dark:text-primary-300 sm:text-2xl md:text-3xl">
                                {{ $category->name }}
                            </h1>
                            @if ($category->parent_id)
                                <a href="{{ Route('business.pos.index', ['category' => $category->parent_id]) }}"
                                    class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                    <x-icon icon="arrow-back" class="size-5" />
                                    Regresar
                                </a>
                            @else
                                <a href="{{ Route('business.pos.index') }}"
                                    class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                    <x-icon icon="arrow-back" class="size-5" />
                                    Regresar
                                </a>
                            @endif
                        @else
                            <h1 class="text-xl font-bold text-primary-500 dark:text-primary-300 sm:text-2xl md:text-3xl">
                                Categorías
                            </h1>
                        @endif
                    </div>
                @endif
                <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse ($categories as $category)
                        <a href="{{ Route('business.pos.index', ['category' => $category->id]) }}"
                            class="flex flex-col w-full flex-1 items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                            <div class="w-100 mt-2 flex justify-center">
                                <div class="group relative mx-auto h-32 w-32 overflow-hidden md:mx-0 md:mr-4">
                                    <img src="{{ $category->image_url ?: asset('images/only-icon.png') }}"
                                        alt="{{ $category->name }}" class="h-full w-full bg-white object-contain p-4">
                                </div>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-1 sm:items-start">
                                <p class="text-2xl text-center font-bold text-blue-500">
                                    {{ $category->name }}
                                </p>
                            </div>
                        </a>
                    @empty
                    @endforelse
                </div>
                @if ($products->count() > 0)
                    <div
                        class="flex w-full items-center justify-between  border-b border-gray-300 p-4 dark:border-gray-800">
                        <h1 class="mt-4 text-xl font-bold text-primary-500 dark:text-primary-300 sm:text-2xl md:text-3xl">
                            Productos
                        </h1>
                        @if ($category && $category->parent_id)
                            <a href="{{ Route('business.pos.index', ['category' => $category->parent_id]) }}"
                                class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                <x-icon icon="arrow-back" class="size-5" />
                                Regresar
                            </a>
                        @endif
                    </div>
                @endif
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {{-- products list --}}
                    @forelse($products as $product)
                        <x-item-card :id="$product->id" :image_url="$product->image_url" :descripcion="$product->descripcion" :codigo="$product->codigo"
                            :precio="$product->precioUni" :stockActual="$product->stockActual" :hasStock="$product->has_stock" />
                    @empty
                        <div
                            class="my-4 md:col-span-2 lg:col-span-3 xl:col-span-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
                            No se han encontrado productos para esta categoría. <a href="{{ Route('business.pos.index') }}"
                                class="text-yellow-500 underline">Ver todos los productos</a>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="flex-1 mt-4 flex flex-col sm:flex-row top-0">
                <div
                    class="relative border w-full border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-950 rounded-lg p-4">
                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100">Esta venta:</p>
                    <div class="flex flex-col gap-2 mt-4">
                        <form action="{{ Route('business.dte.factura') }}" method="POST">
                            @csrf
                            <div class="flex flex-col gap-2">
                                @php
                                    $products = session('dte.products') ?? [];
                                @endphp
                                @forelse ($products as $product)
                                    <dl class="flex items-center justify-between gap-4 w-full">
                                        <dt
                                            class="flex flex-row items-center text-base font-normal text-gray-500 dark:text-gray-300 gap-2">
                                            <span>{{ $product['cantidad'] }}&times; </span>
                                            <span>{{ $product['descripcion'] }}</span>
                                        </dt>
                                        <dd
                                            class="text-base items-center font-medium text-gray-900 dark:text-gray-100 flex flex-row gap-2">
                                            ${{ number_format($product['total'], 2) }}
                                            <x-button type="a" icon="trash"
                                                href="{{ Route('business.dte.product.delete', ['id' => $product['id'], 'from_pos' => true]) }}"
                                                typeButton="danger" onlyIcon="true" class="btn-delete" />
                                        </dd>
                                    </dl>
                                @empty
                                    <div
                                        class="flex w-full items-center justify-center gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950">
                                        <p class="text-center text-gray-500">No hay productos seleccionados</p>
                                    </div>
                                @endforelse
                                @if (session('dte.total'))
                                    <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2">
                                        <dt class="text-base font-bold text-gray-900 ">Total</dt>
                                        <dd class="text-base font-bold text-gray-900" id="totalVenta">
                                            ${{ number_format(session('dte.total'), 2) }}</dd>
                                    </dl>
                                    <input type="hidden" name="omitir_datos_receptor" value="1" />
                                    <input type="hidden" name="condicion_operacion" value="1" />
                                    <input type="hidden" name="pos_id" value="{{ $default_pos->id ?? '' }}">
                                    <x-button type="submit" typeButton="primary" text="Finalizar Venta (Ticket)"
                                        class="w-full sm:w-auto" icon="file-symlink" name="action" value="generate" />
                                    <x-button type="button" typeButton="danger" text="Cancelar documento" icon="cancel"
                                        class="show-modal w-full sm:w-auto" data-target="#cancel-dte" />
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.partials.business.dte.modals.modal-cancel-dte')
    </section>
    @push('scripts')
        <script>
            const incrementButtons = document.querySelectorAll('.increment-button');
            const decrementButtons = document.querySelectorAll('.decrement-button');
            const quantityInputs = Array.from(document.querySelectorAll('.quantity-input'));
            const deleteButtons = document.querySelectorAll('.btn-delete');

            incrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const input = quantityInputs.find(input => input.getAttribute('data-id') ===
                        id);
                    input.value = parseInt(input.value) + 1;
                });
            });

            decrementButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const input = quantityInputs.find(input => input.getAttribute('data-id') ===
                        id);
                    if (parseInt(input.value) > 1) {
                        input.value = parseInt(input.value) - 1;
                    }
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch(this.getAttribute('href'), {
                        method: 'GET',
                    }).then(response => {
                        console.log(response);
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error al eliminar el producto');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
