<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserdataRequest extends FormRequest
{
    /**
     * @var int|mixed
     */
    public mixed $user_id;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            'name' => ['required', 'string'],
            'edad' => ['required', 'numeric'],
            'acercade' => ['required', 'string'],
            'genero' => ['required', 'in:Masculino,Femenino,Otro'],
            'foto' => ['nullable', 'url'],
            'filterBy' => ['nullable', 'string'],
            'filter' => ['nullable', 'string'],
            'orderBy' => ['nullable', 'string'],
            'order' => ['nullable', 'in:asc,desc'],
            'page' => ['nullable', 'numeric', 'min:1'],
            'perPage' => ['nullable', 'numeric', 'min:1'],
        ];
    }
    // check CSRF
    public function authorize(): bool
    {
        // Check CSRF token
        if ($this->wantsJson() && !$this->hasValidSignature()) {
            return false;
        }

        // Check if the user is authenticated
        if (auth()->check()) {
            // Add specific authorization logic here based on user roles or permissions
            // For example, check if the user has the necessary role to perform the action
            // if (auth()->user()->isAdmin()) {
            //     return true; // User is authorized
            // }
        }

        //return false; // Default to false if user is not authenticated or does not have the required role
        return true;
    }

}
