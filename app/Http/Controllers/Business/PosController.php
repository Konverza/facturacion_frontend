<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessUser;
use App\Models\PuntoVenta;
use Illuminate\Http\Request;
use App\Models\BusinessProduct;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Session;


class PosController extends Controller
{
    public $dte;

    public function __construct()
    {
        $this->dte = session("dte", []);
    }

    public function index(Request $request)
    {

        $business_id = Session::get('business') ?? null;
        $products = collect();
        $categories = collect();
        $category = $request->category ?? null;
        $business_user = BusinessUser::where("business_id", $business_id)
            ->where("user_id", auth()->user()->id)
            ->first();
        $default_pos = $business_user->default_pos_id ? PuntoVenta::with("sucursal")->find($business_user->default_pos_id) : null;
        if (!$default_pos) {
            // Si no hay un punto de venta predeterminado, redirigir con mensaje de error
            return redirect()->route('business.dashboard')->with('error', "Error")
                ->with(
                    "error_message",
                    "No hay un punto de venta predeterminado."
                );
        }
        // if search is empty and category is empty, get all products and categories without parents
        if (empty($request->search) && empty($request->category)) {
            $products = BusinessProduct::where('business_id', $business_id)
                ->where('category_id', null)
                ->get();
            $categories = ProductCategory::where('business_id', $business_id)
                ->where('parent_id', null)
                ->get();
        } elseif (!empty($request->search) && empty($request->category)) {
            // if search is not empty and category is empty, get all products and categories without parents
            $products = BusinessProduct::where('business_id', $business_id)
                ->where('category_id', null)
                ->where('descripcion', 'like', '%' . $request->search . '%')
                ->orWhere('codigo', 'like', '%' . $request->search . '%')
                ->get();
        } elseif (empty($request->search) && !empty($request->category)) {
            // if search is empty and category is not empty, get all products and categories with parents
            $products = BusinessProduct::where('business_id', $business_id)
                ->where('category_id', $request->category)
                ->get();
            $categories = ProductCategory::where('business_id', $business_id)
                ->where('parent_id', $request->category)
                ->get();
            $category = ProductCategory::find($request->category);
        } else {
            // if search is not empty and category is not empty, get all products and categories with parents
            $products = BusinessProduct::where('business_id', $business_id)
                ->where('category_id', $request->category)
                ->where('descripcion', 'like', '%' . $request->search . '%')
                ->orWhere('codigo', 'like', '%' . $request->search . '%')
                ->get();
            $categories = ProductCategory::where('business_id', $business_id)
                ->where('parent_id', null)
                ->get();
            $category = ProductCategory::find($request->category);
        }

        if (session()->has("dte") && session("dte.type") !== "01") {
            session()->forget("dte");
            $this->dte = [];
        }
        $this->dte["type"] = "01";
        session(["dte" => $this->dte]);

        return view(
            'business.pos.index'
            ,
            [
                'products' => $products,
                'categories' => $categories,
                'search' => $request->search,
                'category' => $category,
                'default_pos' => $default_pos,
            ]
        );
    }
}
