<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreEditWorkOrder extends FormRequest
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
    public function rules() {
        return [
            "instructions" => 'required',
            "assigned_date" => 'required',
            'end_date' => 'required|after_or_equal:assigned_date',
            'id_users' => 'required|array|exists:users,id_user'
        ];
    }

    //Mensajes
    public function messages() {
        return [
            'instructions.required' => 'Se debe agregar las instrucciones',
            'assigned_date.required' => 'Se debe agregar la fecha de inicio',
            'end_date.required' => 'Se debe agregar la fecha de finalización',
            'end_date.after_or_equal' => 'La fecha de finalización debe ser mayor o igual a la fecha de comienzo',
            'id_users.required' => 'Se debe asignar la ordén de trabajo al menos a un empleado',
            'id_users.array' => 'Se debe enviar los usuarios en formato de array',
            'id_users.exists' => 'Uno o más empleados seleccionados no tienen un identificador válido'

        ];
    }
    
    //Mostrar
    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode('. ', $errors),
            ], 400)
        );
    }
 
}
