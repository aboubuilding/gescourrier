<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; // Ou `return auth()->user()->can('create_users');` si réservé aux admins
    }

    public function rules(): array
    {
        return [
            'name'     => $this->conditionalRule('string|max:150'),
            'email'    => $this->conditionalRule('email|max:150|unique:users,email'),
            'password' => $this->conditionalRule([
                'string',
                'min:8',
                'confirmed',
                Password::defaults() // Sécurisé par défaut (Laravel 10+)
            ]),
            'role'     => 'nullable|string|in:agent,secretaire,chef_service',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.unique'        => 'Cette adresse email est déjà associée à un compte.',
            'password.confirmed'  => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'role.in'             => 'Le rôle sélectionné est invalide.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nom complet',
            'email'    => 'adresse email',
            'password' => 'mot de passe',
            'role'     => 'rôle utilisateur',
        ];
    }

    public function prepareForValidation(): void
    {
        parent::prepareForValidation();
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }
}