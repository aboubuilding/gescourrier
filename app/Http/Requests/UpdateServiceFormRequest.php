<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateServiceFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $serviceId = $this->route('id'); // ou 'service' selon ton route binding

        return [
            // 🏢 Nom du service (obligatoire)
            'nom' => $this->conditionalRule('required|string|max:150'),

            // 📝 Description
            'description' => $this->conditionalRule('nullable|string|max:1000'),

            // 📍 Localisation
            'localisation' => $this->conditionalRule('nullable|string|max:255'),

            // 📞 Téléphone
            'telephone' => $this->conditionalRule([
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\s\-()]+$/'
            ]),

            // 📧 Email
            'email' => $this->conditionalRule('nullable|email|max:150'),

            // 🏢 Organisation
            'organisation_id' => $this->conditionalRule([
                'nullable',
                'integer',
                Rule::exists('organisations', 'id')
            ]),
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [

            'nom.required' => 'Le nom du service est obligatoire.',
            'nom.max' => 'Le nom du service ne doit pas dépasser 150 caractères.',

            'email.email' => 'L’adresse email n’est pas valide.',

            'telephone.regex' => 'Le numéro de téléphone contient des caractères invalides.',

            'organisation_id.exists' => 'L’organisation sélectionnée est invalide.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom du service',
            'description' => 'description',
            'localisation' => 'localisation',
            'telephone' => 'téléphone',
            'email' => 'email',
            'organisation_id' => 'organisation',
        ];
    }
}