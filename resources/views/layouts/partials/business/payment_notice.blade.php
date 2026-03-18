@php
    $business_id = session('business');
    $business = \App\Models\Business::find($business_id);
    $oldest_payment_date = null;
    $should_show_notice = false;
    $notices_response = Illuminate\Support\Facades\Http::get(
        env('EASYPAY_URL') . 'usuario-avisos-login/' . $business->nit,
    );
    if ($notices_response->successful()) {
        $notices_data = $notices_response->json();
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
    } else {
        $notices_data = null;
        $procesando = true;
        $avisos = [];
        $sorted_avisos = collect($avisos);
        $oldest_payment_date = null;
        $should_show_notice = false;
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
            href="https://pagos.konverza.digital?nit={{ $business->nit }}"
            class="underline text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500"
            target="_blank">en
            este enlace</a>
    </div>
@endif
