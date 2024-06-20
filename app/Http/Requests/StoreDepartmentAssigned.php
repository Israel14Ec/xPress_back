<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreDepartmentAssigned extends FormRequest
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
            'id_departments' => 'required|array', // Asegura que sea un array
            'id_departments.*' => 'exists:departments,id_department', // Cada elemento debe existir en la tabla departments
            'id_job' => 'required|exists:jobs,id_job'
        ];
    }
    

    //Mensajes
    public function messages() {
        return [
            'id_departments.required' => 'Ingresar al menos un departamento es obligatorio',
            'id_departments.array' => 'El campo departamentos debe ser un array',
            'id_departments.*.exists' => 'Uno o más departamentos ingresados no existen',
            'id_job.required' => 'Ingresar un trabajo es obligatorio',
            'id_job.exists' => 'El trabajo ingresado no existe'
        ];
    }
    

    //Mostrar
    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode(' ', $errors),
            ], 400)
        );
    }
}
