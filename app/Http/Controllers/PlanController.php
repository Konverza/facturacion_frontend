<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\DataTables\PlansDataTable;

class PlanController extends Controller
{
    public function index(PlansDataTable $dataTable)
    {
        return $dataTable->render('admin.planes');
    }

    public function mejorar_plan()
    {
        return view('admin.mejorar_plan');
    }

    public function store(Request $request)
    {
        // Validar los datos con mensajes personalizados
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_adicional' => 'required|numeric|min:0',
            'limite' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',
            'precio.required' => 'El campo precio es obligatorio.',
            'precio.numeric' => 'El campo precio debe ser un número.',
            'precio.min' => 'El campo precio debe ser al menos 0.',
            'precio_adicional.required' => 'El campo precio es obligatorio.',
            'precio_adicional.numeric' => 'El campo precio debe ser un número.',
            'precio_adicional.min' => 'El campo precio debe ser al menos 0.',
            'limite.required' => 'El campo límite es obligatorio.',
            'limite.integer' => 'El campo límite debe ser un número entero.',
            'limite.min' => 'El campo límite debe ser al menos 1.',
        ]);

        // Crear el plan
        $plan = new Plan();
        $plan->nombre = $request->nombre;
        $plan->precio = $request->precio;
        $plan->precio_adicional = $request->precio_adicional;
        $plan->limite = $request->limite;
        $plan->save();

        // Redireccionar
        return redirect()->route('admin.planes')->with('success', 'Plan creado correctamente');
    }

    public function read($id)
    {
        $plan = Plan::findOrFail($id);
        return json_encode($plan);
    }

    public function update(Request $request, $id)
    {
        // Validar los datos con mensajes personalizados
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'precio_adicional' => 'required|numeric|min:0',
            'limite' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max' => 'El campo nombre no puede exceder los 255 caracteres.',
            'precio.required' => 'El campo precio es obligatorio.',
            'precio.numeric' => 'El campo precio debe ser un número.',
            'precio.min' => 'El campo precio debe ser al menos 0.',
            'precio_adicional.required' => 'El campo precio es obligatorio.',
            'precio_adicional.numeric' => 'El campo precio debe ser un número.',
            'precio_adicional.min' => 'El campo precio debe ser al menos 0.',
            'limite.required' => 'El campo límite es obligatorio.',
            'limite.integer' => 'El campo límite debe ser un número entero.',
            'limite.min' => 'El campo límite debe ser al menos 1.',
        ]);

        // Actualizar el plan
        $plan = Plan::findOrFail($id);
        $plan->nombre = $request->nombre;
        $plan->precio = $request->precio;
        $plan->precio_adicional = $request->precio_adicional;
        $plan->limite = $request->limite;
        $plan->save();

        // Redireccionar
        return redirect()->route('admin.planes')->with('success', 'Plan actualizado correctamente');
    }
}
