<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 👤 Identité & Authentification (conservés pour compatibilité Laravel)
            $table->string('name')->comment('Nom complet ou identifiant d\'affichage');
            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // 🔐 Rôles & Habilitations
            $table->string('role')->default('agent')->index()->comment('admin, agent, secretaire, chef_service, super_admin');

            // 🗑️ État du compte (cohérent avec le reste du projet)
            $table->integer('etat')->default(1)->index()->comment('1=actif, 2=suspendu/supprimé');

            // 📱 Profil & Métadonnées
            $table->string('telephone')->nullable();
            $table->string('avatar')->nullable()->comment('Chemin relatif vers la photo de profil');
            $table->timestamp('derniere_connexion')->nullable()->index()->comment('Dernière authentification réussie');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};