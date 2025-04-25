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

    public function edit($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            return view("business.categories.edit", [
                "category" => $category
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar la categoria'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                // Unlink the old image if it exists
                if ($category->image_url) {
                    $oldImagePath = public_path($category->image_url);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                // Store the new image
                $path = $request->file('image')->store('categories', 'public');
                $category->image_url = "/storage/$path";
            }
            $category->save();
            return redirect()->route('business.categories.index')->with([
                'success' => 'Exito',
                'success_message' => 'Categoria actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al actualizar la categoria'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);
            // Unlink the image if it exists
            if ($category->image_url) {
                $oldImagePath = public_path($category->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Check if the category has products associated with it and set the foreign key to null
            $products = $category->products()->get();
            if ($products->isNotEmpty()) {
                foreach ($products as $product) {
                    $product->category_id = null; // Set the foreign key to null
                    $product->save(); // Save the product
                }
            }
            // Delete the category
            $category->delete();
            return redirect()->route('business.categories.index')->with([
                'success' => 'Exito',
                'success_message' => 'Categoria eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al eliminar la categoria'
            ]);
        }
    }
}
