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
        ];
    }
}
