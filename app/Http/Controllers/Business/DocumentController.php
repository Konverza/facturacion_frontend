<?php


namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    public function index()
    {
        return view("business.documents.index");
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
