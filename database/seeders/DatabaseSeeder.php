<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organisation;
use App\Models\Service;
use App\Models\User;
use App\Models\Agent;
use App\Models\Courrier;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ====================================================================
        // 🏢 1. ORGANISATIONS (racine - pas de dépendances)
        // ====================================================================
        
        // Organisation principale (la vôtre)
        $orgPrincipale = Organisation::factory()
            ->interne()
            ->avecCoordonnees()
            ->create([
                'nom' => 'Direction Générale de l\'Administration',
                'sigle' => 'DGA',
            ]);

        // Organisations partenaires (externes)
        $organisationsExternes = Organisation::factory()
            ->count(5)
            ->externe()
            ->create();

        // Organisations gouvernementales (pour courriers officiels)
        $organisationsGouv = Organisation::factory()
            ->count(3)
            ->gouvernementale()
            ->avecCoordonnees()
            ->create();

        // ====================================================================
        // 🏢 2. SERVICES (dépendent des organisations)
        // ====================================================================
        
        // Services de l'organisation principale
        $servicesPrincipaux = [
            'Ressources Humaines',
            'Courrier & Archives',
            'Informatique',
            'Juridique',
            'Finances',
        ];

        $servicesDGA = collect($servicesPrincipaux)->map(fn ($nom) => 
            Service::factory()->pourOrganisation($orgPrincipale)->create(['nom' => $nom])
        );

        // Services aléatoires pour les autres organisations
        $toutesAutresOrgs = $organisationsExternes->merge($organisationsGouv);
        Service::factory()
            ->count(10)
            ->create(fn () => ['organisation_id' => $toutesAutresOrgs->random()->id]);

        // ====================================================================
        // 👤 3. USERS (authentification - pas de FK métier)
        // ====================================================================
        
        // Super-admin pour accéder à l'interface d'administration
        $admin = User::factory()
            ->admin()
            ->withPassword('admin123')
            ->create([
                'name' => 'Administrateur Système',
                'email' => 'admin@gestion.local',
            ]);

        // Chefs de service (liés aux services DGA)
        $chefsService = $servicesDGA->map(fn ($service) => 
            User::factory()
                ->withRole('chef_service')
                ->withPassword('chef123')
                ->create([
                    'name' => fake()->name(),
                    'email' => strtolower(str_replace(' ', '.', $service->nom)) . '@dga.local',
                ])
        );

        // Agents classiques
        $agentsUsers = User::factory()
            ->count(15)
            ->withRole('agent')
            ->withPassword('agent123')
            ->create();

        // ====================================================================
        // 👨‍💼 4. AGENTS (profil métier - dépendent de users + services)
        // ====================================================================
        
        // Créer un agent pour chaque chef de service
        $chefsService->each(fn ($user, $index) => 
            Agent::factory()->create([
                'user_id' => $user->id,
                'service_id' => $servicesDGA->get($index)?->id ?? $servicesDGA->first()->id,
                'nom' => explode(' ', $user->name)[0] ?? 'Nom',
                'prenom' => explode(' ', $user->name)[1] ?? 'Prénom',
                'fonction' => 'Chef de service',
            ])
        );

        // Agents opérationnels (aléatoires) - ✅ Correction : utiliser state() avec closure
        Agent::factory()
            ->count(20)
            ->state(fn () => [
                'user_id' => $agentsUsers->random()->id,
                'service_id' => $servicesDGA->random()->id,
                'fonction' => fake()->randomElement(['Agent de saisie', 'Gestionnaire', 'Assistant', 'Chargé de mission']),
            ])
            ->create();

        // ====================================================================
        // 📬 5. COURRIERS (dépendent de tout le reste)
        // ====================================================================
        
        // Courriers récents non traités (à affecter)
        Courrier::factory()
            ->count(10)
            ->entrant()
            ->urgent()
            ->create(fn () => [
                'service_id' => $servicesDGA->random()->id,
                'organisation_id' => $organisationsExternes->random()->id,
            ]);

        // Courriers affectés et en cours de traitement
        // ✅ Correction : récupérer les IDs AVANT d'appeler affecte()
        for ($i = 0; $i < 15; $i++) {
            $agentAleatoire = Agent::inRandomOrder()->first();
            $serviceAleatoire = Service::inRandomOrder()->first();
            
            Courrier::factory()
                ->entrant()
                ->affecte(
                    agentId: $agentAleatoire?->user_id,
                    serviceId: $serviceAleatoire?->id
                )
                ->avecFichier()
                ->create();
        }

        // Courriers traités (historique)
        Courrier::factory()
            ->count(20)
            ->traite()
            ->avecFichier()
            ->create(fn () => [
                'organisation_id' => $organisationsGouv->random()->id,
                'date_traitement' => fake()->dateTimeBetween('-3 months', '-1 month'),
            ]);

        // Courriers "supprimés" (pour tester la corbeille)
        Courrier::factory()
            ->count(5)
            ->supprime()
            ->create();

        // ====================================================================
        // 📊 Résumé en console (optionnel mais utile en dev)
        // ====================================================================
        
        if (app()->environment('local')) {
            $this->command->info('✅ Seed terminé avec succès !');
            $this->command->table(['Entité', 'Quantité'], [
                ['Organisations', Organisation::count()],
                ['Services', Service::count()],
                ['Users', User::count()],
                ['Agents', Agent::count()],
                ['Courriers', Courrier::count()],
                ['→ Courriers actifs', Courrier::where('etat', 1)->count()],
                ['→ Courriers supprimés', Courrier::where('etat', 2)->count()],
            ]);
            $this->command->line("🔑 Admin de test : admin@gestion.local / admin123");
        }
    }

    /**
     * Nettoie les tables avant seeding (uniquement en dev).
     * ⚠️ Attention : supprime TOUTES les données !
     */
    protected function cleanDatabase(): void
    {
        // Ordre inverse des dépendances pour éviter les erreurs FK
        \DB::table('courriers')->truncate();
        \DB::table('agents')->truncate();
        \DB::table('services')->truncate();
        \DB::table('organisations')->truncate();
        \DB::table('users')->where('email', '!=', 'admin@gestion.local')->delete();
    }
}