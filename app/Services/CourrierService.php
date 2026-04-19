<?php

namespace App\Services;

use App\Repositories\Interfaces\CourrierInterface;
use App\Models\Courrier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourrierService
{
    public function __construct(
        protected CourrierInterface $repo
    ) {}

    // ========================================================================
    // 📋 LISTE & FORMATAGE
    // ========================================================================

    /**
     * Retourne tous les courriers actifs, formatés et prêts pour l'API/Frontend.
     * @param array $filtres Optionnels : service_id, type, statut
     */
    public function liste(array $filtres = []): array
    {
        $query = $this->repo->query()
            ->where('etat', Courrier::ETAT_ACTIF)
            ->with(['agent', 'service', 'utilisateur', 'organisation']) // Évite les requêtes N+1
            ->latest('date_reception');

        // Filtres optionnels
        !empty($filtres['service_id']) && $query->where('service_id', $filtres['service_id']);
        !empty($filtres['type']) && $query->where('type', $filtres['type']);
        !empty($filtres['statut']) && $query->where('statut', $filtres['statut']);

        return $query->get()->map(fn($c) => $this->formatCourrier($c))->toArray();
    }

    /**
     * Formate un courrier avec ses relations, métadonnées et libellés métier.
     * Structure idéale pour les réponses JSON / ressources API.
     */
    public function formatCourrier(Courrier $courrier): array
    {
        $courrier->loadMissing(['agent', 'service', 'utilisateur', 'organisation']);

        return [
            // 🔖 Identifiants
            'id'        => $courrier->id,
            'reference' => $courrier->reference,
            'numero'    => $courrier->numero,

            // 📊 Classification (code + libellé)
            'type'     => $this->formatEnum($courrier->type, Courrier::getTypesList()),
            'priorite' => $this->formatEnum($courrier->priorite, Courrier::getPrioritesList()),
            'statut'   => $this->formatEnum($courrier->statut, Courrier::getStatutsList()),

            // 📝 Contenu
            'objet'       => $courrier->objet,
            'description' => $courrier->description,
           

            // 📅 Dates
            'dates' => [
                'reception'   => $courrier->date_reception?->format('Y-m-d'),
                'envoi'       => $courrier->date_envoi?->format('Y-m-d'),
                'affectation' => $courrier->date_affectation?->format('Y-m-d H:i'),
                'traitement'  => $courrier->date_traitement?->format('Y-m-d H:i'),
            ],

            // 📎 Fichier scanné
            'fichier' => $courrier->url_fichier ? [
                'url'            => Storage::disk('public')->url($courrier->url_fichier),
                'nom_original'   => $courrier->fichier_nom_original,
                'mime_type'      => $courrier->fichier_mime_type,
                'taille_octets'  => $courrier->fichier_taille,
                'taille_formatee'=> $this->formatTaille($courrier->fichier_taille),
            ] : null,

            // 👥 Acteurs & Entités impliquées
            'acteurs' => [
                'agent'        => $this->formatUser($courrier->agent),
                'service'      => $this->formatRelation($courrier->service, ['id', 'nom']),
                'utilisateur'  => $this->formatUser($courrier->utilisateur),
                'organisation' => $this->formatRelation($courrier->organisation, ['id', 'nom', 'sigle']),
            ],

            // ⏱️ Horodatage
            'etat'       => $courrier->etat === Courrier::ETAT_ACTIF ? 'actif' : 'supprimé',
            'created_at' => $courrier->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $courrier->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    // ========================================================================
    // 📥 Création & Mise à jour
    // ========================================================================

    public function creer(array $data, ?UploadedFile $fichier = null): Courrier
    {
        return DB::transaction(function () use ($data, $fichier) {
            $data['etat'] = Courrier::ETAT_ACTIF;
            if ($fichier) $data = array_merge($data, $this->preparerDonneesFichier($fichier));
            return $this->repo->create($data);
        });
    }

    public function mettreAJour(int $id, array $data): Courrier
    {
        $this->repo->update($id, $data);
        return $this->repo->findOrFail($id);
    }

    // ========================================================================
    // 🗑️ Gestion du cycle de vie (etat)
    // ========================================================================

    public function supprimer(int $id): bool { return $this->repo->supprimer($id); }
    public function restaurer(int $id): bool   { return $this->repo->restaurer($id); }

    // ========================================================================
    // 🔄 Workflow métier
    // ========================================================================

    public function affecter(int $courrierId, int $agentId, int $serviceId): bool
    {
        return $this->repo->affecter($courrierId, $agentId, $serviceId);
    }

    public function marquerTraite(int $courrierId, int $userId): bool
    {
        return $this->repo->marquerTraite($courrierId, $userId);
    }

    // ========================================================================
    // 📎 Gestion des fichiers
    // ========================================================================

    public function attacherFichier(int $courrierId, UploadedFile $fichier): bool
    {
        return $this->repo->attacherFichier($courrierId, $this->preparerDonneesFichier($fichier));
    }

    public function supprimerFichier(int $courrierId): bool
    {
        $courrier = $this->repo->findOrFail($courrierId);
        if ($courrier->url_fichier && Storage::disk('public')->exists($courrier->url_fichier)) {
            Storage::disk('public')->delete($courrier->url_fichier);
        }
        return $this->repo->update($courrierId, [
            'url_fichier' => null, 'fichier_nom_original' => null,
            'fichier_mime_type' => null, 'fichier_taille' => null,
        ]);
    }

    // ========================================================================
    // 🔍 Recherche & Dashboard
    // ========================================================================

    public function rechercher(array $filtres): LengthAwarePaginator { return $this->repo->rechercher($filtres); }

    public function getStats(): array
    {
        return [
            'par_statut'       => $this->repo->countByStatut(),
            'par_periode'      => $this->repo->countByPeriode(),
            'urgents_non_traite' => $this->repo->getUrgentsNonTraites()->count(),
        ];
    }

    // ========================================================================
    // 🛠️ Helpers internes
    // ========================================================================

    protected function preparerDonneesFichier(UploadedFile $fichier): array
    {
        return [
            'url_fichier'          => $fichier->store('courriers', 'public'),
            'fichier_nom_original' => $fichier->getClientOriginalName(),
            'fichier_mime_type'    => $fichier->getMimeType(),
            'fichier_taille'       => $fichier->getSize(),
        ];
    }

    protected function formatTaille(?int $taille): string
    {
        if (!$taille) return '0 o';
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i = 0;
        while ($taille >= 1024 && $i < count($units) - 1) { $taille /= 1024; $i++; }
        return round($taille, 2) . ' ' . $units[$i];
    }

    protected function formatEnum(int|string $code, array $list): array
    {
        return ['code' => $code, 'libelle' => $list[$code] ?? 'Inconnu'];
    }

    protected function formatRelation(?object $relation, array $fields): ?array
    {
        if (!$relation) return null;
        return array_intersect_key($relation->toArray(), array_flip($fields));
    }

    // ⚠️ Adapter selon si agent_id pointe vers User ou Agent
    protected function formatUser(?object $user): ?array
    {
        if (!$user) return null;
        return [
            'id'    => $user->id,
            'nom'   => $user->name ?? ($user->nom . ' ' . $user->prenom),
            'email' => $user->email,
        ];
    }
}