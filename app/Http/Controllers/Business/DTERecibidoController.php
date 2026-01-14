<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadDtesFromHacienda;
use App\Models\Business;
use App\Models\DteImportProcess;
use App\Services\OctopusService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Session;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        return $pdf->download("DTE_{$codGeneracion}.pdf");
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
            'status' => 'pending',
        ]);

        // Despachar Job
        DownloadDtesFromHacienda::dispatch($importProcess, $nit);

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
}
