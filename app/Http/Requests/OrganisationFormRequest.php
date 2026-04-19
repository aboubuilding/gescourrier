<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class OrganisationFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nom'       => $this->conditionalRule('string|max:150'),
            'sigle'     => ['nullable', 'string', 'max:20', Rule::unique('organisations', 'sigle')->ignore($this->route('id'))],
            'type'      => $this->conditionalRule('integer|in:0,1,2,3,4'),
            'adresse'   => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'sigle.unique' => 'Ce sigle est déjà utilisé par une autre organisation.',
            'type.in' => 'Le type doit être : 0 (Externe), 1 (Interne), 2 (Gouvernementale), 3 (Privée) ou 4 (ONG).',
        ]);
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom de l\'organisation',
            'sigle' => 'sigle / acronyme',
            'type' => 'type d\'organisation',
            'adresse' => 'adresse postale',
            'telephone' => 'téléphone',
            'email' => 'adresse email',
        ];
    }
}