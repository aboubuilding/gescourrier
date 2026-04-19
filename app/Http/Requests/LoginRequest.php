<?php

namespace App\Http\Requests;

/**
 * LoginRequest
 * Validation des identifiants de connexion.
 * Étend BaseFormRequest pour conserver le nettoyage automatique des données
 * et les messages d'erreur génériques du projet.
 */
class LoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; // Route publique
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'max:100'], // min:6 pour sécurité basique
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit contenir au moins :min caractères.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'email'    => 'adresse email',
            'password' => 'mot de passe',
        ];
    }

    /**
     * Nettoyage spécifique à la connexion :
     * Met l'email en minuscules pour éviter les problèmes de casse en BDD.
     */
    public function prepareForValidation(): void
    {
        parent::prepareForValidation(); // Garde le nettoyage générique de BaseFormRequest
        
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->input('email')))
            ]);
        }
    }
}