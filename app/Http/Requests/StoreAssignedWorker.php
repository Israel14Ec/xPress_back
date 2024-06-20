<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreAssignedWorker extends FormRequest
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
            'id_user' => 'required|array', 
            'id_user.*' => 'exists:users,id_user', // cada ID en el array exista en la tabla de usuarios
            'id_work_order' => 'required|exists:work_orders,id_work_order',
        ];
    }

    //Mensajes
    public function messages() {
        return [
            'id_user.required' => 'Seleccione al menos a un usuario',
            'id_user.exists' => 'El trabajador seleccionado no es valido',
            'id_work_order.required' => 'La orden de trabajo no es valida',
            'id_work_order.exists' => 'La orden de trabajo no existe'
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
