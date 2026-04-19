<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AgentFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        // Ignore l'email actuel lors de l'update pour éviter les faux positifs
        $ignoreUserId = $this->input('user_id') ?? $this->route('agent')?->user_id;

        return [
            'nom'        => $this->conditionalRule('string|max:100'),
            'prenom'     => $this->conditionalRule('string|max:100'),
            'email'      => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($ignoreUserId)],
            'telephone'  => 'nullable|string|max:20',
            'fonction'   => 'nullable|string|max:100',
            'service_id' => 'nullable|exists:services,id',
            'user_id'    => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'email.unique' => 'Cette adresse email est déjà associée à un compte utilisateur.',
            'service_id.exists' => 'Le service de rattachement sélectionné est invalide.',
            'user_id.exists' => 'Le compte utilisateur sélectionné n\'existe pas.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'adresse email',
            'telephone' => 'téléphone',
            'fonction' => 'fonction / poste',
            'service_id' => 'service',
            'user_id' => 'compte utilisateur',
        ];
    }
}