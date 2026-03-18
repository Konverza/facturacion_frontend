@php
    $business_id = session('business');
    $business = \App\Models\Business::find($business_id);
    $base_endpoint = rtrim((string) env('EASYPAY_URL'), '/') . '/usuario-avisos-login/';
    $not_found_error = 'No fue posible encontrar datos para el documento proporcionado';

    $notices_data = null;
    $procesando = true;
    $avisos = [];
    $sorted_avisos = collect($avisos);
    $oldest_payment_date = null;
    $should_show_notice = false;
    $payment_document_for_link = $business?->nit ?? $business?->dui ?? '';
    if ($business?->nit || $business?->dui) {
        $documents_to_try = array_values(array_filter(array_unique([
            $business?->nit,
            $business?->dui,
        ])));

        foreach ($documents_to_try as $document) {
            $endpoint = $base_endpoint . $document;

            try {
                $notices_response = Illuminate\Support\Facades\Http::acceptJson()
                    ->connectTimeout(5)
                    ->timeout(10)
                    ->retry(3, 300)
                    ->get($endpoint);

                if (!$notices_response->successful()) {
                    continue;
                }

                $response_data = $notices_response->json();

                if (!is_array($response_data)) {
                    continue;
                }

                if (($response_data['error'] ?? null) === $not_found_error) {
                    continue;
                }

                $notices_data = $response_data;
                $payment_document_for_link = $document;
                $procesando = $notices_data['procesando'] ?? false;
                $avisos = $notices_data['avisos'] ?? [];
                $sorted_avisos = collect($avisos)->sortBy('pago_ultimo_dia');

                $oldest_payment_date_raw = $sorted_avisos->first()['pago_ultimo_dia'] ?? null;

                if (!empty($oldest_payment_date_raw)) {
                    try {
                        $oldest_payment_date = \Carbon\Carbon::parse($oldest_payment_date_raw);
                        $should_show_notice = $oldest_payment_date->lt(\Carbon\Carbon::today());
                    } catch (\Throwable $th) {
                        $oldest_payment_date = null;
                        $should_show_notice = false;
                    }
                }

                break;
            } catch (\Throwable $th) {
                Illuminate\Support\Facades\Log::warning('No se pudo consultar avisos de pago de EasyPay', [
                    'endpoint' => $endpoint,
                    'business_id' => $business_id,
                    'document' => $document,
                    'nit' => $business?->nit,
                    'dui' => $business?->dui,
                    'error' => $th->getMessage(),
                ]);
            }
        }
    }
@endphp
@if (!$procesando && $sorted_avisos->count() > 0 && $should_show_notice)
    <div
        class="my-4 rounded-lg border border-dashed border-yellow-500 bg-yellow-100 p-4 text-yellow-500 dark:bg-yellow-950/30">
        Estimado Cliente: Le informamos que tiene actualmente <b class="underline">{{ $sorted_avisos->count() }}</b>
        aviso(s) de pago
        pendiente(s), el más antiguo
        con fecha de vencimiento el <b class="underline">{{ $oldest_payment_date?->format('d/m/Y') ?? '' }}</b>. Para
        evitar interrupciones en el servicio,
        le recomendamos realizar el pago a la brevedad posible. Realice su pago <a
            href="https://pagos.konverza.digital?nit={{ $payment_document_for_link }}"
            class="underline text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500"
            target="_blank">en
            este enlace</a>
    </div>
@endif
