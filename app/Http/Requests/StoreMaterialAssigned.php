<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMaterialAssigned extends FormRequest
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

    //Reglas
    public function rules()
    {
        return [
            'materials' => 'required|array', 
            'materials.*.id_material' => 'required|exists:materials,id_material', // Validar cada 'id_material' dentro del array 'materials'
            'materials.*.amount' => 'required|numeric|min:1', // Validar la 'amount' para cada material
            'id_work_order' => 'required|exists:work_orders,id_work_order',
        ];
    }

    //Mensajes
    public function messages() {
        return [
            'materials.required' => 'Debe proporcionar al menos un material y su cantidad.',
            'materials.*.id_material.required' => 'Debe proporcionar un ID de material válido.',
            'materials.*.id_material.exists' => 'El material seleccionado no es válido.',
            'materials.*.amount.required' => 'Debe proporcionar una cantidad para cada material.',
            'materials.*.amount.numeric' => 'La cantidad debe ser un número.',
            'materials.*.amount.min' => 'La cantidad debe ser al menos 1.',
            'id_work_order.required' => 'No se ha proporcionado una orden de trabajo.',
            'id_work_order.exists' => 'La orden de trabajo proporcionada no existe.',
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
