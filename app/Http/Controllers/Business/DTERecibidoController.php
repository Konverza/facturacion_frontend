<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadDtesFromHacienda;
use App\Models\Business;
use App\Models\DteImportProcess;
use App\Models\ReceivedZipDownloadJob;
use App\Jobs\GenerateReceivedZipDownload;
use App\Services\HaciendaService;
use App\Services\OctopusService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DTERecibidoController extends Controller
{
    private $octopus_service;

    public function __construct()
    {
        $this->octopus_service = new OctopusService();
    }
    public function index()
    {
        return view("business.received_documents.index");
    }

    public function show(string $codGeneracion)
    {
        $dte = Http::get(env('OCTOPUS_API_URL') . "/dtes_recibidos/{$codGeneracion}")->json();
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
        return view("business.received_documents.show", compact('dte', 'codGeneracion', 'catalogos'));
    }

    public function destroy(string $codGeneracion)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business || !$business->nit) {
            return redirect()->route('business.select')->with([
                'error' => 'Error',
                'error_message' => 'Debe seleccionar un negocio válido para eliminar el DTE.',
            ]);
        }

        $response = Http::delete(env('OCTOPUS_API_URL') . "/dtes_recibidos/{$codGeneracion}", [
            'nit' => $business->nit,
        ]);

        if (!$response->successful()) {
            $responseBody = $response->json();
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => is_array($responseBody)
                    ? ($responseBody['message'] ?? $responseBody['detail'] ?? 'No fue posible eliminar el DTE recibido.')
                    : 'No fue posible eliminar el DTE recibido.',
            ]);
        }

        return redirect()->back()->with([
            'success' => 'Éxito',
            'success_message' => 'El DTE recibido fue eliminado correctamente.',
        ]);
    }

    public function downloadPdf(string $codGeneracion)
    {
        $dte = Http::get(env('OCTOPUS_API_URL') . "/dtes_recibidos/{$codGeneracion}")->json();
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
        
        $pdf = Pdf::loadView('business.received_documents.pdf', compact('dte', 'codGeneracion', 'catalogos'))
            ->setPaper('letter', 'portrait');
        
        return $pdf->stream("DTE_{$codGeneracion}.pdf");
    }

    public function importIndex()
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        
        if (!$business || !$business->nit) {
            return redirect()->route('business.select')->with('error', 'Debe seleccionar un negocio primero.');
        }
        
        $nit = $business->nit;

        // Obtener el proceso activo si existe
        $activeProcess = DteImportProcess::where('nit', $nit)
            ->whereIn('status', ['pending', 'downloading', 'processing'])
            ->latest()
            ->first();

        // Obtener historial de procesos
        $processHistory = DteImportProcess::where('nit', $nit)
            ->whereIn('status', ['completed', 'failed'])
            ->latest()
            ->limit(10)
            ->get();

        return view("business.received_documents.import", compact('activeProcess', 'processHistory'));
    }

    public function manualUploadIndex()
    {
        return view("business.received_documents.manual-upload");
    }

    public function manualUploadStore(Request $request)
    {
        $request->validate([
            'dte_json_files' => 'required|array|min:1',
            'dte_json_files.*' => 'required|file|mimes:json,txt|max:5120',
        ]);

        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);

        if (!$business || !$business->nit) {
            return redirect()->route('business.select')->with([
                'error' => 'Error',
                'error_message' => 'Debe seleccionar un negocio válido para cargar el DTE.',
            ]);
        }

        $haciendaService = new HaciendaService($business->nit, $business->dui ?? null);

        $files = $request->file('dte_json_files', []);
        $results = [];
        $processed = 0;
        $failed = 0;

        foreach ($files as $file) {
            $result = $this->processManualJsonUpload($file, $haciendaService, $business->nit);
            $results[] = $result;

            if ($result['success']) {
                $processed++;
            } else {
                $failed++;
            }
        }

        $summary = [
            'total' => count($files),
            'processed' => $processed,
            'failed' => $failed,
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $failed === 0,
                'summary' => $summary,
                'results' => $results,
                'message' => $failed === 0
                    ? 'Todos los DTEs fueron cargados correctamente.'
                    : 'La carga finalizó con errores en algunos archivos.',
            ]);
        }

        if ($failed > 0) {
            return redirect()->back()->with([
                'warning' => 'Carga completada con observaciones',
                'warning_message' => "Se procesaron {$processed} de {$summary['total']} archivos. {$failed} fallaron.",
            ]);
        }

        return redirect()->route('business.received-documents.index')->with([
            'success' => 'Éxito',
            'success_message' => "Se cargaron correctamente {$processed} DTE(s).",
        ]);
    }

    private function processManualJsonUpload(UploadedFile $file, HaciendaService $haciendaService, string $nit): array
    {
        $jsonContent = file_get_contents($file->getRealPath());
        $decoded = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [
                'file_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'success' => false,
                'message' => 'El archivo no contiene un JSON válido.',
            ];
        }

        $dte = isset($decoded['dte']) && is_array($decoded['dte']) ? $decoded['dte'] : $decoded;
        $response = $haciendaService->sendDteToOctopus($dte, $nit, true);

        if (!is_array($response) || ($response['error'] ?? true)) {
            return [
                'file_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'success' => false,
                'message' => $response['message'] ?? 'No fue posible guardar el DTE.',
            ];
        }

        return [
            'file_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'success' => true,
            'message' => $response['message'] ?? 'DTE cargado correctamente.',
        ];
    }

    public function startImport(Request $request)
    {
        $business_id = Session::get('business') ?? null;
        $business = Business::find($business_id);
        
        if (!$business || !$business->nit) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un negocio válido en la sesión.'
            ], 400);
        }
        
        $nit = $business->nit;
        if($business->different_id){
            $dui = $business->dui;
        }

        // Verificar si hay un proceso activo
        $activeProcess = DteImportProcess::where('nit', $nit)
            ->whereIn('status', ['pending', 'downloading', 'processing'])
            ->exists();

        if ($activeProcess) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un proceso de importación en curso. Por favor espere a que termine.'
            ], 422);
        }

        // Crear nuevo proceso
        $importProcess = DteImportProcess::create([
            'nit' => $nit,
            'dui' => $dui ?? null,
            'status' => 'pending',
        ]);

        // Despachar Job
        DownloadDtesFromHacienda::dispatch($importProcess, $nit, $dui ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Proceso de importación iniciado correctamente.',
            'process_id' => $importProcess->id
        ]);
    }

    public function getProgress($id)
    {
        $process = DteImportProcess::findOrFail($id);

        return response()->json([
            'success' => true,
            'process' => [
                'id' => $process->id,
                'status' => $process->status,
                'progress_percentage' => $process->progress_percentage,
                'total_dtes' => $process->total_dtes,
                'processed_dtes' => $process->processed_dtes,
                'failed_dtes' => $process->failed_dtes,
                'started_at' => $process->started_at?->diffForHumans(),
                'completed_at' => $process->completed_at?->diffForHumans(),
                'error_message' => $process->error_message,
                'is_in_progress' => $process->isInProgress(),
            ]
        ]);
    }

    /**
     * Vista de gestión de descargas ZIP de DTEs recibidos
     */
    public function zipDownloads()
    {
        $business_id = Session::get('business');
        $business = Business::find($business_id);

        $activeJob = ReceivedZipDownloadJob::getActiveJobForBusiness($business_id);

        $query = ReceivedZipDownloadJob::where('business_id', $business_id)
            ->orderBy('created_at', 'desc');

        if ($activeJob) {
            $query->where('id', '!=', $activeJob->id);
        }

        $recentJobs = $query->take(10)->get();

        if ($activeJob) {
            $recentJobs = collect([$activeJob])->concat($recentJobs);
        }

        $tipos_dte = [
            '' => 'Todos',
            '01' => 'Factura Consumidor Final',
            '03' => 'Comprobante de crédito fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de crédito',
            '06' => 'Nota de débito',
            '07' => 'Comprobante de retención',
            '08' => 'Comprobante de liquidación',
            '09' => 'Documento Contable de Liquidación',
            '11' => 'Factura de exportación',
            '14' => 'Factura de sujeto excluido',
            '15' => 'Comprobante de Donación'
        ];

        $emisores_unicos = [];
        $nit = $business->nit ?? null;

        $response_emisores = Http::get(env("OCTOPUS_API_URL") . "/dtes_recibidos/emisor-list/{$nit}");
        $emisores = $response_emisores->json() ?? [];
        foreach ($emisores as $emisor) {
            if (isset($emisor['documento_emisor'], $emisor['nombre_emisor'])) {
                $emisores_unicos[$emisor['documento_emisor']] = $emisor['nombre_emisor'];
            }
        }
        $emisores_unicos = array_merge(['' => 'Todos'], $emisores_unicos);

        return view('business.received_documents.zip-downloads', compact(
            'activeJob',
            'recentJobs',
            'tipos_dte',
            'emisores_unicos'
        ));
    }

    /**
     * Crear solicitud de descarga ZIP para DTEs recibidos
     */
    public function createZipDownload(Request $request)
    {
        $request->validate([
            'emision_inicio' => 'required|date',
            'emision_fin' => 'required|date|after_or_equal:emision_inicio',
            'tipo_dte' => 'nullable|string',
            'documento_emisor' => 'nullable|string',
            'busqueda' => 'nullable|string',
        ]);

        $business_id = Session::get('business');

        if (ReceivedZipDownloadJob::hasActiveJobForBusiness($business_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una solicitud de descarga en proceso. Por favor espere a que finalice.'
            ], 422);
        }

        $zipJob = ReceivedZipDownloadJob::create([
            'business_id' => $business_id,
            'fecha_inicio' => $request->emision_inicio,
            'fecha_fin' => $request->emision_fin,
            'tipo_dte' => $request->tipo_dte,
            'documento_emisor' => $request->documento_emisor,
            'busqueda' => $request->busqueda,
            'status' => 'pending',
        ]);

        GenerateReceivedZipDownload::dispatch($zipJob);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de descarga creada. El proceso comenzará en breve.',
            'job_id' => $zipJob->id,
        ]);
    }

    /**
     * Obtener el estado de un trabajo ZIP de DTEs recibidos
     */
    public function getZipStatus($id)
    {
        $business_id = Session::get('business');
        $zipJob = ReceivedZipDownloadJob::where('id', $id)
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
        $zipJob = ReceivedZipDownloadJob::where('id', $id)
            ->where('business_id', $business_id)
            ->where('status', 'completed')
            ->firstOrFail();

        if (!$zipJob->fileExists()) {
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => 'El archivo no está disponible.'
            ]);
        }

        $stream = Storage::disk('s3')->readStream($zipJob->file_path);
        if (!$stream) {
            return redirect()->back()->with([
                'error' => 'Error',
                'error_message' => 'No se pudo abrir el archivo para descarga.'
            ]);
        }

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, $zipJob->file_name);
    }

    /**
     * Cancelar/eliminar un trabajo ZIP de DTEs recibidos
     */
    public function deleteZipJob($id)
    {
        $business_id = Session::get('business');
        $zipJob = ReceivedZipDownloadJob::where('id', $id)
            ->where('business_id', $business_id)
            ->firstOrFail();

        $zipJob->deleteFile();
        $zipJob->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trabajo eliminado correctamente.'
        ]);
    }
}
