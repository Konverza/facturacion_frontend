<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\BusinessQuotationPaymentMethod;
use Illuminate\Http\Request;

class QuotationPaymentMethodController extends Controller
{
    public function index()
    {
        $methods = BusinessQuotationPaymentMethod::where('business_id', session('business'))
            ->orderBy('name')
            ->get();

        return view('business.quotation-payment-methods.index', [
            'methods' => $methods,
        ]);
    }

    public function store(Request $request)
    {
        $businessId = (int) session('business');

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_quotation_payment_methods,name,NULL,id,business_id,' . $businessId,
        ]);

        $method = BusinessQuotationPaymentMethod::create([
            'business_id' => $businessId,
            'name' => trim($validated['name']),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $method->id,
                'name' => $method->name,
            ], 201);
        }

        return back()->with('success', 'Forma de pago creada correctamente');
    }

    public function update(Request $request, string $id)
    {
        $businessId = (int) session('business');
        $method = BusinessQuotationPaymentMethod::where('business_id', $businessId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:business_quotation_payment_methods,name,' . $method->id . ',id,business_id,' . $businessId,
        ]);

        $method->update([
            'name' => trim($validated['name']),
        ]);

        return back()->with('success', 'Forma de pago actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $method = BusinessQuotationPaymentMethod::where('business_id', session('business'))->findOrFail($id);
        $method->delete();

        return back()->with('success', 'Forma de pago eliminada correctamente');
    }
}
