<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreTypeEquipment extends FormRequest
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
            'name_type_equipment' => 'required|max:50',
            'description' => 'required|max:300',
        ];
    }
    

    public function messages()
    {
        return [
            'required' => 'Llene todos los campos',
            'name_type_equipment.max' => 'Para el nombre se admiten solo 50 caracteres como máximo',
            'description.max' => 'Para la descripción solo se admiten 300 caracteres',        
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
