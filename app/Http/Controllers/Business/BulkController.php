<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessCustomer;
use Illuminate\Http\Request;
use App\Models\DTE;
use Illuminate\Support\Facades\Session;

class BulkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_id = Session::get('business') ?? null;
        $templates = DTE::where('business_id', $business_id)
            ->where("status", "template")->get();
        return view('business.bulk.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function send()
    {
        $business_id = Session::get('business') ?? null;
        $templates = DTE::where('business_id', $business_id)
            ->where("status", "template")->get()->pluck('name', 'id')->toArray();
        $customers = BusinessCustomer::where('business_id', $business_id)->get();
        return view('business.bulk.send', compact('templates', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $business_id = Session::get('business') ?? null;
            $template = DTE::where('business_id', $business_id)
                ->where('id', $id)
                ->where("status", "template")
                ->first();
            if (!$template) {
                return redirect()->route('business.bulk.index')->with([
                    'error' => 'Error',
                    'error_message' => 'No se encontró la plantilla'
                ]);
            }
            $template->delete();
            return redirect()->route('business.bulk.index')->with([
                'success' => 'Éxito',
                'success_message' => 'Plantilla eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.bulk.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al eliminar la plantilla'
            ]);
        }
    }

    /**
     * Devuelve el contenido JSON de una plantilla DTE por id (scope del business actual).
     */
    public function template(string $id)
    {
        try {
            $business_id = Session::get('business') ?? null;
            if (!$business_id) {
                return response()->json(['success' => false, 'message' => 'Sesión de empresa no encontrada'], 401);
            }
            $template = DTE::where('business_id', $business_id)
                ->where('id', $id)
                ->where('status', 'template')
                ->first();
            if (!$template) {
                return response()->json(['success' => false, 'message' => 'Plantilla no encontrada'], 404);
            }
            return response()->json([
                'success' => true,
                'dte' => json_decode($template->content, true),
                'type' => $template->type,
                'name' => $template->name,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener la plantilla'], 500);
        }
    }
}
