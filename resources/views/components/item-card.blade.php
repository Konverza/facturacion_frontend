@props(['id', 'descripcion', 'codigo', 'precio', 'stockActual', 'image_url' => null, 'has_stock' => null])
<form action="{{route("business.dte.product.store-pos")}}" method="post">
    @csrf
    <div
        class="flex flex-col w-full items-center justify-between gap-4 rounded-lg border border-gray-300 p-4 dark:border-gray-800 dark:bg-gray-950 min-h-[400px]">
        <div class="w-full mt-2 flex justify-center">
            <div class="group relative mx-auto h-32 w-32 overflow-hidden md:mx-0 md:mr-4">
                <img src="{{ $image_url ?: asset('images/only-icon.png') }}" alt="{{ $descripcion }}"
                    class="h-full w-full bg-white object-contain p-4">
            </div>
        </div>
        <div class="flex flex-col justify-center items-center gap-1">
            <span class="text-center text-black dark:text-white">{{ $descripcion }}</span>
            <small class="text-gray-800 dark:text-gray-200">Código: {{ $codigo }}</small>
        </div>
        <div class="flex flex-col justify-center items-center gap-1">
            <span class="text-center font-bold text-blue-600">${{ $precio }}</span>
            {{-- input group with plus and minus, and a input number --}}
            <div class="flex items-center justify-center w-full">
                <x-button type="button" typeButton="default" icon="minus" onlyIcon="true" :rounded="false"
                    class="w-10 h-10 rounded-l-lg decrement-button" data-id="{{ $id }}" />
                <input type="number" name="cantidad" value="1" data-id="{{ $id }}" inputmode="numeric"
                    class="w-16 border-t border-b border-gray-300 p-2 text-center dark:border-gray-800 dark:bg-gray-950 quantity-input"
                    min="1" {{ $has_stock ? "max=$stockActual" : "" }} />
                <x-button type="button" typeButton="default" icon="plus" onlyIcon="true" :rounded="false"
                    class="w-10 h-10 rounded-r-lg increment-button" data-id="{{ $id }}" />
                <input type="hidden" name="product_id" value="{{ $id }}" />
            </div>
            <x-button type="submit" typeButton="success" text="Añadir producto" icon="plus" class="w-full sm:w-auto"
                id="btn-add-product" />
        </div>
    </div>
</form>
