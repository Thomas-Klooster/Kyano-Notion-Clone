<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'remember' => ['nullable', 'boolean'],
            'role' => ['required', Rule::in(['admin', 'klant', 'customer'])],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Dit email is al in gebruik.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('role') === 'customer') {
            $this->merge(['role' => 'klant']);
        }

        if (!$this->filled('password')) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }
}
