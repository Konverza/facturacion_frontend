<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                "name" => "required|string|max:255",
                "price" => "required|numeric|min:0",
                "price_aditional" => "required|numeric|min:0",
                "limit" => "required|integer|min:1",
            ]
        );

        DB::beginTransaction();
        try {
            $plan = new Plan();
            $plan->nombre = $request->name;
            $plan->precio = $request->price;
            $plan->precio_adicional = $request->price_aditional;
            $plan->limite = $request->limit;
            $plan->save();
            DB::commit();
            return redirect()->route('admin.plans.index')
                ->with('success', 'Plan creado')
                ->with("success_message", "El plan se ha creado correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.plans.index')
                ->with('error', '¡Ooops!')
                ->with("error_message", "Ha ocurrido un error al crear el plan.");
        }
    }

    public function edit(string $id){
        $plan = Plan::findOrFail($id);
        return response()->json($plan);
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $plan = Plan::findOrFail($id);
            $plan->delete();
            DB::commit();
            return redirect()->route('admin.plans.index')
                ->with('success', 'Plan eliminado')->with("success_message", "Plan eliminado correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.plans.index')->with('error', 'Ocurrió un error al eliminar el plan.');
        }
    }

    public function update (Request $request, string $id){
        $request->validate(
            [
                "name" => "required|string|max:255",
                "price" => "required|numeric|min:0",
                "price_aditional" => "required|numeric|min:0",
                "limit" => "required|integer|min:1",
            ]
        );

        DB::beginTransaction();
        try {
            $plan = Plan::findOrFail($id);
            $plan->nombre = $request->name;
            $plan->precio = $request->price;
            $plan->precio_adicional = $request->price_aditional;
            $plan->limite = $request->limit;
            $plan->save();
            DB::commit();
            return redirect()->route('admin.plans.index')
                ->with('success', 'Plan actualizado')
                ->with("success_message", "El plan se ha actualizado correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.plans.index')
                ->with('error', '¡Ooops!')
                ->with("error_message", "Ha ocurrido un error al actualizar el plan.");
        }
    }
}
