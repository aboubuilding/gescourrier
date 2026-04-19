<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ServiceFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nom'             => $this->conditionalRule('string|max:150'),
            'organisation_id' => 'nullable|exists:organisations,id',
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'organisation_id.exists' => 'L\'organisation de rattachement sélectionnée est invalide.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'nom' => 'nom du service',
            'organisation_id' => 'organisation',
        ];
    }
}