<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreWorkOrders extends FormRequest
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
        return [
            'instructions' => 'required',
            'id_job' => 'required|exists:jobs,id_job',
            'end_date' => 'nullable|date|after_or_equal:assigned_date',
            
        ];
    }

    public function messages() {
        return [
            'instructions.required' => 'Es obligatorio añadir una instrucción',
            'id_job.required'=> 'Seleccionar un trabajo es obligatorio',
            'id_job.exists' => 'El trabajo seleccionado no existe',
            'end_date.after_or_equal' => 'La fecha de finalización, debe ser posterior a a la fecha de inicio',
            
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
