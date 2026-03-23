<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_producto' => 'required|int',
            'codigo' => 'required|string',
            'unidad_medida' => 'required|string',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric',
            'precio_sin_iva' => 'required|numeric',
            'stock_inicial' => 'nullable|numeric',
            'tributos' => 'required|array',
            'stock_minimo' => 'nullable|numeric',
            'has_stock' => 'nullable|boolean',
            'category_id' => 'nullable|int',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'special_price' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'margin' => 'nullable|numeric',
            'special_price_with_iva' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'price_variants' => 'nullable|array',
            'price_variants.*.price_without_iva' => 'nullable|numeric',
            'price_variants.*.price_with_iva' => 'nullable|numeric',
            'new_price_variants' => 'nullable|array',
            'new_price_variants.*.name' => 'required_with:new_price_variants.*.price_without_iva,new_price_variants.*.price_with_iva|nullable|string|max:150',
            'new_price_variants.*.price_without_iva' => 'nullable|numeric|min:0',
            'new_price_variants.*.price_with_iva' => 'nullable|numeric|min:0',
            'cost_variants' => 'nullable|array',
            'cost_variants.*.nombre_proveedor' => 'required_with:cost_variants.*.costo_final|nullable|string|max:150',
            'cost_variants.*.costo_final' => 'required_with:cost_variants.*.nombre_proveedor|nullable|numeric|min:0',
            'cost_variants.*.price_variant_id' => 'nullable|string|max:50',
        ];
    }
}
