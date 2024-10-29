<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreEquipment extends FormRequest
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
        {
            return [
                //Datos del request - Rules
                'name_equipment' => 'required|max:50',
                'description' => 'max:300',
                'unit_value' => 'required|gt:0|regex:/^\d+(\.\d{1,2})?$/', //Validacion para maximo dos decimales y mayor que 0
                'id_status_equipment|exists:status_equipments,id_status_equipment',
                'id_type_equipment|exists:type_equipments,id_type_equipment'
            ];
        }
    }

     // Mensajes de las reglas
     public function messages()
     {
         return [
             'name_equipment.required' => 'El nombre del equipo es obligatorio.',
             'name_equipment.max' => 'El nombre del equipo no debe superar los 50 caracteres.',

             'description' => 'La descripción debe tener máximo 300 caracteres',

             'unit_value.required' => 'El valor unitario es obligatorio.',
             'unit_value.gt' => 'El valor unitario debe ser mayor que 0.',
             'unit_value.regex' => 'El valor unitario debe ser un número con hasta dos decimales.',
     
             'id_status_equipment.exists' => 'El estado del equipo seleccionado no es válido.',
             'id_type_equipment.exists' => 'El tipo de equipo seleccionado no es válido.',
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
