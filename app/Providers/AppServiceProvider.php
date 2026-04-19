<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

// 🔗 Contrats (Interfaces)
use App\Repositories\Interfaces\CourrierInterface;
use App\Repositories\Interfaces\AgentInterface;
use App\Repositories\Interfaces\OrganisationInterface;
use App\Repositories\Interfaces\ServiceInterface;
use App\Repositories\Interfaces\UserInterface;

// 📦 Implémentations concrètes
use App\Repositories\Eloquent\CourrierRepository;
use App\Repositories\Eloquent\AgentRepository;
use App\Repositories\Eloquent\OrganisationRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\UserRepository;

// 🛡️ Middlewares personnalisés
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\CheckRole;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Register any application services.
     * C'est ici qu'on lie les Interfaces aux Répositories concrets
     * et qu'on configure les services globaux.
     */
    public function register(): void
    {
        // ── 🔗 Bindings Repository (Interface → Implémentation) ──
        // Permet l'injection de dépendances automatique dans les contrôleurs/services
        $this->app->singleton(CourrierInterface::class, CourrierRepository::class);
        $this->app->singleton(AgentInterface::class, AgentRepository::class);
        $this->app->singleton(OrganisationInterface::class, OrganisationRepository::class);
        $this->app->singleton(ServiceInterface::class, ServiceRepository::class);
        $this->app->singleton(UserInterface::class, UserRepository::class);

        // 💡 Note : BaseRepository n'a PAS besoin de binding.
        // C'est une classe abstraite étendue par les repositories concrets.

        // ── 🎯 Bindings de services utilitaires (optionnels) ──
        // Exemple : si tu as un service de notification ou d'export
        // $this->app->singleton(\App\Services\NotificationService::class, function ($app) {
        //     return new \App\Services\NotificationService(
        //         $app->make(\Illuminate\Mail\Mailer::class),
        //         $app->make(\Illuminate\Notifications\ChannelManager::class)
        //     );
        // });

        // ── 🧩 Configuration des JSON Resources ──
        // Supprime l'enveloppe "data" par défaut pour les réponses API si désiré
        // JsonResource::wrap(null);
        
        // ── 🌐 Configuration de l'URL de l'application en CLI ──
        // Utile pour les commandes Artisan qui génèrent des URLs
        if ($this->app->runningInConsole() && config('app.url')) {
            $this->app['url']->forceRootUrl(config('app.url'));
        }
    }

    /**
     * Bootstrap any application services.
     * Configuration globale, middlewares, view composers, macros, etc.
     */
    public function boot(): void
    {
        // ── 🛡️ Enregistrement des middlewares personnalisés ──
        // Pour Laravel 11+ (bootstrap/app.php), ces aliases sont optionnels
        // mais utiles pour la documentation et la cohérence
        $this->app['router']->aliasMiddleware('check.active', EnsureUserIsActive::class);
        $this->app['router']->aliasMiddleware('role', CheckRole::class);

        // ── 📄 Pagination : URL personnalisées ──
        // Force l'utilisation de Bootstrap 5 pour les liens de pagination
        Paginator::useBootstrapFive();
        // Ou useBootstrapFour() / useTailwind() selon ton frontend

        // ── 🔒 Modèles Eloquent : Configuration de sécurité ──
        // Empêche l'assignation massive non contrôlée par défaut
        Model::preventLazyLoading(!app()->isProduction()); // Détecte les N+1 en dev
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());
        Model::preventAccessingMissingAttributes(!app()->isProduction());

        // ── 🎨 Blade Directives personnalisées ──
        
        // @role('admin') ... @endrole
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && in_array(auth()->user()->role, [$role])): ?>";
        });
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // @haspermission('courriers.create') ... @endhaspermission
        Blade::directive('haspermission', function ($permission) {
            return "<?php if(auth()->check() && auth()->user()->can($permission)): ?>";
        });
        Blade::directive('endhaspermission', function () {
            return "<?php endif; ?>";
        });

        // @formatDate($date, 'd/m/Y')
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo $expression ? \Carbon\Carbon::parse($expression)->format('d/m/Y') : ''; ?>";
        });

        // @currency($amount) → affiche "1 250 FCFA"
        Blade::directive('currency', function ($expression) {
            return "<?php echo number_format($expression, 0, ',', ' ') . ' FCFA'; ?>";
        });

        // ── 👁️ View Composers : Partage de données globales aux vues ──
        
        // Partage les notifications non lues à toutes les vues (si tu as un système de notifications)
        View::composer('*', function ($view) {
            if (auth()->check()) {
                // Exemple : $view->with('unreadNotifications', auth()->user()->unreadNotifications->count());
                $view->with('currentYear', now()->year);
                $view->with('appName', config('app.name', 'Gestion Courriers'));
            }
        });

        // View composer spécifique pour le layout principal
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                // Stats rapides pour la sidebar (avec cache 10 min)
                $stats = Cache::remember('sidebar_stats_'.auth()->id(), 600, function () {
                    return [
                        'courriers_pending' => \App\Models\Courrier::where('statut', 1)
                            ->where('etat', 1)->count(),
                        'affectations_mine' => \App\Models\Courrier::where('agent_id', auth()->id())
                            ->where('statut', '!=', 3)->where('etat', 1)->count(),
                    ];
                });
                $view->with('sidebarStats', $stats);
            }
        });

        // ── 🔧 Macros utilitaires pour les Collections ──
        
        // $collection->toSelect2() → format pour Select2 JS
        \Illuminate\Support\Collection::macro('toSelect2', function ($key = 'id', $value = 'name') {
            return $this->map(fn($item) => [
                'id' => $item->$key,
                'text' => $item->$value,
            ])->values();
        });

        // $collection->groupByMonth('created_at') → groupe par mois
        \Illuminate\Support\Collection::macro('groupByMonth', function ($dateField) {
            return $this->groupBy(function ($item) use ($dateField) {
                return $item->$dateField?->format('Y-m');
            });
        });

        // ── 🌍 Configuration locale par défaut ──
        // Force le fuseau horaire et la locale pour toute l'app
        config(['app.timezone' => 'Africa/Lome']);
        config(['app.locale' => 'fr']);
        \Carbon\Carbon::setLocale('fr');

        // ── 🗄️ Configuration DB par défaut (optionnel) ──
        // Exemple : forcer UTF8MB4 pour les emojis et caractères spéciaux
        // \DB::statement("SET NAMES 'utf8mb4'");
    }
}