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
            <p class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                Monto: ${{ number_format((float) $movimiento->monto, 2) }}
            </p>
            @if ($movimiento->numero_factura)
                <p class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                    Número de factura: {{ $movimiento->numero_factura ?? '' }}
                </p>
            @endif
            <p class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                {{ $movimiento->observaciones ?? 'Sin observaciones' }}
            </p>
            @if ($movimiento->numero_factura)
                <div class="flex items-center justify-start">
                    <button type="button"
                        data-url="{{ Route('business.cuentas-por-cobrar.invoice-link-cod', $movimiento->numero_factura) }}"
                        data-cod-generacion="{{ $movimiento->numero_factura }}"
                        class="btn-open-invoice inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">
                        <x-icon icon="pdf" class="h-4 w-4" />
                        Ver factura
                    </button>
                </div>
            @endif
        </li>
    @endforeach
</ol>
