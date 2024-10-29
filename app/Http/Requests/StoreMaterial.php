<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMaterial extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name_material' => 'required|max:50',
            'unit_value' => 'numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/', //Validacion para maximo dos decimales y mayor que 0
            'stock' => 'numeric|gt:0',
            'total_value' => 'numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/'

        ];
    }

    public function messages()
    {
        return [
            'name_material.required' => 'Ingrese el nombre del material',
            'name_material.max' => 'El nombre del material debe ser de máximo 50 caracteres',
            'unit_value.regex' => 'Solo se admiten hasta dos decimales',
            'unit_value.numeric' => 'Solo ingrese números para el valor unitario',
            'unit_value.gt' => 'El valor unitario debe ser mayor que 0',
            'stock.numeric' => 'El stock debe ser un número',
            'stock.gt' => 'El stock debe ser mayor que 0',
            'total_value.regex' => 'Solo se admiten hasta dos decimales para el valor total',
            'total_value.numeric' => 'Solo ingrese números para el valor total',
            'total_value.gt' => 'El valor total debe ser mayor que 0'
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode(' ,', $errors),
            ], 400)
        );
    }
    
}
