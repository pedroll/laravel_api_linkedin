<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles validation for actividad related requests.
 */
class ActividadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authenticated, otherwise false.
     */
    public function authorize()
    {
        // Example logic to determine if the user is authorized
        // Can be customized based on your application's auth requirements
        //       return auth()->check() && auth()->user()->hasRole('ActivityManager');
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Defines validation rules for creating or updating an actividad.
     * Includes dynamic rules for the 'foto' field based on the HTTP method.
     * Also includes rules for optional filters and sorts in JSON format.
     *
     * @return array The validation rules.
     */
    public function rules()
    {
        $rules = [
            'nombre' => ['required','unique:actividades', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'foto' => ['nullable','url']
        ];

        if ($this->isMethod('post')) {
            // Rules specific to creating a new actividad
            //$rules['foto'] = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Rules specific to updating an existing actividad
            // Making 'foto' optional on update, but still validating if present
           // $rules['foto'] = ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];
        }

        $filterAndSortRules = [
            'filters' => ['nullable', 'json'],
            'sorts' => ['nullable', 'json'],
        ];

        return array_merge($rules, $filterAndSortRules);
    }

    /**
     * Get custom messages for validator errors.
     *
     * Provides custom error messages for validation failures.
     *
     * @return array Custom error messages.
     */
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la actividad es obligatorio.',
            'descripcion.required' => 'La descripción de la actividad es obligatoria.',
            'fecha.required' => 'La fecha de la actividad es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'foto.required' => 'La foto de la actividad es obligatoria para crear una nueva actividad.',
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser un archivo tipo: jpg, jpeg, png.',
            'foto.max' => 'La foto no debe exceder de 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Trims whitespace from all input data before validation.
     */
    protected function prepareForValidation()
    {
        $this->merge(array_map('trim', $this->all()));
    }
}
