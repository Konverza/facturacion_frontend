<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadDtesFromHacienda;
use App\Models\Business;
use App\Models\DteImportProcess;
use Illuminate\Http\Request;
use Session;

class DTERecibidoController extends Controller
{
    public function index()
    {
        return view("business.received_documents.index");
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
