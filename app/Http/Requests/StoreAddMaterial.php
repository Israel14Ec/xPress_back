<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreAddMaterial extends FormRequest
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

    public function rules()
    {
        return [
            //Datos del request - Rules
            'stock' => 'required|numeric|gt:0',
            'description' => 'required',
            'id_material' => 'required|exists:materials,id_material'
        ];
    }

    public function messages()
    {
        return [
            'stock.required' => 'El campo stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un valor numérico.',
            'stock.gt' => 'El stock debe ser mayor que 0',
            'description.required' => 'La descripción es obligatoria.',
            'id_material.required' => 'No se selecciono un material',
            'id_material.exists' => 'El material seleccionado no existe.'
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode(' ', $errors),
            ], 400)
        );
    }
}
