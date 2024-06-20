<?php

namespace App\Http\Requests;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompleteWorkOrder extends FormRequest
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

    //Validaciones
    public function rules()
    {
        return [
       
            //WorkOrders
            'id_job' => 'required|exists:jobs,id_job',
            'instructions' => 'required',
            'end_date' => 'nullable|after_or_equal:assigned_date',
            
            //AssignedWorker
            'id_user' => 'required|array', 
            'id_user.*' => 'exists:users,id_user', // cada ID en el array exista en la tabla de usuarios
        
            //MaterialAssigned

            'materials.*.id_material' => 'exists:materials,id_material', // Validar cada 'id_material' dentro del array 'materials'
            'materials.*.amount' => 'numeric|min:1', // Validar la 'amount' para cada material

            //EquipmentAssigned
            'id_equipment_assigned' => 'array',
            'id_equipment_assigned.*' => 'exists:construction_equipments,id_construction_equipment',
            
        ];
    }

    public function messages() {
        return [

            //WorkOrders
            'instructions.required' => 'Es obligatorio añadir una instrucción',
            'id_job.required'=> 'Seleccionar un trabajo es obligatorio',
            'id_job.exists' => 'El trabajo seleccionado no existe',
            'end_date.after_or_equal' => 'La fecha de finalización, debe ser posterior a a la fecha de inicio',

            //AssignedWorker
            'id_user.required' => 'Seleccione al menos a un usuario',
            'id_user.exists' => 'El trabajador seleccionado no es valido',

            //MaterialAssigned
            'materials.*.id_material.exists' => 'El material seleccionado no es válido.',
            'materials.*.amount.numeric' => 'La cantidad debe ser un número.',
            'materials.*.amount.min' => 'La cantidad debe ser al menos 1.',

            //EquipmentAssigned
            'id_equipment_assigned.array' => 'La selección de equipos debe ser un arreglo.',
            'id_equipment_assigned.*.exists' => 'El equipo seleccionado no existe o no es válido.',

            
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = ['msg' => $error];
        }
    
        throw new HttpResponseException(
            response()->json($errorMessages, 400)
        );
    }

}
