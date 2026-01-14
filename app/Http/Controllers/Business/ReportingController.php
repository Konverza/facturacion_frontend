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
            $uploadedDtes = [];
            if ($request->hasFile('uploaded_files')) {
                $uploadedDtes = $this->processUploadedFiles($request->file('uploaded_files'));
            }

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
            $onlySelected = $request->has('only_selected') && $request->only_selected;
            $onlyMix = $request->has('only_mix') && $request->only_mix;
            $datos_empresa = $octopusService->getDatosEmpresa($business->nit);

            if ($book === "contribuyentes" || $book === "consumidores" || $book === "retencion_iva") {
                if ($onlySelected) {
                    $dtes = $uploadedDtes;
                } else {
                    $dtes = $this->fetchSentDtes($business->nit, $request->start_date, $request->end_date);
                    if ($onlyMix && !empty($uploadedDtes)) {
                        $dtes = array_merge($dtes, $uploadedDtes);
                    }
                }

                $date_start = Carbon::parse($request->start_date);
                $date_end = Carbon::parse($request->end_date);
                $months_string = $this->getMonthsString($date_start, $date_end);
                $years_string = $this->getYearsString($date_start, $date_end);

                $codes = $this->getCodesByBookType($book);
                if (empty($codes)) {
                    return redirect()->back()->with([
                        "error" => "Error",
                        "error_message" => "Tipo de libro no válido",
                    ]);
                }

                $dtes_filter = collect($dtes)->filter(function ($dte) use ($date_start, $date_end, $codes, $book, $onlySelected) {
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

                    if ($onlySelected) {
                        return in_array($tipoDte, $codes);
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
                // case 'retencion_iva':
                //     $file_path = $this->exportRetencionIva($dtes_filter, $datos_empresa, $months_string, $request);
                //     break;
                // case 'compras':
                //     $file_path = $this->exportCompras($request, $uploadedDtes, $onlySelected, $onlyMix);
                //     break;
                // case 'percepcion_iva':
                //     $file_path = $this->exportPercepcionIva($request, $uploadedDtes, $onlySelected, $onlyMix);
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
            case "compras_se":
                // Implementar exportación de anexo de compras SE si es necesario
                return $this->exportAnexoComprasSE($dtes, $tipoOperacionSe, $clasificacion, $sector, $tipo_costo);
            default:
                throw new \Exception('Tipo de anexo no válido');
        }
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
            $docFirst = $dtesGroup->first()["documento"] ?: [];
            $docLast = $dtesGroup->last()["documento"] ?: [];

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
            $respuesta_hacienda = $doc["respuesta_hacienda"] ?? [];
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

            $totalDocExentas += $resumen["totalExenta"] ?? 0;
            $totalDocGravadas += $resumen["totalGravada"] ?? 0;
            $totalDocNoSujetas += $resumen["totalNoSuj"] ?? 0;
            $totalIva += $iva;

            $sheet->setCellValue("A{$row}", $date);
            $sheet->setCellValue("B{$row}", "4.DOCUMENTO TRIBUTARIO ELECTRÓNICO (DTE)");
            $sheet->setCellValue("C{$row}", $tipo_documento);
            $sheet->setCellValue("D{$row}", $doc["identificacion"]["numeroControl"] ?? "");
            $sheet->setCellValue("E{$row}", $sello_recibido);
            $sheet->setCellValue("F{$row}", $doc["identificacion"]["codigoGeneracion"] ?? "");
            $sheet->setCellValue("G{$row}", $doc["receptor"]["nrc"] ?? "");
            $sheet->setCellValue("H{$row}", html_entity_decode($doc["receptor"]["nombre"] ?? "", ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $sheet->setCellValue("I{$row}", $resumen["totalExenta"] ?? 0);
            $sheet->setCellValue("J{$row}", $resumen["totalNoSuj"] ?? 0);
            $sheet->setCellValue("K{$row}", $resumen["totalGravada"] ?? 0);
            $sheet->setCellValue("L{$row}", $iva);
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


    private function processUploadedFiles($files)
    {
        $allDtes = [];

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'json') {
                $dtes = $this->processJsonFile($file);
                if (!empty($dtes)) {
                    $allDtes = array_merge($allDtes, $dtes);
                }
            } elseif ($extension === 'zip') {
                $dtes = $this->processZipFile($file);
                if (!empty($dtes)) {
                    $allDtes = array_merge($allDtes, $dtes);
                }
            }
        }

        return $allDtes;
    }

    private function processJsonFile($file)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $jsonData = json_decode($content, true);

            if ($jsonData === null) {
                return [];
            }

            $dte = [
                "documento" => json_encode($jsonData),
                "estado" => "PROCESADO",
                "source" => "uploaded_file"
            ];
            return [$dte];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function processZipFile($file)
    {
        try {
            $zip = new \ZipArchive();
            $result = $zip->open($file->getRealPath());
            $allDtes = [];

            if ($result === TRUE) {
                $tempDir = storage_path('app/temp/' . uniqid());
                mkdir($tempDir, 0755, true);

                $zip->extractTo($tempDir);
                $zip->close();

                $jsonFiles = glob($tempDir . '/*.json');

                foreach ($jsonFiles as $jsonFile) {
                    $content = file_get_contents($jsonFile);
                    $jsonData = json_decode($content, true);

                    if ($jsonData !== null) {
                        $dte = [
                            "documento" => json_encode($jsonData),
                            "estado" => "PROCESADO",
                            "source" => "uploaded_zip_file"
                        ];

                        $allDtes[] = $dte;
                    }
                }

                $this->deleteDirectory($tempDir);
            }

            return $allDtes;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir))
            return;

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
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
        if (auth()->user()->only_fcf) {
            $this->tipo_dte = '01'; // Default to Factura Electrónica if the user only wants FCF
        }

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
