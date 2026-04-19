<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * BaseFormRequest
 * Classe abstraite de base pour tous les FormRequests du projet.
 * Factorise le nettoyage des données, les messages d'erreur génériques
 * et les helpers de validation conditionnelle.
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     * Par défaut : autorisé. Surchargez cette méthode pour ajouter des checks de permissions.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation. À implémenter obligatoirement dans les classes filles.
     */
    abstract public function rules(): array;

    /**
     * Messages d'erreur personnalisés par défaut.
     * Les classes filles peuvent surcharger ou fusionner avec `parent::messages()`.
     */
    public function messages(): array
    {
        return [
            '*.required' => 'Ce champ est obligatoire.',
            '*.string'   => 'Ce champ doit être une chaîne de caractères valide.',
            '*.integer'  => 'Ce champ doit être un nombre entier.',
            '*.email'    => 'Ce champ doit être une adresse email valide.',
            '*.date'     => 'Ce champ doit être une date valide.',
            '*.exists'   => 'La valeur sélectionnée est invalide ou n\'existe plus.',
            '*.file'     => 'Ce champ doit contenir un fichier valide.',
            '*.mimes'    => 'Le fichier doit être au format :values.',
            '*.max'      => 'Ce champ ne doit pas dépasser :max caractères.',
            '*.max.file' => 'Le fichier ne doit pas dépasser :max kilooctets.',
        ];
    }

    /**
     * Attributs lisibles pour les messages d'erreur.
     * À compléter dans les classes filles pour un affichage propre.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Nettoyage et préparation des données avant validation.
     * Supprime les espaces, convertit les chaînes vides en `null` (idéal pour les champs nullable).
     */
    public function prepareForValidation(): void
    {
        $this->merge(
            collect($this->all())->map(fn ($value) => is_string($value) ? (trim($value) === '' ? null : trim($value)) : $value)->all()
        );
    }

    /**
     * Helper : Retourne une règle `required|...` en création, `sometimes|...` en modification.
     *
     * @param string $rules Règles de base (ex: 'integer|in:0,1,2')
     * @param bool $requiredOnStore Si true, 'required' en POST, sinon 'sometimes'
     * @return string Règle complète prête à l'emploi
     */
    protected function conditionalRule(string $rules, bool $requiredOnStore = true): string
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $prefix = ($requiredOnStore && !$isUpdate) ? 'required|' : 'sometimes|';
        return $prefix . ltrim($rules, '|');
    }
}