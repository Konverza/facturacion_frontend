<ol class="relative border-s border-gray-200 dark:border-gray-700">
    @foreach ($movimientos as $movimiento)
        <li class="mb-4 ms-4">
            <div
                class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full border border-gray-400 bg-gray-400 dark:border-gray-900 dark:bg-gray-700">
            </div>
            <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                {{ $movimiento->created_at->format('d/m/Y') }} <span class="text-gray-500 dark:text-gray-400">
                    a las</span>
                {{ $movimiento->created_at->format('H:i A') }}
            </time>
            <h3 class="text-base font-semibold uppercase text-gray-900 dark:text-white">
                {{ $movimiento->tipo }}
            </h3>
            @if ($movimiento->numero_factura)
                <p class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                    Número de factura: {{ $movimiento->numero_factura ?? '' }}
                </p>
            @endif
            <p class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                {{ $movimiento->observaciones ?? 'Sin observaciones' }}
            </p>
            <div class="flex items-center justify-start">
                <x-button type="a" href="{{ $movimiento->invoice['enlace_pdf'] }}" typeButton="secondary"
                    text="Ver factura" icon="pdf" size="small" />
            </div>
        </li>
    @endforeach
</ol>
