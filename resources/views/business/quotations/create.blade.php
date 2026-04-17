@extends('layouts.auth-template')
@section('title', 'Crear cotizacion')

@section('content')
    <section class="my-4 px-4 pb-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-primary-500 dark:text-primary-300 sm:text-3xl">{{ $pageTitle }}</h1>
            <x-button type="a" href="{{ Route('business.quotations.index') }}" typeButton="secondary" text="Ver cotizaciones"
                icon="file-report" class="w-full sm:w-auto" />
        </div>

        <div class="mt-4">
            @include('business.quotations._form', [
                'formAction' => Route('business.quotations.store'),
                'method' => 'POST',
                'priceInputMode' => 'without_iva',
            ])
        </div>
    </section>

    @include('layouts.partials.business.dte.modals.modal-select-customer')
    @include('layouts.partials.business.dte.modals.modal-select-product', [
        'priceInputMode' => 'without_iva',
    ])
    @include('layouts.partials.business.dte.modals.modal-edit-product')
    @include('layouts.partials.business.dte.drawer-new-product', [
        'priceInputMode' => 'without_iva',
    ])
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

        function setFeedback(feedbackSelector, message, isError = false) {
            $(feedbackSelector)
                .text(message)
                .removeClass('text-red-500 text-green-600 dark:text-green-400 text-gray-500 dark:text-gray-400')
                .addClass(isError ? 'text-red-500' : 'text-green-600 dark:text-green-400');
        }

        function addOptionToCustomSelect(selectId, optionId, optionLabel) {
            const input = $('#' + selectId);
            const selectedText = $('#' + selectId + '_selected');
            const optionsList = $('#selected-' + selectId).siblings('.selectOptions');

            optionsList.find('.itemOption.pointer-events-none').remove();

            const safeLabel = $('<div>').text(optionLabel).html();
            const exists = optionsList.find('.itemOption[data-value="' + optionId + '"]').length > 0;
            if (!exists) {
                optionsList.append(
                    '<li class="itemOption cursor-default truncate rounded-lg px-4 py-2 text-sm text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900" ' +
                    'title="' + safeLabel + '" data-value="' + optionId + '" data-input="#' + selectId + '">' +
                    safeLabel +
                    '</li>'
                );
            }

            input.val(optionId).trigger('Changed').prop('required', true);
            selectedText.text(optionLabel);
        }

        async function quickCreateOption(inputSelector, feedbackSelector, endpoint, selectId) {
            const $input = $(inputSelector);
            const name = ($input.val() || '').trim();

            if (!name) {
                setFeedback(feedbackSelector, 'Escribe un nombre para guardar.', true);
                return;
            }

            try {
                const response = await $.ajax({
                    url: endpoint,
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').first().val(),
                        name: name
                    },
                    headers: {
                        Accept: 'application/json'
                    }
                });

                addOptionToCustomSelect(selectId, response.id, response.name);
                $input.val('');
                setFeedback(feedbackSelector, 'Se agregó correctamente.');
            } catch (error) {
                const validationMessage = error?.responseJSON?.errors?.name?.[0];
                const fallbackMessage = error?.responseJSON?.message || 'No se pudo guardar.';
                setFeedback(feedbackSelector, validationMessage || fallbackMessage, true);
            }
        }

        $(document).on('click', '#btn-quick-payment-method', function () {
            quickCreateOption(
                '#quick_payment_method_name',
                '#quick-payment-method-feedback',
                "{{ Route('business.quotation-payment-methods.quick-store') }}",
                'quotation_payment_method_id'
            );
        });

        $(document).on('click', '#btn-quick-delivery-time', function () {
            quickCreateOption(
                '#quick_delivery_time_name',
                '#quick-delivery-time-feedback',
                "{{ Route('business.quotation-delivery-times.quick-store') }}",
                'quotation_delivery_time_id'
            );
        });
    </script>
@endpush
