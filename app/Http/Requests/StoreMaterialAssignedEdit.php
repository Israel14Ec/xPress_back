<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMaterialAssignedEdit extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "amount" => "required|numeric|gt:0",
  
        ];
    }

    //Mensajes
    public function messages() {
        return [
            "amount.required" => "Poner la cantidad solicitada de material es obligatorio",
            "amount.numeric" => "La cantidad solicitada de material debe ser un valor numérico",
            "amount.gt" => "La cantidad solicitada de material debe ser mayor que 0"
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
