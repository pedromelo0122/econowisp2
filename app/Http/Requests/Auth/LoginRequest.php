<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * Copia email/username/phone -> login antes de validar
     * para que no dependa del nombre exacto del input en la vista.
     */
    protected function prepareForValidation(): void
    {
        $login = $this->input('login');

        if ($login === null || $login === '') {
            $login = $this->input('email');
            if ($login === null || $login === '') $login = $this->input('username');
            if ($login === null || $login === '') $login = $this->input('phone');
        }

        if (is_string($login)) { $login = trim($login); }

        if ($login !== null && $login !== '') {
            $this->merge(['login' => $login]);
        }
    }

    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'El campo usuario/email/teléfono es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    public function attributes(): array
    {
        return [
            'login'    => 'usuario/email/teléfono',
            'password' => 'contraseña',
        ];
    }

    /**
     * Estandariza credenciales para Auth::attempt().
     * Acepta email, teléfono (E.164) o username.
     */
    public function credentials(): array
    {
        $login    = (string) $this->input('login', '');
        $password = (string) $this->input('password', '');

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return ['email' => $login, 'password' => $password];
        }

        if (preg_match('/^\+?[0-9]{6,}$/', $login)) {
            return ['phone' => $login, 'password' => $password];
        }

        return ['username' => $login, 'password' => $password];
    }
}
