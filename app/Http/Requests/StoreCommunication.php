<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreCommunication extends FormRequest
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

    public function rules () {
        {
            return [
                'name_communication' => 'required|max:50',
                'description' => 'required|max:300',
            ];
        }
    }

    // Mensajes de las reglas
    public function messages() {
        return [
            'name_communication.required' => 'El nombre del medio de comunicación es obligatorio.',
            'name_communication.max' => 'El nombre del medio de comunicación no debe superar los 50 caracteres.',
         
            'description.required' => 'Agregar una descripción es obligatorio.',
            'description.max' => 'La descripcion debe tener como máximo 300 caracteres, supero el limite',
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
