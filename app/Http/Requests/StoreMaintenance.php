<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class StoreMaintenance extends FormRequest
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
            //Datos del request - Rules
            'name' => 'required|max:50',
            'description' => 'required|max:300'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.max' => 'El campo nombre admite hasta 50 caracteres.',

            'description.required' => 'El campo descripción es obligatorio.',
            'description.max' => 'El campo descripción admite hasta 300 caracteres.'
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
