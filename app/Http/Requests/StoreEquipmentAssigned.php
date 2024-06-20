<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreEquipmentAssigned extends FormRequest
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
            'id_equipment_assigned' => 'required|array',
            'id_equipment_assigned.*' => 'exists:construction_equipments,id_construction_equipment',
            'id_work_order' => 'required|exists:work_orders,id_work_order',
        ];
    }


    // Mensajes de validación
    public function messages() {
        return [
            'id_equipment_assigned.required' => 'Es necesario seleccionar al menos un equipo.',
            'id_equipment_assigned.array' => 'La selección de equipos debe ser un arreglo.',
            'id_equipment_assigned.*.exists' => 'El equipo seleccionado no existe o no es válido.',
            'id_work_order.required' => 'Es necesario proporcionar una orden de trabajo.',
            'id_work_order.exists' => 'La orden de trabajo especificada no existe o no es válida.',
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
