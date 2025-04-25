<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Session;


class CategoryController extends Controller
{
    public function index()
    {
        try {
            $business_id = Session::get('business') ?? null;
            $categories = ProductCategory::where('business_id', $business_id)->get();
            return view("business.categories.index", [
                "categories" => $categories
            ]);

        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar las categorias'
            ]);
        }
    }

    public function create()
    {
        return view("business.categories.create");
    }

    public function store(Request $request)
    {
        try {
            $business_id = Session::get('business') ?? null;
            $category = new ProductCategory();
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $category->image_url = "/storage/$path";
            }
            $category->save();
            return redirect()->route('business.categories.index')->with([
                'success' => 'Exito',
                'success_message' => 'Categoria creada correctamente'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al crear la categoria'
            ]);
        }
    }
}
