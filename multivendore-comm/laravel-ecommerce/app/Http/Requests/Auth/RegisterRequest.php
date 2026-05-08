<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|unique:users,phone',
            'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'locale'     => 'nullable|string|max:5',
            'timezone'   => 'nullable|timezone',
        ];
    }
}
