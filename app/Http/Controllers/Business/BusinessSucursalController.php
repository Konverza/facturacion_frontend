<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\PuntoVenta;
use App\Models\Sucursal;
use App\Services\OctopusService;

class BusinessSucursalController extends Controller
{
    private $octopusService;

    public function __construct()
    {
        $this->octopusService = new OctopusService();
    }

    public function index(int $business_id)
    {
        $this->checkBusinessAccess($business_id);
        $sucursales = Sucursal::where('business_id', $business_id)
            ->with(['puntosVentas'])
            ->get();
        $business = Business::findOrFail($business_id);
        $departamentos = $this->octopusService->getCatalog("CAT-012");

        return view('business.sucursales.index', [
            'sucursales' => $sucursales,
            'business' => $business,
            'departamentos' => $departamentos,
        ]);
    }

    public function edit(int $business_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $sucursal = Sucursal::findOrFail($id);
        return response()->json($sucursal);
    }

    public function store_sucursal(int $business_id)
    {
        $this->checkBusinessAccess($business_id);

        $validator = validator(request()->all(), [
            'nombre' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'codSucursal' => 'required|string|max:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $data = $validator->validated();
        $data['business_id'] = $business_id;

        Sucursal::create($data);

        return redirect()->route('business.sucursales.index', $business_id)
            ->with('success', 'Sucursal Creada')
            ->with("success_message", "Sucursal creada exitosamente.");
    }

    public function update_sucursal(int $business_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $validator = validator(request()->all(), [
            'nombre' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'codSucursal' => 'required|string|max:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $data = $validator->validated();
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->update($data);

        return redirect()->route('business.sucursales.index', $business_id)
            ->with('success', 'Sucursal Actualizada')
            ->with("success_message", "Sucursal actualizada exitosamente.");
    }

    public function delete_sucursal(int $business_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $sucursal = Sucursal::findOrFail($id);
        // Check if the Sucursal has associated Puntos de Venta
        if ($sucursal->puntosVentas()->count() > 0) {
            return redirect()->route('business.sucursales.index', $business_id)
                ->with('error', 'No se puede eliminar.')
                ->with("error_message", "No se puede eliminar la sucursal porque tiene puntos de venta asociados.");
        }
        $sucursal->delete();

        return redirect()->route('business.sucursales.index', $business_id)
            ->with('success', 'Sucursal Eliminada')
            ->with("success_message", "Sucursal eliminada exitosamente.");
    }

    public function index_puntos_venta(int $business_id, int $sucursal_id)
    {
        $this->checkBusinessAccess($business_id);

        $puntos_venta = PuntoVenta::where('sucursal_id', $sucursal_id)->get();
        $sucursal = Sucursal::findOrFail($sucursal_id);

        return view('business.sucursales.puntos_venta.index', [
            'puntos_venta' => $puntos_venta,
            'sucursal' => $sucursal,
            'business_id' => $business_id,
        ]);
    }

    public function store_punto_venta(int $business_id, int $sucursal_id)
    {
        $this->checkBusinessAccess($business_id);

        $validator = validator(request()->all(), [
            'nombre' => 'required|string|max:255',
            'codPuntoVenta' => 'required|string|max:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['sucursal_id'] = $sucursal_id;

        PuntoVenta::create($data);

        return redirect()->route('business.puntos-venta.index', ['business_id' => $business_id, 'sucursal_id' => $sucursal_id])
            ->with('success', 'Punto de Venta Creado')
            ->with("success_message", "Punto de venta creado exitosamente.");
    }

    public function edit_punto_venta(int $business_id, int $sucursal_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $punto_venta = PuntoVenta::findOrFail($id);
        return response()->json($punto_venta);
    }

    public function update_punto_venta(int $business_id, int $sucursal_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $validator = validator(request()->all(), [
            'nombre' => 'required|string|max:255',
            'codPuntoVenta' => 'required|string|max:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $punto_venta = PuntoVenta::findOrFail($id);
        $punto_venta->update($data);

        return redirect()->route('business.puntos-venta.index', ['business_id' => $business_id, 'sucursal_id' => $sucursal_id])
            ->with('success', 'Punto de Venta Actualizado')
            ->with("success_message", "Punto de venta actualizado exitosamente.");
    }

    public function delete_punto_venta(int $business_id, int $sucursal_id, int $id)
    {
        $this->checkBusinessAccess($business_id);

        $punto_venta = PuntoVenta::findOrFail($id);
        $punto_venta->delete();

        return redirect()->route('business.puntos-venta.index', ['business_id' => $business_id, 'sucursal_id' => $sucursal_id])
            ->with('success', 'Punto de Venta Eliminado')
            ->with("success_message", "Punto de venta eliminado exitosamente.");
    }

    private function checkBusinessAccess(int $business_id)
    {
        // Verify if user has access to the business
        $business_user = BusinessUser::where('business_id', $business_id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$business_user) {
            return redirect()->route('business.index')
                ->with('error', 'Acceso Denegado')
                ->with("error_message", "No tienes acceso a este negocio.");
        }
    }

}
