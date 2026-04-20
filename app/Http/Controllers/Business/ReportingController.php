<?php

namespace App\Http\Controllers\Business;

use App\Exports\ComprasSe;
use App\Exports\ConsumidorFinal;
use App\Exports\Contribuyente;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\OctopusService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Session;
use Storage;

class ReportingController extends Controller
{
    public function index()
    {
        return view('business.reporting.index');
    }

    public function store(Request $request)
    {
        try {
            $octopusService = new OctopusService();

            $business_id = Session::get('business') ?? null;
            if ($business_id) {
                $business = Business::find($business_id);
            } else {
                return redirect()->back()->with([
                    "error" => "Error",
                    "error_message" => "No se encontró la sesión de negocio."
                ]);
            }

            $book = $request->input("book_type");
            $datos_empresa = $octopusService->getDatosEmpresa($business->nit);
            $date_start = Carbon::parse($request->start_date);
            $date_end = Carbon::parse($request->end_date);
            $months_string = $this->getMonthsString($date_start, $date_end);
            $years_string = $this->getYearsString($date_start, $date_end);

            if ($book === "contribuyentes" || $book === "consumidores" || $book === "retencion_iva") {
                $dtes = $this->fetchSentDtes($business->nit, $request->start_date, $request->end_date);

                $codes = $this->getCodesByBookType($book);
                if (empty($codes)) {
                    return redirect()->back()->with([
                        "error" => "Error",
                        "error_message" => "Tipo de libro no válido",
                    ]);
                }

                $dtes_filter = collect($dtes)->filter(function ($dte) use ($date_start, $date_end, $codes, $book) {
                    $doc = $dte["documento"];
                    if (!$doc)
                        return false;

                    $date = $doc["identificacion"]["fecEmi"] ?? null;
                    $tipoDte = trim((string) ($doc["identificacion"]["tipoDte"] ?? ''));
                    $status = $dte["estado"] ?? null;

                    if (!$date || $status !== "PROCESADO")
                        return false;

                    if ($book === "percepcion_iva") {
                        if (($doc["resumen"]["ivaPerci1"] ?? 0) <= 0)
                            return false;
                    }

                    if ($book == "retencion_iva" && ($tipoDte === "05" || $tipoDte === "06")) {
                        if (($doc["resumen"]["ivaRete1"] ?? 0) <= 0)
                            return false;
                    }

                    $date_emi = Carbon::parse($date);
                    return $date_emi->betweenIncluded($date_start->format("Y-m-d"), $date_end->format("Y-m-d"))
                        && in_array($tipoDte, $codes);
                })->values()->all();

                if (empty($dtes_filter)) {
                    return redirect()->back()->with([
                        "error" => "No se encontraron documentos",
                        "error_message" => "No se encontraron dtes en el rango de fechas",
                    ]);
                }

                // Ordenar los DTEs por fecha de emisión ascendente
                usort($dtes_filter, function ($a, $b) {
                    $dateA = Carbon::parse($a["documento"]["identificacion"]["fecEmi"] ?? '');
                    $dateB = Carbon::parse($b["documento"]["identificacion"]["fecEmi"] ?? '');
                    return $dateA->greaterThan($dateB) ? 1 : -1;
                });
            }

            switch ($book) {
                case 'contribuyentes':
                    $file_path = $this->exportContribuyentes($dtes_filter, $datos_empresa, $months_string, $years_string, $request);
                    break;
                case 'consumidores':
                    $file_path = $this->exportConsumidores($dtes_filter, $datos_empresa, $months_string, $years_string, $request);
                    break;
                case 'anexos_f07':
                    $file_path = $this->anexos($request);
                    break;
                case 'compras':
                    $file_path = $this->exportCompras($business->nit, $datos_empresa, $months_string, $years_string, $request);
                    break;
                // case 'retencion_iva':
                //     $file_path = $this->exportRetencionIva($dtes_filter, $datos_empresa, $months_string, $request);
                //     break;
                // case 'percepcion_iva':
                //     $file_path = $this->exportPercepcionIva($request);
                //     break;
                default:
                    return redirect()->back()->with([
                        "error" => "Error",
                        "error_message" => "Tipo de libro aún no implementado",
                    ]);
            }

            return response()->download($file_path);

        } catch (\Exception $e) {
            Log::error("Error generating report: " . $e->getMessage());
            return redirect()->back()->with([
                "error" => "Error",
                "error_message" => "Ha ocurrido un error al generar el libro. Vuelve a intentarlo.",
            ]);
        }
    }

    public function previewAnexo(Request $request)
    {
        $validated = $request->validate([
            'book_type' => 'required|in:anexos_f07',
            'tipo_anexo' => 'required|in:contribuyentes,consumidores,compras,compras_se',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'tipo_operacion' => 'nullable|string',
            'tipo_ingreso' => 'nullable|string',
            'tipo_operacion_se' => 'nullable|string',
            'clasificacion' => 'nullable|string',
            'sector' => 'nullable|string',
            'tipo_costo' => 'nullable|string',
        ]);

        $business_id = Session::get('business') ?? null;
        $business = $business_id ? Business::find($business_id) : null;
        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la sesión de negocio.',
            ], 422);
        }

        $metadata = $this->getAnexoMetadata($validated['tipo_anexo']);
        $rows = $this->buildAnexoPreviewRows($validated, $business);

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron documentos para el anexo seleccionado.',
            ], 422);
        }

        $rows = $this->formatRowsForPreview($rows, $metadata['numeric_columns']);

        return response()->json([
            'success' => true,
            'message' => 'Previsualización generada correctamente.',
            'tipo_anexo' => $validated['tipo_anexo'],
            'columns' => $metadata['columns'],
            'editable_columns' => $metadata['editable_columns'],
            'rows' => $rows,
            'file_prefix' => $metadata['file_prefix'],
        ]);
    }

    public function downloadAnexo(Request $request)
    {
        $validated = $request->validate([
            'tipo_anexo' => 'required|in:contribuyentes,consumidores,compras,compras_se',
            'rows' => 'required|array|min:1',
            'rows.*' => 'required|array',
        ]);

        $metadata = $this->getAnexoMetadata($validated['tipo_anexo']);
        $columnKeys = array_map(fn($column) => $column['key'], $metadata['columns']);

        $normalizedRows = [];
        foreach ($validated['rows'] as $row) {
            $normalized = [];
            foreach ($columnKeys as $columnKey) {
                $value = $row[$columnKey] ?? '';
                if (in_array($columnKey, $metadata['numeric_columns'], true)) {
                    $value = number_format((float) $value, 2, '.', '');
                }
                $normalized[] = $value;
            }
            $normalizedRows[] = $normalized;
        }

        $file_path = $this->saveRowsAsCsv($normalizedRows, $metadata['file_prefix']);

        return response()->download($file_path)->deleteFileAfterSend(true);
    }

    public function anexos(Request $request): string
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $tipoOperacion = $request->tipo_operacion ?? null;
        $tipoOperacionSe = $request->tipo_operacion_se ?? null;
        $tipoIngreso = $request->tipo_ingreso ?? null;
        $clasificacion = $request->clasificacion ?? null;
        $sector = $request->sector ?? null;
        $tipo_costo = $request->tipo_costo ?? null;
        $dtes = $this->fetchSentDtes($business->nit, $request->start_date, $request->end_date, false);

        switch ($request->tipo_anexo) {
            case "contribuyentes":
                return $this->exportAnexoContribuyentes($dtes, $tipoOperacion, $tipoIngreso);
            case "consumidores":
                return $this->exportAnexoConsumidores($dtes, $tipoOperacion, $tipoIngreso);
            case "compras":
                $dtesRecibidos = $this->fetchReceivedDtes($business->nit, $request->start_date, $request->end_date, [], true);
                $rowsCompras = $this->buildAnexoComprasRows($dtesRecibidos, [
                    'tipo_operacion_se' => $tipoOperacionSe,
                    'clasificacion' => $clasificacion,
                    'sector' => $sector,
                    'tipo_costo' => $tipo_costo,
                ]);
                $metadataCompras = $this->getAnexoMetadata('compras');
                $columnKeysCompras = array_map(fn($column) => $column['key'], $metadataCompras['columns']);
                $normalizedRowsCompras = [];
                foreach ($rowsCompras as $row) {
                    $normalized = [];
                    foreach ($columnKeysCompras as $columnKey) {
                        $value = $row[$columnKey] ?? '';
                        if (in_array($columnKey, $metadataCompras['numeric_columns'], true)) {
                            $value = number_format((float) $value, 2, '.', '');
                        }
                        $normalized[] = $value;
                    }
                    $normalizedRowsCompras[] = $normalized;
                }
                return $this->saveRowsAsCsv($normalizedRowsCompras, $metadataCompras['file_prefix']);
            case "compras_se":
                // Implementar exportación de anexo de compras SE si es necesario
                return $this->exportAnexoComprasSE($dtes, $tipoOperacionSe, $clasificacion, $sector, $tipo_costo);
            default:
                throw new \Exception('Tipo de anexo no válido');
        }
    }

    private function buildAnexoPreviewRows(array $payload, Business $business): array
    {
        $tipoAnexo = $payload['tipo_anexo'];

        if (in_array($tipoAnexo, ['contribuyentes', 'consumidores'], true)) {
            $dtes = $this->fetchSentDtes($business->nit, $payload['start_date'], $payload['end_date'], true);
        } else {
            $dtes = $this->fetchReceivedDtes($business->nit, $payload['start_date'], $payload['end_date'], [], true);
        }

        return match ($tipoAnexo) {
            'contribuyentes' => $this->buildAnexoContribuyentesRows($dtes, $payload),
            'consumidores' => $this->buildAnexoConsumidoresRows($dtes, $payload),
            'compras' => $this->buildAnexoComprasRows($dtes, $payload),
            'compras_se' => $this->buildAnexoComprasSeRows($dtes, $payload),
            default => [],
        };
    }

    private function buildAnexoContribuyentesRows(array $dtes, array $payload): array
    {
        $filtered = collect($dtes)
            ->filter(fn($dte) => ($dte['estado'] ?? null) === 'PROCESADO' && in_array((string) ($dte['tipo_dte'] ?? ''), ['03', '05', '06'], true))
            ->sortBy('fhEmision')
            ->values();

        $rows = [];
        foreach ($filtered as $dte) {
            $doc = $dte['documento'] ?? [];
            $resumen = $doc['resumen'] ?? [];

            $totalIva = $this->sumTributosByCodes($resumen, ['20']);
            $totalPagar = (float) ($resumen['totalPagar'] ?? 0);

            $rows[] = [
                'A' => $this->formatDate($dte['fhEmision'] ?? null),
                'B' => '4',
                'C' => (string) ($dte['tipo_dte'] ?? ''),
                'D' => str_replace('-', '', (string) data_get($doc, 'identificacion.numeroControl', '')),
                'E' => (string) ($dte['selloRecibido'] ?? ''),
                'F' => str_replace('-', '', (string) ($dte['codGeneracion'] ?? '')),
                'G' => '',
                'H' => (string) data_get($doc, 'receptor.nit', ''),
                'I' => strtoupper((string) html_entity_decode((string) data_get($doc, 'receptor.nombre', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'J' => (float) ($resumen['totalExenta'] ?? 0),
                'K' => (float) ($resumen['totalNoSuj'] ?? 0),
                'L' => (float) ($resumen['totalGravada'] ?? 0),
                'M' => (float) $totalIva,
                'N' => 0.00,
                'O' => 0.00,
                'P' => $totalPagar,
                'Q' => '',
                'R' => (string) ($payload['tipo_operacion'] ?? '1'),
                'S' => (string) ($payload['tipo_ingreso'] ?? '1'),
                'T' => '1',
            ];
        }

        return $rows;
    }

    private function buildAnexoConsumidoresRows(array $dtes, array $payload): array
    {
        $dteCollection = collect($dtes)
            ->filter(fn($dte) => ($dte['estado'] ?? null) === 'PROCESADO' && in_array((string) ($dte['tipo_dte'] ?? ''), ['01', '11'], true))
            ->sortBy('fhEmision')
            ->values();

        $grouped = $dteCollection
            ->groupBy(fn($dte) => Carbon::parse($dte['fhEmision'])->toDateString())
            ->map(fn($items) => $items->groupBy('tipo_dte'));

        $rows = [];
        $paisesCentroamerica = ['GT', '9483', 'HN', '9501', 'SV', '9300', 'NI', '9615', 'CR', '9411'];

        foreach ($grouped as $fecha => $tipos) {
            foreach ($tipos as $tipo => $items) {
                $primerCod = $items->first()['codGeneracion'] ?? '';
                $ultimoCod = $items->last()['codGeneracion'] ?? '';

                $totalExento = $items->sum(fn($dte) => (float) data_get($dte, 'documento.resumen.totalExenta', 0));
                $totalNoSuj = $items->sum(fn($dte) => (float) data_get($dte, 'documento.resumen.totalNoSuj', 0));
                $totalGravado = $tipo === '01'
                    ? $items->sum(fn($dte) => (float) data_get($dte, 'documento.resumen.totalGravada', 0))
                    : 0.00;

                $totalExportacionDentro = 0.00;
                $totalExportacionFuera = 0.00;
                $totalExportacionServicios = 0.00;

                if ($tipo === '11') {
                    foreach ($items as $dte) {
                        $codPais = data_get($dte, 'documento.receptor.codPais');
                        $tipoItemExpor = data_get($dte, 'documento.emisor.tipoItemExpor');
                        $totalGravadaDte = (float) data_get($dte, 'documento.resumen.totalGravada', 0);

                        if ((string) $tipoItemExpor === '2') {
                            $totalExportacionServicios += $totalGravadaDte;
                            continue;
                        }

                        if (!empty($codPais)) {
                            if (in_array((string) $codPais, $paisesCentroamerica, true)) {
                                $totalExportacionDentro += $totalGravadaDte;
                            } else {
                                $totalExportacionFuera += $totalGravadaDte;
                            }
                        }
                    }
                }

                $totalZonasFrancas = 0.00;
                $totalCuentaTerceros = 0.00;
                $totalPagar = $totalExento + $totalNoSuj + $totalGravado + $totalExportacionDentro + $totalExportacionFuera + $totalExportacionServicios + $totalZonasFrancas + $totalCuentaTerceros;

                $rows[] = [
                    'A' => Carbon::parse($fecha)->format('d/m/Y'),
                    'B' => '4',
                    'C' => (string) $tipo,
                    'D' => 'N/A',
                    'E' => 'N/A',
                    'F' => 'N/A',
                    'G' => 'N/A',
                    'H' => str_replace('-', '', (string) $primerCod),
                    'I' => str_replace('-', '', (string) $ultimoCod),
                    'J' => '',
                    'K' => $totalExento,
                    'L' => 0.00,
                    'M' => $totalNoSuj,
                    'N' => $totalGravado,
                    'O' => $totalExportacionDentro,
                    'P' => $totalExportacionFuera,
                    'Q' => $totalExportacionServicios,
                    'R' => $totalZonasFrancas,
                    'S' => $totalCuentaTerceros,
                    'T' => $totalPagar,
                    'U' => (string) ($payload['tipo_operacion'] ?? '1'),
                    'V' => (string) ($payload['tipo_ingreso'] ?? '1'),
                    'W' => '2',
                ];
            }
        }

        return $rows;
    }

    private function buildAnexoComprasSeRows(array $dtes, array $payload): array
    {
        $tipoDocumentoMap = [
            '36' => '1',
            '13' => '2',
            '37' => '3',
            '03' => '3',
            '02' => '3',
        ];

        $filtered = collect($dtes)
            ->filter(fn($dte) => ($dte['estado'] ?? null) === 'PROCESADO' && (string) ($dte['tipo_dte'] ?? '') === '14')
            ->sortBy('fhEmision')
            ->values();

        $rows = [];
        foreach ($filtered as $dte) {
            $doc = $dte['documento'] ?? [];
            $sujetoExcluido = $doc['sujetoExcluido'] ?? [];

            $rows[] = [
                'A' => (string) ($tipoDocumentoMap[(string) data_get($sujetoExcluido, 'tipoDocumento', '')] ?? ''),
                'B' => str_replace('-', '', (string) data_get($sujetoExcluido, 'numDocumento', '')),
                'C' => strtoupper((string) html_entity_decode((string) data_get($sujetoExcluido, 'nombre', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'D' => $this->formatDate($dte['fhEmision'] ?? null),
                'E' => (string) ($dte['selloRecibido'] ?? ''),
                'F' => str_replace('-', '', (string) ($dte['codGeneracion'] ?? '')),
                'G' => (float) data_get($doc, 'resumen.totalCompra', 0),
                'H' => 0.00,
                'I' => (string) ($payload['tipo_operacion_se'] ?? '2'),
                'J' => (string) ($payload['clasificacion'] ?? '2'),
                'K' => (string) ($payload['sector'] ?? '4'),
                'L' => (string) ($payload['tipo_costo'] ?? '2'),
                'M' => '5',
            ];
        }

        return $rows;
    }

    private function buildAnexoComprasRows(array $dtes, array $payload): array
    {
        // Por defecto se consideran solo DTEs 03, 05 y 06 para el Anexo de Compras.
        $filtered = collect($dtes)
            ->filter(fn($dte) => ($dte['estado'] ?? null) !== 'ANULADO' && in_array((string) ($dte['tipo_dte'] ?? ''), ['03', '05', '06'], true))
            ->sortBy('fhEmision')
            ->values();

        $rows = [];
        foreach ($filtered as $dte) {
            $doc = $dte['documento'] ?? [];
            $resumen = $doc['resumen'] ?? [];
            $identificacion = $doc['identificacion'] ?? [];
            $emisor = $doc['emisor'] ?? [];

            $totalExenta = (float) ($resumen['totalExenta'] ?? 0);
            $totalNoSuj = (float) ($resumen['totalNoSuj'] ?? 0);
            $totalGravada = (float) ($resumen['totalGravada'] ?? 0);

            $descuExenta = (float) ($resumen['descuExenta'] ?? 0);
            $descuNoSuj = (float) ($resumen['descuNoSuj'] ?? 0);
            $descuGravada = (float) ($resumen['descuGravada'] ?? 0);

            $fovial = $this->sumTributosByCodes($resumen, ['D1']);
            $contrans = $this->sumTributosByCodes($resumen, ['C8']);

            $colG = max(0, ($totalExenta + $totalNoSuj) - ($descuExenta + $descuNoSuj) + $fovial + $contrans);
            $colJ = max(0, $totalGravada - $descuGravada);
            $colN = $this->sumTributosByCodes($resumen, ['20']);
            $colO = $colG + $colJ;

            $tipoOperacion = $this->resolveTipoOperacionCompras($totalExenta - $descuExenta, $totalNoSuj - $descuNoSuj, $colJ);

            $nitOrNrc = (string) (data_get($emisor, 'nit') ?: data_get($emisor, 'nrc', ''));
            $dui = empty($nitOrNrc) ? (string) data_get($emisor, 'dui', '') : '';

            $rows[] = [
                'A' => $this->formatDate(data_get($identificacion, 'fecEmi')),
                'B' => '4',
                'C' => (string) data_get($identificacion, 'tipoDte', $dte['tipo_dte'] ?? ''),
                'D' => str_replace('-', '', (string) data_get($identificacion, 'codigoGeneracion', $dte['codGeneracion'] ?? '')),
                'E' => $nitOrNrc,
                'F' => strtoupper((string) html_entity_decode((string) data_get($emisor, 'nombre', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'G' => $colG,
                'H' => 0.00,
                'I' => 0.00,
                'J' => $colJ,
                'K' => 0.00,
                'L' => 0.00,
                'M' => 0.00,
                'N' => $colN,
                'O' => $colO,
                'P' => $dui,
                'Q' => (string) ($payload['tipo_operacion_se'] ?? $tipoOperacion),
                'R' => (string) ($payload['clasificacion'] ?? '2'),
                'S' => (string) ($payload['sector'] ?? '4'),
                'T' => (string) ($payload['tipo_costo'] ?? '2'),
                'U' => '3',
            ];
        }

        return $rows;
    }

    private function resolveTipoOperacionCompras(float $netExenta, float $netNoSuj, float $netGravada): string
    {
        $hasGravada = $netGravada > 0;
        $hasExenta = $netExenta > 0;
        $hasNoSuj = $netNoSuj > 0;

        $count = ($hasGravada ? 1 : 0) + ($hasExenta ? 1 : 0) + ($hasNoSuj ? 1 : 0);
        if ($count === 0) {
            return '0';
        }
        if ($count > 1) {
            return '4';
        }
        if ($hasGravada) {
            return '1';
        }
        if ($hasExenta) {
            return '2';
        }

        return '3';
    }

    private function getAnexoMetadata(string $tipoAnexo): array
    {
        return match ($tipoAnexo) {
            'contribuyentes' => [
                'file_prefix' => 'anexo-f07-contribuyentes_',
                'numeric_columns' => ['J', 'K', 'L', 'M', 'N', 'O', 'P'],
                'editable_columns' => [
                    'R' => [
                        '1' => 'Gravada',
                        '2' => 'No Gravada o Exento',
                        '3' => 'Excluido o no Constituye Renta',
                        '4' => 'Mixta',
                        '12' => 'Ingresos que ya fueron sujetos de retención informados',
                        '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                    ],
                    'S' => [
                        '1' => 'Profesiones, Artes y Oficios',
                        '2' => 'Actividades de Servicios',
                        '3' => 'Actividades Comerciales',
                        '4' => 'Actividades Industriales',
                        '5' => 'Actividades Agropecuarias',
                        '6' => 'Utilidades y Dividendos',
                        '7' => 'Exportaciones de bienes',
                        '8' => 'Servicios Realizados en el Exterior y Utilizados en El Salvador',
                        '9' => 'Exportaciones de servicios',
                        '10' => 'Otras Rentas Gravables',
                        '12' => 'Ingresos que ya fueron sujetos de retención informados',
                        '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                    ],
                ],
                'columns' => $this->buildColumns([
                    'A' => 'Fecha', 'B' => 'Clase', 'C' => 'Tipo Doc', 'D' => 'No. Documento', 'E' => 'Sello', 'F' => 'Cod. Generación',
                    'G' => 'Control Interno', 'H' => 'NIT/NRC', 'I' => 'Nombre', 'J' => 'Exentas', 'K' => 'No Sujetas', 'L' => 'Gravadas',
                    'M' => 'IVA', 'N' => 'Vta. Terceros', 'O' => 'IVA Terceros', 'P' => 'Total', 'Q' => 'DUI',
                    'R' => 'Tipo Operación', 'S' => 'Tipo Ingreso', 'T' => 'Anexo'
                ]),
            ],
            'consumidores' => [
                'file_prefix' => 'anexo-f07-consumidor-final_',
                'numeric_columns' => ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'],
                'editable_columns' => [
                    'U' => [
                        '1' => 'Gravada',
                        '2' => 'No Gravada o Exento',
                        '3' => 'Excluido o no Constituye Renta',
                        '4' => 'Mixta',
                        '12' => 'Ingresos que ya fueron sujetos de retención informados',
                        '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                    ],
                    'V' => [
                        '1' => 'Profesiones, Artes y Oficios',
                        '2' => 'Actividades de Servicios',
                        '3' => 'Actividades Comerciales',
                        '4' => 'Actividades Industriales',
                        '5' => 'Actividades Agropecuarias',
                        '6' => 'Utilidades y Dividendos',
                        '7' => 'Exportaciones de bienes',
                        '8' => 'Servicios Realizados en el Exterior y Utilizados en El Salvador',
                        '9' => 'Exportaciones de servicios',
                        '10' => 'Otras Rentas Gravables',
                        '12' => 'Ingresos que ya fueron sujetos de retención informados',
                        '13' => 'Sujetos pasivos Excluidos (art. 6 LISR)',
                    ],
                ],
                'columns' => $this->buildColumns([
                    'A' => 'Fecha', 'B' => 'Clase', 'C' => 'Tipo DTE', 'D' => 'Resolución', 'E' => 'Serie', 'F' => 'Ctrl Int Del',
                    'G' => 'Ctrl Int Al', 'H' => 'Doc Del', 'I' => 'Doc Al', 'J' => 'Máquina', 'K' => 'Exentas', 'L' => 'Exentas Proporc.',
                    'M' => 'No Sujetas', 'N' => 'Gravadas', 'O' => 'Exp. CA', 'P' => 'Exp. Fuera CA', 'Q' => 'Exp. Servicios',
                    'R' => 'Zonas Francas', 'S' => 'Terceros No Dom.', 'T' => 'Total', 'U' => 'Tipo Operación', 'V' => 'Tipo Ingreso', 'W' => 'Anexo'
                ]),
            ],
            'compras_se' => [
                'file_prefix' => 'anexo-f07-compras-se_',
                'numeric_columns' => ['G', 'H'],
                'editable_columns' => [
                    'I' => ['1' => 'Gravada', '2' => 'No Gravada o Exenta', '3' => 'Excluido o no Constituye Renta', '4' => 'Mixta'],
                    'J' => ['1' => 'Costo', '2' => 'Gasto'],
                    'K' => ['1' => 'Industrial', '2' => 'Comercial', '3' => 'Agropecuario', '4' => 'Servicios/Otros'],
                    'L' => [
                        '1' => 'Gastos de Venta sin Donación',
                        '2' => 'Gastos de Administración sin Donación',
                        '3' => 'Gastos Financieros sin Donación',
                        '4' => 'Costo Artículos Importados/Internaciones',
                        '5' => 'Costo Artículos Internos',
                        '6' => 'Costos Indirectos de Fabricación',
                        '7' => 'Mano de Obra',
                    ],
                ],
                'columns' => $this->buildColumns([
                    'A' => 'Tipo Doc', 'B' => 'No. Doc', 'C' => 'Nombre', 'D' => 'Fecha', 'E' => 'Sello', 'F' => 'Cod. Generación',
                    'G' => 'Monto Compra', 'H' => 'Retención IVA', 'I' => 'Tipo Operación', 'J' => 'Clasificación', 'K' => 'Sector', 'L' => 'Tipo Costo/Gasto', 'M' => 'Anexo'
                ]),
            ],
            'compras' => [
                'file_prefix' => 'anexo-f07-compras_',
                'numeric_columns' => ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'],
                'editable_columns' => [
                    'Q' => ['0' => 'Sin datos', '1' => 'Solo gravadas', '2' => 'Solo exentas', '3' => 'Solo no sujetas', '4' => 'Mixta'],
                    'R' => ['1' => 'Costo', '2' => 'Gasto'],
                    'S' => ['1' => 'Industrial', '2' => 'Comercial', '3' => 'Agropecuario', '4' => 'Servicios/Otros'],
                    'T' => [
                        '1' => 'Gastos de Venta sin Donación',
                        '2' => 'Gastos de Administración sin Donación',
                        '3' => 'Gastos Financieros sin Donación',
                        '4' => 'Costo Artículos Importados/Internaciones',
                        '5' => 'Costo Artículos Internos',
                        '6' => 'Costos Indirectos de Fabricación',
                        '7' => 'Mano de Obra',
                    ],
                ],
                'columns' => $this->buildColumns([
                    'A' => 'Fecha de Emisión', 'B' => 'Clase Doc', 'C' => 'Tipo Doc', 'D' => 'No. Documento', 'E' => 'NIT/NRC Proveedor',
                    'F' => 'Nombre Proveedor', 'G' => 'Compras Internas Exentas/No Sujetas', 'H' => 'Internaciones Exentas/No Sujetas',
                    'I' => 'Importaciones Exentas/No Sujetas', 'J' => 'Compras Internas Gravadas', 'K' => 'Internaciones Gravadas',
                    'L' => 'Importaciones Gravadas Bienes', 'M' => 'Importaciones Gravadas Servicios', 'N' => 'Crédito Fiscal',
                    'O' => 'Total Compras', 'P' => 'DUI Proveedor', 'Q' => 'Tipo Operación', 'R' => 'Clasificación',
                    'S' => 'Sector', 'T' => 'Tipo Costo/Gasto', 'U' => 'No. Anexo'
                ]),
            ],
            default => [
                'file_prefix' => 'anexo_',
                'numeric_columns' => [],
                'editable_columns' => [],
                'columns' => [],
            ],
        };
    }

    private function buildColumns(array $map): array
    {
        $columns = [];
        foreach ($map as $key => $label) {
            $columns[] = ['key' => $key, 'label' => $label];
        }
        return $columns;
    }

    private function formatRowsForPreview(array $rows, array $numericColumns): array
    {
        return array_map(function ($row) use ($numericColumns) {
            foreach ($numericColumns as $columnKey) {
                if (array_key_exists($columnKey, $row)) {
                    $row[$columnKey] = number_format((float) $row[$columnKey], 2, '.', '');
                }
            }
            return $row;
        }, $rows);
    }

    private function sumTributosByCodes(array $resumen, array $codes): float
    {
        $tributos = $resumen['tributos'] ?? [];
        if (!is_array($tributos)) {
            return 0.0;
        }

        $sum = 0.0;
        foreach ($tributos as $tributo) {
            $codigo = (string) data_get($tributo, 'codigo', '');
            if (in_array($codigo, $codes, true)) {
                $sum += (float) data_get($tributo, 'valor', 0);
            }
        }

        return round($sum, 2);
    }

    private function formatDate(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function saveRowsAsCsv(array $rows, string $prefix): string
    {
        $fileName = $prefix . date('YmdHis') . '_' . Str::lower(Str::random(6)) . '.csv';
        $directory = 'exports';

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $filePath = storage_path("app/public/{$directory}/{$fileName}");
        $handle = fopen($filePath, 'w');

        if ($handle === false) {
            throw new \RuntimeException('No fue posible crear el archivo CSV.');
        }

        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }

        fclose($handle);

        return $filePath;
    }

    private function exportConsumidores(array $dtes, array $datos_empresa, string $months, string $years, $request): string
    {
        $path = public_path("reportes/formato_consumidor_final.xlsx");
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $startRow = 11;

        $dtesGroupedByDate = collect($dtes)->groupBy(fn($dte) => $dte["documento"]["identificacion"]["fecEmi"] ?? '');

        $total_dtes = count($dtesGroupedByDate);
        if ($total_dtes > 1) {
            $sheet->insertNewRowBefore($startRow + 1, $total_dtes - 1);
        }

        $sheet->setCellValue("A1", $datos_empresa["nombre"] ?? "");
        $sheet->setCellValue("A2", $datos_empresa["complemento"] ?? "");
        $sheet->setCellValue("A3", "Número de Registro de Contribuyente: " . $datos_empresa["nrc"] . " NIT: " . $datos_empresa["nit"]);
        $sheet->setCellValue("C6", $months);
        $sheet->setCellValue("C7", $years);

        $row = $startRow;

        $totalDocExentas = 0;
        $totalDocGravadas = 0;
        $totalDocNoSujetas = 0;
        $totalDocExportacion = 0;

        foreach ($dtesGroupedByDate as $date => $dtesGroup) {
            // Ordenar DTEs por número de control antes de obtener el primero y último
            $dtesGroupOrdenados = $dtesGroup->sortBy(fn($dte) => $dte["documento"]["identificacion"]["numeroControl"])->values();
            
            $docFirst = $dtesGroupOrdenados->first()["documento"] ?: [];
            $docLast = $dtesGroupOrdenados->last()["documento"] ?: [];

            $tipo_documento = "";
            if ($docFirst["identificacion"]["tipoDte"] === "01") {
                $tipo_documento = "01.FACTURA";
            } elseif ($docFirst["identificacion"]["tipoDte"] === "11") {
                $tipo_documento = "11.FACTURA DE EXPORTACIÓN";
            }

            $sheet->setCellValue("A{$row}", Carbon::parse($date)->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", "4.DOCUMENTO TRIBUTARIO ELECTRÓNICO (DTE)");
            $sheet->setCellValue("C{$row}", $tipo_documento);
            $sheet->setCellValue("D{$row}", "N/A");
            $sheet->setCellValue("E{$row}", "N/A");
            $sheet->setCellValue("F{$row}", "N/A");
            $sheet->setCellValue("G{$row}", $docFirst["identificacion"]["codigoGeneracion"] ?? "");
            $sheet->setCellValue("H{$row}", $docLast["identificacion"]["codigoGeneracion"] ?? "");
            $sheet->setCellValue("I{$row}", $docFirst["identificacion"]["numeroControl"] ?? "");
            $sheet->setCellValue("J{$row}", $docLast["identificacion"]["numeroControl"] ?? "");

            $total_exportaciones = 0;
            $total_no_sujetas = 0;
            $total_gravadas = 0;
            $total_exentas = 0;

            foreach ($dtesGroup as $dte) {
                $doc = $dte["documento"] ?: [];
                if ($doc["identificacion"]["tipoDte"] === "11") {
                    $total_exportaciones += $doc["resumen"]["montoTotalOperacion"] ?? 0;
                    $totalDocExportacion += $doc["resumen"]["montoTotalOperacion"] ?? 0;
                } else {
                    $total_no_sujetas += $doc["resumen"]["totalNoSuj"] ?? 0;
                    $total_gravadas += $doc["resumen"]["totalGravada"] ?? 0;
                    $total_exentas += $doc["resumen"]["totalExenta"] ?? 0;

                    $totalDocExentas += $doc["resumen"]["totalExenta"] ?? 0;
                    $totalDocGravadas += $doc["resumen"]["totalGravada"] ?? 0;
                    $totalDocNoSujetas += $doc["resumen"]["totalNoSuj"] ?? 0;
                }
            }

            $sheet->setCellValue("K{$row}", $total_no_sujetas);
            $sheet->setCellValue("L{$row}", $total_exentas);
            $sheet->setCellValue("M{$row}", $total_gravadas);
            $sheet->setCellValue("N{$row}", $total_exportaciones);
            $sheet->setCellValue("O{$row}", "=SUM(K{$row}:N{$row})");

            $row++;
        }

        // Set totals 
        $sheet->setCellValue("K{$row}", $totalDocNoSujetas);
        $sheet->setCellValue("L{$row}", $totalDocExentas);
        $sheet->setCellValue("M{$row}", $totalDocGravadas);
        $sheet->setCellValue("N{$row}", $totalDocExportacion);
        $sheet->setCellValue("O{$row}", "=SUM(K{$row}:N{$row})");
        return $this->saveSpreadsheet($spreadsheet, "libro_consumidores_", $request);
    }

    private function exportContribuyentes(array $dtes, array $datos_empresa, string $months, string $years, $request): string
    {
        $path = public_path("reportes/formato_contribuyentes.xlsx");
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $startRow = 10;
        $total_dtes = count($dtes);
        if ($total_dtes > 1) {
            $sheet->insertNewRowBefore($startRow + 1, $total_dtes - 1);
        }

        $sheet->setCellValue("A1", $datos_empresa["nombre"] ?? "");
        $sheet->setCellValue("A2", $datos_empresa["complemento"] ?? "");
        $sheet->setCellValue("A3", "Número de Registro de Contribuyente: " . $datos_empresa["nrc"] . " NIT: " . $datos_empresa["nit"]);
        $sheet->setCellValue("C6", $months);
        $sheet->setCellValue("C7", $years);

        $row = $startRow;

        $totalDocExentas = 0;
        $totalDocGravadas = 0;
        $totalDocNoSujetas = 0;
        $totalIva = 0;

        foreach ($dtes as $index => $dte) {
            $doc = $dte["documento"] ?: [];
            $resumen = $doc["resumen"] ?: [];
            $identificacion = $doc["identificacion"] ?: [];
            $respuesta_hacienda = $doc["respuesta_hacienda"] ?? $doc["respuestaHacienda"] ?? [];
            if (is_string($respuesta_hacienda)) {
                $respuesta_hacienda = json_decode($respuesta_hacienda, true) ?? [];
            }

            $sello_recibido = $doc["selloRecibido"] ?? $respuesta_hacienda["selloRecibido"] ?? "";

            $iva = 0;
            if (!empty($resumen["tributos"]) && is_array($resumen["tributos"])) {
                foreach ($resumen["tributos"] as $tributo) {
                    if (($tributo["codigo"] ?? '') === "20") {
                        $iva = $tributo["valor"] ?? 0;
                        break;
                    }
                }
            }

            $date = Carbon::parse($identificacion["fecEmi"] ?? '')->format('d/m/Y');

            $tipo_documento = "";
            if ($identificacion["tipoDte"] === "03") {
                $tipo_documento = "03.COMPROBANTE DE CRÉDITO FISCAL";
            } elseif ($identificacion["tipoDte"] === "05") {
                $tipo_documento = "05.NOTA DE CRÉDITO";
            } elseif ($identificacion["tipoDte"] === "06") {
                $tipo_documento = "06.NOTA DE DÉBITO";
            }

            $sign = ($identificacion["tipoDte"] ?? "") === "05" ? -1 : 1;
            $totalExenta = (float) ($resumen["totalExenta"] ?? 0) * $sign;
            $totalNoSuj = (float) ($resumen["totalNoSuj"] ?? 0) * $sign;
            $totalGravada = (float) ($resumen["totalGravada"] ?? 0) * $sign;
            $ivaFirmado = (float) $iva * $sign;

            $totalDocExentas += $totalExenta;
            $totalDocGravadas += $totalGravada;
            $totalDocNoSujetas += $totalNoSuj;
            $totalIva += $ivaFirmado;

            $sheet->setCellValue("A{$row}", $date);
            $sheet->setCellValue("B{$row}", "4.DOCUMENTO TRIBUTARIO ELECTRÓNICO (DTE)");
            $sheet->setCellValue("C{$row}", $tipo_documento);
            $sheet->setCellValue("D{$row}", $doc["identificacion"]["numeroControl"] ?? "");
            $sheet->setCellValue("E{$row}", $sello_recibido);
            $sheet->setCellValue("F{$row}", $doc["identificacion"]["codigoGeneracion"] ?? "");
            $sheet->setCellValue("G{$row}", $doc["receptor"]["nrc"] ?? "");
            $sheet->setCellValue("H{$row}", html_entity_decode($doc["receptor"]["nombre"] ?? "", ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $sheet->setCellValue("I{$row}", $totalExenta);
            $sheet->setCellValue("J{$row}", $totalNoSuj);
            $sheet->setCellValue("K{$row}", $totalGravada);
            $sheet->setCellValue("L{$row}", $ivaFirmado);
            $sheet->setCellValue("M{$row}", "=SUM(I$row:L$row)");
            $row++;
        }

        // Set totals
        $sheet->setCellValue("I{$row}", $totalDocExentas);
        $sheet->setCellValue("J{$row}", $totalDocNoSujetas);
        $sheet->setCellValue("K{$row}", $totalDocGravadas);
        $sheet->setCellValue("L{$row}", $totalIva);
        $sheet->setCellValue("M{$row}", "=SUM(I$row:L$row)");

        return $this->saveSpreadsheet($spreadsheet, "libro_contribuyentes_", $request);
    }

    private function exportCompras(string $nit, array $datos_empresa, string $months, string $years, Request $request): string
    {
        $path = public_path("reportes/formato_compras.xlsx");
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $startRow = 11;

        $dtesEmitidos = collect($this->fetchSentDtes($nit, $request->start_date, $request->end_date, true))
            ->filter(fn($dte) => ($dte['estado'] ?? null) === 'PROCESADO' && (string) ($dte['tipo_dte'] ?? '') === '14')
            ->map(function ($dte) {
                $doc = $dte['documento'] ?? [];
                $identificacion = $doc['identificacion'] ?? [];

                return [
                    'source' => 'emitido',
                    'dte' => $dte,
                    'emission_date' => (string) ($identificacion['fecEmi'] ?? ''),
                    'timestamp' => Carbon::parse($identificacion['fecEmi'] ?? now()->toDateString())->timestamp,
                ];
            });

        $dtesRecibidos = collect($this->fetchReceivedDtes($nit, $request->start_date, $request->end_date, [], true))
            ->filter(function ($dte) {
                $tipo = (string) ($dte['tipo_dte'] ?? '');
                $estado = $dte['estado'] ?? null;

                return in_array($tipo, ['03', '05', '06'], true)
                    && ($estado !== 'ANULADO');
            })
            ->map(function ($dte) {
                $doc = $dte['documento'] ?? [];
                $identificacion = $doc['identificacion'] ?? [];
                $emissionDate = (string) ($identificacion['fecEmi'] ?? data_get($dte, 'fhEmision', ''));

                return [
                    'source' => 'recibido',
                    'dte' => $dte,
                    'emission_date' => $emissionDate,
                    'timestamp' => Carbon::parse($emissionDate ?: now()->toDateString())->timestamp,
                ];
            });

        $documents = $dtesEmitidos
            ->concat($dtesRecibidos)
            ->sortBy('timestamp')
            ->values();

        $total_dtes = $documents->count();
        if ($total_dtes > 1) {
            $sheet->insertNewRowBefore($startRow + 1, $total_dtes - 1);
        }

        $sheet->setCellValue("A1", $datos_empresa['nombre'] ?? '');
        $sheet->setCellValue("A2", $datos_empresa['complemento'] ?? '');
        $sheet->setCellValue("A3", 'Número de Registro de Contribuyente: ' . ($datos_empresa['nrc'] ?? '') . ' NIT: ' . ($datos_empresa['nit'] ?? ''));
        $sheet->setCellValue("C6", $months);
        $sheet->setCellValue("C7", $years);

        $row = $startRow;

        $totalComprasExentas = 0.0;
        $totalComprasGravadas = 0.0;
        $totalComprasSujetosExcluidos = 0.0;
        $totalCreditoFiscal = 0.0;
        $totalFovial = 0.0;
        $totalCotrans = 0.0;

        foreach ($documents as $index => $item) {
            $dte = $item['dte'];
            $source = $item['source'];
            $doc = $dte['documento'] ?? [];
            $identificacion = $doc['identificacion'] ?? [];
            $resumen = $doc['resumen'] ?? [];

            $date = $this->formatDate($item['emission_date']);
            $codigoGeneracion = (string) data_get($identificacion, 'codigoGeneracion', ($dte['codGeneracion'] ?? ''));
            $tipoDte = (string) data_get($identificacion, 'tipoDte', ($dte['tipo_dte'] ?? ''));
            $tipoDocumento = match ($tipoDte) {
                '14' => '14.FACTURA DE SUJETO EXCLUIDO',
                '03' => '03.COMPROBANTE DE CREDITO FISCAL',
                '05' => '05.NOTA DE CREDITO',
                '06' => '06.NOTA DE DEBITO',
                default => $tipoDte,
            };
            $sign = $source === 'recibido' && $tipoDte === '05' ? -1 : 1;

            $colG = 0.0;
            $colH = 0.0;
            $colI = 0.0;
            $colJ = 0.0;
            $colK = 0.0;
            $colL = 0.0;
            $colD = '';
            $colE = '';
            $colF = '';

            if ($source === 'recibido') {
                $emisor = $doc['emisor'] ?? [];
                $totalExenta = (float) ($resumen['totalExenta'] ?? 0);
                $totalNoSuj = (float) ($resumen['totalNoSuj'] ?? 0);
                $descuExenta = (float) ($resumen['descuExenta'] ?? 0);
                $descuNoSuj = (float) ($resumen['descuNoSuj'] ?? 0);
                $totalGravada = (float) ($resumen['totalGravada'] ?? 0);
                $descuGravada = (float) ($resumen['descuGravada'] ?? 0);

                $colG = (($totalExenta + $totalNoSuj) - ($descuExenta + $descuNoSuj)) * $sign;
                $colH = ($totalGravada - $descuGravada) * $sign;
                $colJ = $this->sumTributosByCodes($resumen, ['20']) * $sign;
                $colK = $this->sumTributosByCodes($resumen, ['D1']) * $sign;
                $colL = $this->sumTributosByCodes($resumen, ['C8']) * $sign;

                $colD = (string) data_get($emisor, 'nrc', '');
                $colE = (string) data_get($emisor, 'nit', '');
                $colF = strtoupper((string) html_entity_decode((string) data_get($emisor, 'nombre', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            } else {
                $receptor = $doc['receptor'] ?? [];
                $colI = (float) data_get($resumen, 'totalCompra', 0);

                $colD = '';
                $colE = (string) data_get($receptor, 'numDocumento', '');
                $colF = strtoupper((string) html_entity_decode((string) data_get($receptor, 'nombre', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            $totalComprasExentas += $colG;
            $totalComprasGravadas += $colH;
            $totalComprasSujetosExcluidos += $colI;
            $totalCreditoFiscal += $colJ;
            $totalFovial += $colK;
            $totalCotrans += $colL;

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $tipoDocumento);
            $sheet->setCellValue("C{$row}", $date);
            $sheet->setCellValue("D{$row}", $codigoGeneracion);
            $sheet->setCellValue("E{$row}", $colD);
            $sheet->setCellValue("F{$row}", $colE);
            $sheet->setCellValue("G{$row}", $colF);
            $sheet->setCellValue("H{$row}", $colG);
            $sheet->setCellValue("I{$row}", $colH);
            $sheet->setCellValue("J{$row}", $colI);
            $sheet->setCellValue("K{$row}", $colJ);
            $sheet->setCellValue("L{$row}", $colK);
            $sheet->setCellValue("M{$row}", $colL);
            $sheet->setCellValue("N{$row}", "=SUM(H{$row}:M{$row})");
            $row++;
        }

        // Set totals
        $sheet->setCellValue("H{$row}", $totalComprasExentas);
        $sheet->setCellValue("I{$row}", $totalComprasGravadas);
        $sheet->setCellValue("J{$row}", $totalComprasSujetosExcluidos);
        $sheet->setCellValue("K{$row}", $totalCreditoFiscal);
        $sheet->setCellValue("L{$row}", $totalFovial);
        $sheet->setCellValue("M{$row}", $totalCotrans);
        $sheet->setCellValue("N{$row}", "=SUM(H{$row}:M{$row})");

        return $this->saveSpreadsheet($spreadsheet, 'libro_compras_', $request);
    }

    private function exportAnexoContribuyentes($dtes, $tipoOperacion, $tipoIngreso)
    {
        $dte_collection = collect();
        foreach ($dtes as $dte) {
            if ($dte["estado"] == "PROCESADO" && in_array($dte["tipo_dte"], ["03", "05", "06"])) {
                $dte_collection->push($dte);
            }
        }
        $fileName = 'anexo-f07-contribuyentes_' . date("YmdHis") . '.csv';
        $filePath = "exports/{$fileName}";
        Excel::store(new Contribuyente($dte_collection, $tipoOperacion, $tipoIngreso), $filePath, 'public');
        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception('Error al generar el archivo de contribuyentes');
        }
        return storage_path("app/public/{$filePath}");
    }

    private function exportAnexoConsumidores($dtes, $tipoOperacion, $tipoIngreso)
    {
        $dte_collection = collect();
        foreach ($dtes as $dte) {
            if ($dte["estado"] === "PROCESADO" && in_array($dte["tipo_dte"], ["01", "11"])) {
                $dte_collection->push($dte);
            }
        }

        // Agrupar por fecha y ordenar dentro de cada grupo por fecha y hora
        $grouped_dtes = $dte_collection
            ->sortBy('fhEmision') // Ordenar globalmente antes de agrupar
            ->groupBy(fn($dte) => Carbon::parse($dte["fhEmision"])->toDateString())
            ->map(fn($dtes) => $dtes->groupBy('tipo_dte'));

        $result = [];

        foreach ($grouped_dtes as $fecha => $tipos) {
            foreach ($tipos as $tipo => $dtes) {
                // Obtener el primer y último codGeneracion del grupo por fecha
                $primer_cod = $dtes->first()["codGeneracion"];
                $ultimo_cod = $dtes->last()["codGeneracion"];

                // Países del área centroamericana
                $paisesCentroamerica = ['GT', '9483', 'HN', '9501', 'SV', '9300', 'NI', '9615', 'CR', '9411'];

                // Sumar valores de los DTEs
                $totalExento = $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalExenta ?? 0);
                $totalNoSuj = $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalNoSuj ?? 0);
                $totalGravado = $tipo == "01" ? $dtes->sum(fn($dte) => $dte["documento"]->resumen->totalGravada ?? 0) : 0;

                // Cálculos de exportación solo para tipo "11"
                $totalExportacionDentro = 0;
                $totalExportacionFuera = 0;
                $totalExportacionServicios = 0;

                if ($tipo == "11") {
                    foreach ($dtes as $dte) {
                        $codPais = $dte["documento"]->receptor->codPais ?? null;
                        $tipoItemExpor = $dte["documento"]->emisor->tipoItemExpor ?? null;
                        $totalGravadaDTE = $dte["documento"]->resumen->totalGravada ?? 0;

                        // Exportación de servicios (tipoItemExpor = 2)
                        if ($tipoItemExpor == 2) {
                            $totalExportacionServicios += $totalGravadaDTE;
                            continue; // Ya contabilizado, pasar al siguiente DTE
                        }

                        // Exportación dentro o fuera de Centroamérica
                        if ($codPais) {
                            if (in_array($codPais, $paisesCentroamerica)) {
                                $totalExportacionDentro += $totalGravadaDTE;
                            } else {
                                $totalExportacionFuera += $totalGravadaDTE;
                            }
                        }
                    }
                }

                $totalZonasFrancas = 0;
                $totalCuentaTerceros = 0;

                $totalPagar = $totalExento + $totalNoSuj + $totalGravado + $totalExportacionDentro + $totalExportacionFuera + $totalExportacionServicios + $totalZonasFrancas + $totalCuentaTerceros;

                // Formatear la fecha
                $fecha_formateada = Carbon::parse($fecha)->format('d/m/Y');

                // Generar el array con la estructura solicitada
                $result[] = [
                    $fecha_formateada, // Fecha de emisión en dd/mm/YYYY (A)
                    "4", // Clase de documento (B)
                    $tipo, // Tipo de DTE (C)
                    "N/A", // Número de resolución "N/A" (D)
                    "N/A", // Serie de Documento "N/A" (E)
                    "N/A", // Número de Control interno (Del) (F)
                    "N/A", // Número de Control interno (Al) (G)
                    $primer_cod, // Número de documento (del) (H)
                    $ultimo_cod, // Número de documento (al) (I)
                    null, // Número de máquina registradora (J)
                    $totalExento, // Ventas exentas (K)
                    0, // Ventas internas exentas no sujetas a proporcionalidad (L)
                    $totalNoSuj, // Ventas no sujetas (M)
                    $totalGravado, // ventas gravadas locales (N) si tipo es "01", si no, 0
                    $totalExportacionDentro, // Exportaciones dentro del área centroamericana (O)
                    $totalExportacionFuera, // Exportaciones fuera del área centroamericana (P)
                    $totalExportacionServicios, // Exportaciones de Servicios (Q)
                    $totalZonasFrancas, // Ventas a Zonas Francas y DPA (R)
                    $totalCuentaTerceros, // Ventas a Cuenta de Terceros No domiciliados (S)
                    $totalPagar, // Suma totalExenta + totalNoSuj + totalGravada (T)
                    $tipoOperacion, // Tipo de operación (Renta) (U)
                    $tipoIngreso, // "03" // Tipo de Ingreso (Renta) (V)
                    "2", // "2" // Número de Anexo, siempre "2" (W)
                ];
            }
        }

        $fileName = 'anexo-f07-consumidor-final_' . date("YmdHis") . '.csv';
        $filePath = "exports/{$fileName}";

        // Guardar el archivo temporalmente
        Excel::store(new ConsumidorFinal($result), $filePath, 'public');

        // Verificar si el archivo se generó
        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception('Error al generar el archivo de consumidores');
        }

        return storage_path("app/public/{$filePath}");
    }


    private function exportAnexoComprasSE($dtes, $tipoOperacion , $clasificacion, $sector, $tipo_costo)
    {
        $dte_collection = collect();
        foreach ($dtes as $dte) {
            if ($dte["estado"] == "PROCESADO" && in_array($dte["tipo_dte"], ["14"])) {
                $dte_collection->push($dte);
            }
        }

        // Sort DTE by emission date and time
        $dte_collection = $dte_collection->sortBy('fhEmision');
        $fileName = 'anexo-f07-compras-se_' . date("YmdHis") . '.csv';
        $filePath = "exports/{$fileName}";
        Excel::store(new ComprasSe($dte_collection, $tipoOperacion, $clasificacion, $sector, $tipo_costo), $filePath, 'public');
        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception('Error al generar el archivo de Compras Sujeto Excluido');
        }
        return storage_path("app/public/{$filePath}");
    }


    private function getMonthsString(Carbon $start, Carbon $end): string
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->startOfMonth());
        $months = [];
        foreach ($period as $date) {
            $months[] = strtoupper($date->locale('es')->isoFormat('MMMM'));
        }
        return implode(" - ", $months);
    }

    private function getYearsString(Carbon $start, Carbon $end): string
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $period = CarbonPeriod::create($start->copy()->startOfYear(), '1 year', $end->copy()->startOfYear());
        $years = [];
        foreach ($period as $date) {
            $years[] = strtoupper($date->locale('es')->isoFormat('YYYY'));
        }
        return implode(" - ", $years);
    }

    private function getCodesByBookType(string $book_type): array
    {
        return match ($book_type) {
            "contribuyentes" => ["03", "05", "06"],
            "consumidores" => ["01", "11"],
            "percepcion_iva" => ["03", "06", "05"],
            "retencion_iva" => ["07", "05", "06"],
            "compras" => ["03", "05", "06", "11"],
            default => [],
        };
    }

    private function fetchSentDtes(string $nit, string $start_date, string $end_date, bool $associative = true): array
    {
        // Parametros
        $parameters = [
            'nit' => $nit,
            'emisionInicio' => $start_date ? "{$start_date}T00:00:00" : null,
            'emisionFin' => $end_date ? "{$end_date}T23:59:59" : null,
            'estado' => "PROCESADO",
        ];

        // Realizar la solicitud a la API de Octopus para obtener los DTEs
        $response_dtes = Http::get(env("OCTOPUS_API_URL") . "/dtes/", $parameters);
        $data = $response_dtes->json();
        $dtes = array_map(function ($dte) use ($associative) {
            $dte["documento"] = json_decode($dte["documento"], $associative);
            return $dte;
        }, $data['items'] ?? []);

        return $dtes;
    }

    private function fetchReceivedDtes(string $nit, string $start_date, string $end_date, array $filters = [], bool $associative = true): array
    {
        $parameters = [
            'nit' => $nit,
            'fechaInicio' => $start_date ? "{$start_date}T00:00:00" : null,
            'fechaFin' => $end_date ? "{$end_date}T23:59:59" : null,
            'tipo_dte' => $filters['received_tipo_dte'] ?? null,
            'documento_emisor' => $filters['received_documento_emisor'] ?? null,
            'q' => $filters['received_q'] ?? null,
            'sort' => 'desc',
            'limit' => 10000,
            'page' => 1,
        ];

        $responseDtes = Http::get(env('OCTOPUS_API_URL') . '/dtes_recibidos/', $parameters);
        $data = $responseDtes->json();

        return array_map(function ($dte) use ($associative) {
            $dte['documento'] = json_decode($dte['documento'] ?? '{}', $associative);
            return $dte;
        }, $data['items'] ?? []);
    }

    private function saveSpreadsheet($spreadsheet, string $prefix, $request): string
    {
        if ($request->has("format_csv")) {
            $fileName = $prefix . date("YmdHis") . ".csv";
            $filePath = storage_path("app/public/{$fileName}");

            $writer = IOFactory::createWriter($spreadsheet, "Csv");
            $writer->save($filePath);
        } else {
            $fileName = $prefix . date("YmdHis") . ".xlsx";
            $filePath = storage_path("app/public/{$fileName}");

            $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
            $writer->save($filePath);
        }

        return $filePath;
    }
}
