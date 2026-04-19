<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * UserFormRequest
 * Validation unifiée pour la création et la modification des comptes utilisateurs.
 * Gère dynamiquement les règles (required vs sometimes), l'unicité de l'email
 * et la sécurité des mots de passe.
 */
class UserFormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        // 🔒 Remplace par `return auth()->user()->can('manage_users');` si tu utilises des Gates
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $required = $isUpdate ? 'sometimes|' : 'required|';

        return [
            'name'      => $required . 'string|max:150',
            
            // 📧 Email : unique en BDD, mais ignore l'utilisateur courant lors d'un update
            'email'     => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            
            // 🔑 Mot de passe : nullable en update (permet de modifier sans changer le MDP)
            'password'  => 'nullable|string|min:8|confirmed',
            
            'telephone' => 'nullable|string|max:20',
            
            // 🛡️ Rôle : restreint aux valeurs métier autorisées
            'role'      => $required . 'string|in:admin,chef_service,secretaire,agent',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.unique'       => 'Cette adresse email est déjà associée à un compte.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins :min caractères.',
            'role.in'            => 'Le rôle sélectionné est invalide.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'name'      => 'nom complet',
            'email'     => 'adresse email',
            'password'  => 'mot de passe',
            'telephone' => 'téléphone',
            'role'      => 'rôle utilisateur',
        ];
    }

    /**
     * Nettoyage spécifique : force l'email en minuscules et supprime les espaces.
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