<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacturaRequest extends FormRequest
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
        $rules = [
            //Emisor data
            "tipo_documento" => "required|string",
            "numero_documento" => "required|string",
            "nombre_receptor" => "required|string",
            "telefono_receptor" => "required|string",
            "correo_receptor" => "required|string",
            "direccion_receptor" => "required|string",
            "departamento" => "required|string",
            "municipio" => "required|string",
            "complemento" => "required|string",
            "telefono" => "required|string",
            "correo" => "required|string|email",

            //Dte data
            "nit" => "required|string",
                
        ];

        if($this->input("omitir_datos_receptor")){
            $rules["nombre_receptor"] = "nullable";
            $rules["telefono_receptor"] = "nullable";
            $rules["correo_receptor"] = "nullable";
            $rules["direccion_receptor"] = "nullable";
            $rules["tipo_documento"] = "nullable";
            $rules["numero_documento"] = "nullable";
            $rules["departamento"] = "nullable";
            $rules["municipio"] = "nullable";
            $rules["complemento"] = "nullable";
            $rules["telefono"] = "nullable";
            $rules["correo"] = "nullable";
        }        


        return $rules;
    }
}
