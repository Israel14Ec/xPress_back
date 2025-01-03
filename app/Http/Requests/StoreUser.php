<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUser extends FormRequest
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
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8',
            'phone_number' => 'unique:users|max:10'
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'Ingrese el nombre',
            'name.max' => 'Máximo 50 caracteres para el nombre',
            'last_name.required' => 'Ingrese el apellido',
            'last_name.max' => 'Máximo 50 caracteres para el apellido',
            'email.required' => 'Ingrese su email',
            'email.unique' => 'El email ya fue registrado',
            'email.email' => 'Proporcione un email válido',
            'password.required' => 'Ingrese su contraseña',
            'password.min' => 'La contraseña debe contener al menos :min caracteres',
            'phone_number.required' => 'Ya se registro ese número de teléfono',
            'phone_number.max' => 'El número de teléfono debe tener 10 digitos'
        ];
    }

    protected function failedValidation(Validator $validator) {
        $errors = $validator->errors()->all();
    
        throw new HttpResponseException(
            response()->json([
                'error' => implode(' ,', $errors),
            ], 400)
        );
    }
}
