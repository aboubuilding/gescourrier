<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CourrierFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'reference'       => 'nullable|string|max:100',
            'numero'          => 'nullable|string|max:100',
            
            // ✅ Helper utilisé : required en store, sometimes en update
            'type'            => $this->conditionalRule('integer|in:0,1,2'),
            'priorite'        => $this->conditionalRule('integer|in:0,1,2'),
            'objet'           => $this->conditionalRule('string|max:255'),
            'date_reception'  => $this->conditionalRule('date'),
            
            'description'     => 'nullable|string',
            'date_envoi'      => 'nullable|date|after_or_equal:date_reception',
            'service_id'      => 'nullable|exists:services,id',
            'agent_id'        => 'nullable|exists:users,id',
            'organisation_id' => 'nullable|exists:organisations,id',
            
            // Fichier non obligatoire dans les 2 cas
            'fichier'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        // Fusion avec les messages par défaut de BaseFormRequest
        return array_merge(parent::messages(), [
            'type.in'                     => 'Le type doit être : Entrant (0), Sortant (1) ou Interne (2).',
            'priorite.in'                 => 'La priorité doit être : Normale (0), Urgente (1) ou Très urgente (2).',
            'date_envoi.after_or_equal'   => 'La date d\'envoi doit être supérieure ou égale à la date de réception.',
        ]);
    }

    public function attributes(): array
    {
        return [
            'reference'       => 'référence',
            'numero'          => 'numéro',
            'type'            => 'type de courrier',
            'priorite'        => 'niveau de priorité',
            'objet'           => 'objet du courrier',
            'date_reception'  => 'date de réception',
            'date_envoi'      => 'date d\'envoi',
            'service_id'      => 'service concerné',
            'agent_id'        => 'agent affecté',
            'organisation_id' => 'organisation expéditrice',
            'fichier'         => 'fichier scanné',
        ];
    }
}