<?php

namespace App\Services;

use App\Repositories\Interfaces\CourrierInterface;
use App\Repositories\Interfaces\AgentInterface;
use App\Repositories\Interfaces\ServiceInterface;
use App\Repositories\Interfaces\OrganisationInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Models\Courrier;
use App\Models\User;
use App\Models\Agent;
use App\Models\Service;
use App\Models\Organisation;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function __construct(
        protected CourrierInterface $courrierRepo,
        protected AgentInterface $agentRepo,
        protected ServiceInterface $serviceRepo,
        protected OrganisationInterface $orgRepo,
        protected UserInterface $userRepo
    ) {}

    /**
     * Retourne l'ensemble des données du tableau de bord.
     */
    public function getOverview(): array
    {
        return [
            'kpis'            => $this->getKPIs(),
            'statuts_courrier'=> $this->getStatutsCourrier(),
            'alertes'         => $this->getAlertesUrgentes(),
            'activite_recente'=> $this->getActiviteRecente(),
            'genere_le'       => now()->format('Y-m-d H:i:s'),
        ];
    }

    // ========================================================================
    // 📊 Indicateurs Clés (KPIs)
    // ========================================================================

    protected function getKPIs(): array
    {
        return [
            'courriers_total'   => $this->courrierRepo->query()->where('etat', Courrier::ETAT_ACTIF)->count(),
            'courriers_ce_mois' => $this->courrierRepo->query()
                ->where('etat', Courrier::ETAT_ACTIF)
                ->whereMonth('date_reception', now()->month)
                ->whereYear('date_reception', now()->year)
                ->count(),
            'agents_actifs'     => $this->agentRepo->query()->where('etat', Agent::ETAT_ACTIF)->count(),
            'services_actifs'   => $this->serviceRepo->query()->where('etat', Service::ETAT_ACTIF)->count(),
            'organisations'     => $this->orgRepo->query()->where('etat', Organisation::ETAT_ACTIF)->count(),
            'utilisateurs'      => $this->userRepo->query()->where('etat', User::ETAT_ACTIF)->count(),
        ];
    }

    protected function getStatutsCourrier(): array
    {
        $base = $this->courrierRepo->query()->where('etat', Courrier::ETAT_ACTIF);
        
        return [
            'non_affecte' => (clone $base)->where('statut', Courrier::STATUT_NON_AFFECTE)->count(),
            'affecte'     => (clone $base)->where('statut', Courrier::STATUT_AFFECTE)->count(),
            'traite'      => (clone $base)->where('statut', Courrier::STATUT_TRAITE)->count(),
            'urgents'     => (clone $base)
                ->where('priorite', '>=', Courrier::PRIORITE_URGENTE)
                ->where('statut', '!=', Courrier::STATUT_TRAITE)
                ->count(),
        ];
    }

    // ========================================================================
    // 🚨 Alertes & Notifications
    // ========================================================================

    protected function getAlertesUrgentes(): array
    {
        // Courriers urgents ou très urgents non encore traités
        $alertes = $this->courrierRepo->query()
            ->where('etat', Courrier::ETAT_ACTIF)
            ->where('priorite', '>=', Courrier::PRIORITE_URGENTE)
            ->where('statut', '!=', Courrier::STATUT_TRAITE)
            ->orderBy('priorite', 'desc')
            ->orderBy('date_reception', 'asc')
            ->limit(5)
            ->get(['id', 'reference', 'objet', 'priorite', 'date_reception']);

        return $alertes->map(fn($c) => [
            'id'         => $c->id,
            'reference'  => $c->reference,
            'objet'      => $c->objet,
            'priorite'   => $c->priorite,
            'recu_le'    => Carbon::parse($c->date_reception)->format('d/m/Y'),
            'delai_jours'=> $c->date_reception ? Carbon::parse($c->date_reception)->diffInDays(now()) : 0,
        ])->toArray();
    }

    // ========================================================================
    // 🔄 Flux d'activité récent
    // ========================================================================

    protected function getActiviteRecente(): array
    {
        return $this->courrierRepo->query()
            ->where('etat', Courrier::ETAT_ACTIF)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'reference', 'objet', 'statut', 'updated_at'])
            ->map(function ($c) {
                $action = match ($c->statut) {
                    Courrier::STATUT_NON_AFFECTE => '📥 Reçu / Créé',
                    Courrier::STATUT_AFFECTE     => '👤 Affecté',
                    Courrier::STATUT_TRAITE      => '✅ Traité',
                    default                      => '🔄 Mis à jour',
                };

                return [
                    'id'        => $c->id,
                    'reference' => $c->reference,
                    'objet'     => $c->objet,
                    'action'    => $action,
                    'date'      => $c->updated_at->diffForHumans(),
                ];
            })->toArray();
    }
}