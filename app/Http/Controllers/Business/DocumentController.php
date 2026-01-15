<?php


namespace App\Http\Controllers\Business;

use App\Services\OctopusService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use App\Models\ZipDownloadJob;
use App\Jobs\GenerateZipDownload;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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
                'Content-Type' => 'application/zip',
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

    /**
     * Vista de gestión de descargas ZIP
     */
    public function zipDownloads()
    {
        $business_id = Session::get('business');
        $activeJob = ZipDownloadJob::getActiveJobForBusiness($business_id);
        
        // Obtener trabajos recientes (excluyendo el activo si existe)
        $query = ZipDownloadJob::where('business_id', $business_id)
            ->orderBy('created_at', 'desc');
        
        if ($activeJob) {
            $query->where('id', '!=', $activeJob->id);
        }
        
        $recentJobs = $query->take(10)->get();
        
        // Si hay trabajo activo, agregarlo al inicio de la colección
        if ($activeJob) {
            $recentJobs = collect([$activeJob])->concat($recentJobs);
        }

        return view('business.documents.zip-downloads', compact('activeJob', 'recentJobs'));
    }

    /**
     * Crear solicitud de descarga ZIP
     */
    public function createZipDownload(Request $request)
    {
        $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $business_id = Session::get('business');

        // Verificar si ya hay un trabajo activo
        if (ZipDownloadJob::hasActiveJobForBusiness($business_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una solicitud de descarga en proceso. Por favor espere a que finalice.'
            ], 422);
        }

        // Crear el registro del trabajo
        $zipJob = ZipDownloadJob::create([
            'business_id' => $business_id,
            'fecha_inicio' => $request->desde,
            'fecha_fin' => $request->hasta,
            'status' => 'pending',
        ]);

        // Despachar el Job
        GenerateZipDownload::dispatch($zipJob);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de descarga creada. El proceso comenzará en breve.',
            'job_id' => $zipJob->id,
        ]);
    }

    /**
     * Obtener el estado de un trabajo ZIP
     */
    public function getZipStatus($id)
    {
        $business_id = Session::get('business');
        $zipJob = ZipDownloadJob::where('id', $id)
            ->where('business_id', $business_id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $zipJob->id,
                'status' => $zipJob->status,
                'progress' => $zipJob->getProgressPercentage(),
                'processed_dtes' => $zipJob->processed_dtes,
                'total_dtes' => $zipJob->total_dtes,
                'file_name' => $zipJob->file_name,
                'error_message' => $zipJob->error_message,
                'created_at' => $zipJob->created_at->format('d/m/Y H:i'),
                'can_download' => $zipJob->status === 'completed' && $zipJob->fileExists(),
            ]
        ]);
    }

    /**
     * Descargar archivo ZIP generado desde S3
     */
    public function downloadZip($id)
    {
        $business_id = Session::get('business');
        $zipJob = ZipDownloadJob::where('id', $id)
            ->where('business_id', $business_id)
            ->where('status', 'completed')
            ->firstOrFail();

        if (!$zipJob->fileExists()) {
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => 'El archivo no está disponible.'
            ]);
        }

        // Descargar desde S3 y hacer streaming al usuario
        return Storage::disk('s3')->download($zipJob->file_path, $zipJob->file_name);
    }

    /**
     * Cancelar/eliminar un trabajo ZIP
     */
    public function deleteZipJob($id)
    {
        $business_id = Session::get('business');
        $zipJob = ZipDownloadJob::where('id', $id)
            ->where('business_id', $business_id)
            ->firstOrFail();

        // Eliminar archivo si existe
        $zipJob->deleteFile();

        // Eliminar registro
        $zipJob->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trabajo eliminado correctamente.'
        ]);
    }
}