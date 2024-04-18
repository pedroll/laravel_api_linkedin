<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class userdataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required'],
            'foto' => ['nullable'],
            'edad' => ['required'],
            'acercade' => ['required'],
            'genero' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
