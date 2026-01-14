<?php


namespace App\Http\Controllers\Business;

use App\Services\OctopusService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{

    private $octopus_service;

    public function index()
    {
        return view("business.documents.index");
    }

    public function show(string $codGeneracion)
    {
        $this->octopus_service = new OctopusService();
        $dte = Http::get(env('OCTOPUS_API_URL') . "/dtes/{$codGeneracion}")->json();
        $catalogos = [
            'unidades_medidas' => $this->octopus_service->getCatalog("CAT-014"),
            'departamentos' => $this->octopus_service->simpleDepartamentos(),
            'tipos_documentos' => $this->octopus_service->getCatalog("CAT-022"),
            'actividades_economicas' => $this->octopus_service->getCatalog("CAT-019", null, true, true),
            'countries' => $this->octopus_service->getCatalog("CAT-020"),
            'recinto_fiscal' => $this->octopus_service->getCatalog("CAT-027", null, true, true),
            'regimen_exportacion' => $this->octopus_service->getCatalog("CAT-028", null, true, true),
            'tipos_establecimientos' => $this->octopus_service->getCatalog("CAT-009"),
            'formas_pago' => $this->octopus_service->getCatalog("CAT-017"),
            'tipo_servicio' => $this->octopus_service->getCatalog("CAT-010"),
            'modo_transporte' => $this->octopus_service->getCatalog("CAT-030"),
            'incoterms' => $this->octopus_service->getCatalog("CAT-031", null, true, true),
            'bienTitulo' => $this->octopus_service->getCatalog("CAT-025"),
        ];
        return view("business.documents.show", compact('dte', 'codGeneracion', 'catalogos'));
    }

    public function zipAndDownload(Request $request)
    {
        ini_set("max_execution_time", 0);
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        $desde = "{$request->desde}T00:00:00";
        $hasta = "{$request->hasta}T23:59:59";

        $dtes = Http::get(env("OCTOPUS_API_URL") . "/dtes/?nit=" . $business->nit . "&fechaInicio=" . $desde . "&fechaFin=" . $hasta)->json();
        $dtes = $dtes["items"] ?? [];
        // Convertir las fechas al formato ddmmyy
        $fecha_inicio_formateada = \DateTime::createFromFormat('Y-m-d\TH:i:s', $desde)->format('dmY');
        $fecha_fin_formateada = \DateTime::createFromFormat('Y-m-d\TH:i:s', $hasta)->format('dmY');
        // Crear y abrir el archivo ZIP
        $zip = new \ZipArchive();
        $zipFileName = "dtes_{$fecha_inicio_formateada}_{$fecha_fin_formateada}.zip";
        $zipFilePath = storage_path("app/public/{$zipFileName}");

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            // Recorrer los DTEs y agregar archivos al ZIP
            foreach ($dtes as $dte) {
                if ($dte["estado"] == "PROCESADO") {
                    $codGeneracion = $dte['codGeneracion'];
                    $fhProcesamiento = $dte['fhProcesamiento'];
                    $fechaProcesamiento = (new \DateTime($fhProcesamiento))->format('Y-m-d');
                    // Descargar el archivo pdf y json de "enlace_pdf" y "enlace_json" respectivamente, añadirlo al zip
                    $pdfContent = $dte["enlace_pdf"] ? Http::get($dte["enlace_pdf"])->body() : null;
                    $jsonContent = $dte["enlace_json"] ? Http::get($dte["enlace_json"])->body() : null;
                    $ticketContent = $dte["enlace_rtf"] ? Http::get($dte["enlace_rtf"])->body() : null;
                    if ($pdfContent) {
                        $zip->addFromString("{$fechaProcesamiento}/{$codGeneracion}/{$codGeneracion}.pdf", $pdfContent);
                    }
                    if ($jsonContent) {
                        $zip->addFromString("{$fechaProcesamiento}/{$codGeneracion}/{$codGeneracion}.json", $jsonContent);
                    }
                    if ($ticketContent) {
                        $zip->addFromString("{$fechaProcesamiento}/{$codGeneracion}/{$codGeneracion}_ticket.pdf", $ticketContent);
                    }
                }
            }
            $zip->close();
            // Descargar el archivo zip
            return response()->download($zipFilePath, $zipFileName, [
                'Content-Type' => 'text/csv',
                'X-Download-Started' => 'true',
                "X-File-Name" => $zipFileName,
            ])->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => 'Error al crear el archivo ZIP'
            ]);
        }
    }
}
