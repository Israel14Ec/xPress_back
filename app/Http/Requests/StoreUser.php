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
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8',
            'phone_number' => 'unique:users'
        ];
    }


    public function messages()
    {
        return [
            'email.required' => 'Ingrese su email',
            'email.unique' => 'El email ya fue registrado',
            'email.email' => 'Proporcione un email válido',
            'password.required' => 'Ingrese su contraseña',
            'password.min' => 'La contraseña debe contener al menos :min caracteres',
            'phone_number' => 'Ya se registro ese número de teléfono'
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
