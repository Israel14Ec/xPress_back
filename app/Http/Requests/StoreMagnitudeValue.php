<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMagnitudeValue extends FormRequest
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

    public function rules(){
        return [
            'value' => 'required',
            'id_magnitude' => 'required|exists:magnitudes,id_magnitude'
        ];
    }

    public function messages() {
       return [
           'value.required' => 'El campo del valor es obligatorio.',
           'id_magnitude.required' => 'Es necesario seleccionar una magnitud',
           'id_magnitude.exists' => 'La magnitud seleccionada no existe'
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
