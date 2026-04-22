<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organisationId = $this->route('id') ?? $this->route('organisation');
        
        return [
            'nom' => 'required|string|max:150',
            'sigle' => ['nullable', 'string', 'max:20', Rule::unique('organisations', 'sigle')->ignore($organisationId)],
            'type' => 'required|integer|in:0,1,2,3,4',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de l\'organisation est requis.',
            'nom.max' => 'Le nom ne doit pas dépasser 150 caractères.',
            'sigle.unique' => 'Ce sigle est déjà utilisé par une autre organisation.',
            'type.required' => 'Le type d\'organisation est requis.',
            'type.in' => 'Le type sélectionné est invalide.',
            'email.email' => 'L\'email doit être une adresse valide.',
        ];
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