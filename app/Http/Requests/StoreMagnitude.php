<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreMagnitude extends FormRequest
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

   public function rules() {
        return [
            'name' => 'required|max:50',
            'symbol' => 'required|max:50'
        ];
   }

   public function messages()
   {
    
       return [
           'name.required' => 'El campo nombre es obligatorio.',
           'name.max' => 'El nombre tiene un máximo de 50 caracteres',

           'symbol.required' => 'El campo de simbolo es obligatorio',
           'symbol.max' => 'El campo de símbolo admite un máximo de 50 caracteres'
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
