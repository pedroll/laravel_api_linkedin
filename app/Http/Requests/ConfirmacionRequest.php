<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmacionRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required',
            'actividad_id' => 'required',
        ];
        $filterAndSortRules = [
            'filters' => ['nullable', 'json'],
            'sorts' => ['nullable', 'json'],
        ];

        return array_merge($rules, $filterAndSortRules);
    }

    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
    {
        $this->merge(array_map('trim', $this->all()));
    }
    // messages
    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'confirmacion.required' => 'The confirmation field is required.',
        ];
    }
}
