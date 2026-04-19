<?php

namespace App\Http\Requests;

class MotdepasseOublieRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // ⚠️ Pas de `exists:users,email` pour éviter de révéler si un email existe (sécurité)
        return ['email' => 'required|email|max:150'];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.email' => 'Veuillez entrer une adresse email valide.',
        ]);
    }

    public function attributes(): array
    {
        return ['email' => 'adresse email'];
    }

    public function prepareForValidation(): void
    {
        parent::prepareForValidation();
        if ($this->has('email')) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }
}