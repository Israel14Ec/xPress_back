<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserEdit extends FormRequest
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
            'name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'phone_number' => 'max:10'
        ];
    }

    public function messages () {
        return [
            'name.required' => 'Ingrese el nombre',
            'name.max' => 'Máximo 50 caracteres para el nombre',
            'last_name.required' => 'Ingrese el apellido',
            'last_name.max' => 'Máximo 50 caracteres para el apellido',
            'phone_number.max' => 'El número de teléfono debe tener 10 digitos'
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'msg' => implode(' ,', $errors),
            ], 400)
        );
    }
}
