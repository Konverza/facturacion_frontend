<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessQuotationDeliveryTime;
use Illuminate\Http\Request;

class QuotationDeliveryTimeController extends Controller
{
    public function index()
    {
        $deliveryTimes = BusinessQuotationDeliveryTime::where('business_id', session('business'))
            ->orderBy('name')
            ->get();

        return view('business.quotation-delivery-times.index', [
            'deliveryTimes' => $deliveryTimes,
        ]);
    }

    public function store(Request $request)
    {
        $businessId = (int) session('business');

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_quotation_delivery_times,name,NULL,id,business_id,' . $businessId,
        ]);

        $deliveryTime = BusinessQuotationDeliveryTime::create([
            'business_id' => $businessId,
            'name' => trim($validated['name']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $deliveryTime->id,
                'name' => $deliveryTime->name,
            ], 201);
        }

        return back()->with('success', 'Tiempo de entrega creado correctamente');
    }

    public function update(Request $request, string $id)
    {
        $businessId = (int) session('business');
        $deliveryTime = BusinessQuotationDeliveryTime::where('business_id', $businessId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_quotation_delivery_times,name,' . $deliveryTime->id . ',id,business_id,' . $businessId,
        ]);

        $deliveryTime->update([
            'name' => trim($validated['name']),
        ]);

        return back()->with('success', 'Tiempo de entrega actualizado correctamente');
    }

    public function destroy(string $id)
    {
        $deliveryTime = BusinessQuotationDeliveryTime::where('business_id', session('business'))->findOrFail($id);
        $deliveryTime->delete();

        return back()->with('success', 'Tiempo de entrega eliminado correctamente');
    }
}
