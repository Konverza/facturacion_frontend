@extends('layouts.auth-template')
@section('title', 'Editar cotizacion')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">{{ $pageTitle }}</h1>
            <x-button type="a" href="{{ Route('business.quotations.show', $quotation->id) }}" typeButton="secondary"
                text="Volver" icon="arrow-left" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4">
            @include('business.quotations._form', [
                'formAction' => Route('business.quotations.update', $quotation->id),
                'method' => 'PUT',
            ])
        </div>
    </section>

    @include('layouts.partials.business.dte.modals.modal-select-customer')
    @include('layouts.partials.business.dte.modals.modal-select-product')
    @include('layouts.partials.business.dte.drawer-new-product')
    @include('layouts.partials.business.dte.modals.modal-add-discount')
@endsection

@push('scripts')
    @vite('resources/js/dte.js')
    <script>
        $(document).on('click', '.selected-customer', function () {
            const url = $(this).data('url');
            const id = (url || '').split('/').pop();
            if (id) {
                $('#customer_id').val(id);
            }
        });
    </script>
@endpush
