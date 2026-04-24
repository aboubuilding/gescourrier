<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CourrierFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:0,1,2',
            'priorite' => 'required|in:0,1,2',
            'reference' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'organisation_id' => 'required|exists:organisations,id',
            'objet' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'date_reception' => 'nullable|date',
            'date_envoi' => 'nullable|date',
            'fichier' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de courrier est requis.',
            'type.in' => 'Le type sélectionné est invalide.',
            'priorite.required' => 'La priorité est requise.',
            'organisation_id.required' => 'L\'expéditeur est requis.',
            'organisation_id.exists' => 'L\'organisation sélectionnée n\'existe pas.',
            'objet.required' => 'L\'objet du courrier est requis.', 
            'fichier.max' => 'Le fichier ne doit pas dépasser 5 Mo.',
            'fichier.mimes' => 'Le fichier doit être de type PDF, JPG, JPEG ou PNG.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'success' => false,
            'message' => 'Erreurs de validation',
            'errors' => $validator->errors()
        ], 422);

        throw new HttpResponseException($response);
    }
}