<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActividadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required'],
            'foto' => ['nullable'],
            'edad' => ['required', 'integer'],
            'acercade' => ['required'],
            'genero' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
