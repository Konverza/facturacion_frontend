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
        try {
            $business_id = Session::get('business') ?? null;
            $categories = ProductCategory::where('business_id', $business_id)->get(["id", "name"]);

            // Return categories as ['id' => 'name', 'id' => 'name']
            $categories = $categories->mapWithKeys(function ($category_val) {
                return [$category_val->id => $category_val->name];
            })->toArray();
            $categories = array_merge(["0" => "Sin padre"], $categories);

            return view("business.categories.create", [
                "categories" => $categories
            ]);
        } catch (\Exception $e) {
            return redirect()->route('business.categories.index')->with([
                'error' => 'Error',
                'error_message' => 'Error al cargar las categorias'
            ]);
        }
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

            // Check if the category has a parent category
            if ($request->has('parent_id')) {
                $parentCategory = ProductCategory::find($request->parent_id);
                if ($parentCategory) {
                    $category->parent_id = $parentCategory->id;
                }
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
            $business_id = Session::get('business') ?? null;
            $categories = ProductCategory::where('business_id', $business_id)
                            ->where('id', '!=', $category->id)
                            ->get(["id", "name"]);
            
            // Return categories as ['id' => 'name', 'id' => 'name']
            $categories = $categories->mapWithKeys(function ($category_val) {
                return [$category_val->id => $category_val->name];
            })->toArray();
            $categories = array_merge(["0" => "Sin padre"], $categories);

            return view("business.categories.edit", [
                "category" => $category,
                "categories" => $categories
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
            // Check if the category has a parent category
            if ($request->has('parent_id')) {
                $parentCategory = ProductCategory::find($request->parent_id);
                if ($parentCategory) {
                    $category->parent_id = $parentCategory->id;
                } else {
                    $category->parent_id = null; // Set to null if no parent category is found
                }
            }
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

            // Set null all child categories
            $childCategories = ProductCategory::where('parent_id', $category->id)->get();
            foreach ($childCategories as $childCategory) {
                $childCategory->parent_id = null; // Set the foreign key to null
                $childCategory->save(); // Save the child category
            }
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
