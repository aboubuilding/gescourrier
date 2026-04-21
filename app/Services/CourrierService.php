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
        ->with([
            'agent.service', // ✅ correction ici
            'service',
            'utilisateur',
            'organisation'
        ])
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc');

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
    // Chargement des relations manquantes (évite les requêtes N+1)
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
            // ✅ AGENT : Formatage inline direct (sans formatUser)
            'agent' => $courrier->agent ? [
                'id'           => $courrier->agent->id,
                'nom'          => $courrier->agent->nom,
                'prenom'       => $courrier->agent->prenom,
                'nom_complet'  => trim($courrier->agent->prenom . ' ' . $courrier->agent->nom),
                'email'        => $courrier->agent->email ?? null,
                'fonction'     => $courrier->agent->fonction ?? 'Agent',
                'telephone'    => $courrier->agent->telephone ?? null,
                'service_id'   => $courrier->agent->service_id,
                // Si l'agent a une relation avec un modèle User, tu peux aussi ajouter :
                // 'user_id' => $courrier->agent->user_id,
            ] : null,
            
            // Service : Formatage simple avec formatRelation
            'service' => $this->formatRelation($courrier->service, ['id', 'nom']),
            
            // Utilisateur : On garde formatUser si c'est utile ailleurs
            'utilisateur' => $this->formatUser($courrier->utilisateur),
            
            // Organisation : Formatage simple
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

    public function supprimer(int $id): bool
{
    // 1. Récupérer le courrier
    $courrier = $this->repo->findOrFail($id);

    // 2. Supprimer le fichier physique s'il existe
    if (!empty($courrier->url_fichier)) {
        // Vérifie si le fichier existe sur le disque 'public'
        if (Storage::disk('public')->exists($courrier->url_fichier)) {
            Storage::disk('public')->delete($courrier->url_fichier);
        }
    }

    // 3. Mettre à jour l'état à 0 (Inactif/Supprimé)
    // On utilise update directement sur le modèle ou via le repo selon ton architecture
    $courrier->update([
        'etat' => 0 
    ]);

    return true;
}
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
    // Base query : courriers actifs uniquement
    $base = \App\Models\Courrier::where('etat', 1);

    return [
        // KPI Cards
        'total'        => (clone $base)->count(),
        'entrants'     => (clone $base)->where('type', \App\Models\Courrier::TYPE_ENTRANT)->count(),
        'sortants'     => (clone $base)->where('type', \App\Models\Courrier::TYPE_SORTANT)->count(),
        'internes'     => (clone $base)->where('type', \App\Models\Courrier::TYPE_INTERNE)->count(),
        'tres_urgents' => (clone $base)->where('priorite', \App\Models\Courrier::PRIORITE_TRES_URGENTE)->count(),
        'archives'     => (clone $base)->where('statut', \App\Models\Courrier::STATUT_ARCHIVE)->count(),

       
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

   /**
 * Formate une valeur enum (type, statut, priorité, etc.)
 * Accepte null pour éviter les erreurs lors de la création/modification
 */
protected function formatEnum(int|string|null $code, array $list): array
{
    // Si le code est null ou vide, on retourne une valeur par défaut sûre
    if ($code === null || $code === '') {
        return [
            'code' => null,
            'libelle' => 'Non défini'
        ];
    }

    // Conversion explicite en entier ou chaîne selon la clé du tableau
    // Cela évite les erreurs si la base renvoie "0" (string) au lieu de 0 (int)
    $key = is_int($code) ? $code : (is_numeric($code) ? (int)$code : $code);

    return [
        'code' => $code,
        'libelle' => $list[$key] ?? 'Inconnu'
    ];
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