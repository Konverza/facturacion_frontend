<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessPriceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceVariantController extends Controller
{
    public function index()
    {
        $business = Business::find(session('business'));
        $variants = BusinessPriceVariant::where('business_id', session('business'))
            ->orderBy('name')
            ->get();

        return view('business.price-variants.index', [
            'business' => $business,
            'variants' => $variants,
        ]);
    }

    public function store(Request $request)
    {
        $businessId = session('business');

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_price_variants,name,NULL,id,business_id,' . $businessId,
        ]);

        BusinessPriceVariant::create([
            'business_id' => $businessId,
            'name' => $validated['name'],
            'price_without_iva' => null,
            'price_with_iva' => null,
        ]);

        return back()->with('success', 'Variante creada correctamente');
    }

    public function update(Request $request, string $id)
    {
        $businessId = session('business');
        $variant = BusinessPriceVariant::where('business_id', $businessId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_price_variants,name,' . $variant->id . ',id,business_id,' . $businessId,
        ]);

        $variant->update([
            'name' => $validated['name'],
            'price_without_iva' => $variant->price_without_iva,
            'price_with_iva' => $variant->price_with_iva,
        ]);

        return back()->with('success', 'Variante actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $variant = BusinessPriceVariant::where('business_id', session('business'))->findOrFail($id);
        $variant->delete();

        return back()->with('success', 'Variante eliminada correctamente');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'price_variants_enabled' => 'nullable|boolean',
        ]);

        $business = Business::find(session('business'));
        if (!$business) {
            return back()->with('error', 'Negocio no encontrado');
        }

        DB::transaction(function () use ($business, $request) {
            $enabled = $request->has('price_variants_enabled');
            $business->price_variants_enabled = $enabled;

            if ($enabled) {
                $business->show_special_prices = false;
            }

            $business->save();
        });

        return back()->with('success', 'Configuración actualizada');
    }
}
