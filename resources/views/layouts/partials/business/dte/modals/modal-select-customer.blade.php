<!-- Modal selected customers -->
<div id="selected-customer" tabindex="-1" aria-hidden="true"
    class="fixed left-0 right-0 top-0 z-[100] hidden h-full max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-200/50 dark:bg-gray-900/50 md:inset-0">
    <div class="relative max-h-full w-full max-w-[750px] m-4">
        <!-- Modal content -->
        <div class="motion-preset-expand relative rounded-lg bg-white shadow motion-duration-300 dark:bg-gray-950">
            <div class="flex flex-col">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between rounded-t border-b border-gray-300 p-4 dark:border-gray-800">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Seleccionar cliente
                    </h3>
                    <button type="button"
                        class="hide-modal ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-900 dark:hover:text-white" data-target="#selected-customer">
                        <x-icon icon="x" class="h-5 w-5" />
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4">
                    <div class="mb-4">
                        <x-input type="text" id="input-search-customers" placeholder="Buscar cliente" icon="search"
                        class="w-full" />
                    </div>
                    <x-table id="table-customers">
                        <x-slot name="thead">
                            <x-tr>
                                <x-th class="w-10">#</x-th>
                                <x-th>Identificación</x-th>
                                <x-th>Nombre</x-th>
                                <x-th :last="true"></x-th>
                            </x-tr>
                        </x-slot>
                        <x-slot name="tbody">
                            @foreach ($business_customers as $customer)
                                <x-tr>
                                    <x-td>{{ $loop->iteration }}</x-td>
                                    <x-td>
                                        {{ $customer->numDocumento }}
                                    </x-td>
                                    <x-td>
                                        {{ $customer->nombre }}
                                    </x-td>
                                    <x-td :last="true">
                                        <x-button type="button" icon="arrow-next" size="small"
                                            typeButton="secondary" text="Seleccionar" class="selected-customer"
                                            data-url="{{ Route('business.customers.show', $customer->id) }}" />
                                    </x-td>
                                </x-tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end gap-4 rounded-b border-t border-gray-300 p-4 dark:border-gray-800">
                    <x-button type="button" text="Cancelar" icon="x" typeButton="secondary"
                        class="hide-modal" data-target="#selected-customer" />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal selected customers -->
