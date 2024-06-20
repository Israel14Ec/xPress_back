<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreJob extends FormRequest
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
            'name_job' => 'required',
            'description' => 'required',
            'num_caf' => 'max:50',
            'start_date' => 'required',
            'id_job_status' => 'required|exists:job_statuses,id_job_status',
            'id_job_priority' => 'required|exists:job_priorities,id_job_priority',
            'id_client' => 'required|exists:clients,id_client',
            'id_communication_type' => 'required|exists:communication_types,id_communication_type',
            'id_establishment' => 'required|exists:establishments,id_establishment',
            'id_type_maintenance' => 'required|exists:type_maintenances,id_type_maintenance',
        ];
    }

    // Mensajes de las reglas
    public function messages() {
        return [
            'name_job.required' => 'El nombre del trabajo es obligatorio.',
            'description.required' => 'Agregar una descripción es obligatorio.',
            'description.max' => 'La descripción debe tener como máximo 300 caracteres, superó el límite',
            'num_caf.max' => 'El número de CAF no debe superar los 50 caracteres.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'id_job_status.required' => 'El estado del trabajo es obligatorio.',
            'id_job_status.exists' => 'El estado del trabajo seleccionado no es válido.',
            'id_job_priority.required' => 'La prioridad del trabajo es obligatoria.',
            'id_job_priority.exists' => 'La prioridad del trabajo seleccionada no es válida.',
            'id_client.required' => 'El cliente es obligatorio.',
            'id_client.exists' => 'El cliente seleccionado no es válido.',
            'id_communication_type.required' => 'El tipo de comunicación es obligatorio.',
            'id_communication_type.exists' => 'El tipo de comunicación seleccionado no es válido.',
            'id_establishment.required' => 'El establecimiento es obligatorio.',
            'id_establishment.exists' => 'El establecimiento seleccionado no es válido.',
            'id_type_maintenance.required' => 'El tipo de mantenimiento es obligatorio.',
            'id_type_maintenance.exists' => 'El tipo de mantenimiento seleccionado no es válido.',
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
