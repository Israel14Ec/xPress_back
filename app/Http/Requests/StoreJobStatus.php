<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreJobStatus extends FormRequest
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
        $id = $this->route('id'); //Obtiene el id de la ruta
        return [
            //Datos del request - Rules
            'name' => 'required|max:50',
            'description' => 'required|max:300', //Validacion para maximo dos decimales y mayor que 0
            'color' => 'required|max:7',
            'step' => [
                'nullable',
                'integer',
                Rule::unique('job_statuses', 'step')->ignore($id, 'id_job_status')
            ] 
        ];
        
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.max' => 'El nombre tiene un máximo de 50 caracteres',
            'description.required' => 'El campo descripción es obligatorio',
            'description.max' => 'La descripción tiene un máximo de 300 caracteres',
            'color.required' => 'El campo color es obligatorio',
            'color.max' => 'El nombre tiene un máximo de 7 caracteres ',
            'step.integer' => 'El valor debe ser un numero',
            'step.unique' => 'La secuencia de paso debe ser un numero unico'
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
