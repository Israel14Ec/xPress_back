<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreJobPriorities extends FormRequest
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
        $jobPriorityId = $this->route('id'); //Obtiene el id de la ruta

        return [
            'name' => 'required|max:50', 
            'description' => 'required|max:300',
            // La regla unique cambia dependiendo de si estás creando o actualizando
            'level' => [
                'required',
                'integer',
                'gt:0',
                Rule::unique('job_priorities', 'level')->ignore($jobPriorityId, 'id_job_priority')
            ]
        ];
    }



    // Mensajes de las reglas
    public function messages()
    {
        return [
            'name.required' => 'El nombre de la prioridad es obligatorio',
            'name.max' => 'El nombre de la prioridad no puede superar los 50 caracteres',
    
            'descripcion.required' => 'El valor unitario es obligatorio.',
            'descripcion.max' => 'La descripción no puede superar los 300 caracteres',

            'level.required' => 'El nivel es obligatorio',
            'level.integer' => 'El nivel debe ser un numero entero',
            'level.gt' => 'El nivel debe ser mayo que 0',
            'level' => 'El nivel debe ser un valor unico'
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
