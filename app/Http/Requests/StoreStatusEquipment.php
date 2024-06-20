<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreStatusEquipment extends FormRequest
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
                'name_status_equipment' => 'required|max:50',
                'description' => 'required|max:300',
                'color' => 'required|max:7',
            ];
        }
    }

    // Mensajes de las reglas
    public function messages()
    {
        return [
            'name_status_equipment.required' => 'El nombre del estado del equipo es obligatorio.',
            'name_status_equipment.max' => 'El nombre del estado del equipo no debe superar los 50 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no debe superar los 300 caracteres.',
            'color.required' => 'El color es obligatorio.',
            'color.max' => 'El color no debe superar los 7 caracteres.',
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
