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
        Schema::create('courriers', function (Blueprint $table) {
            $table->id();

            // 🔖 Identifiants métier
            $table->string('reference')->nullable()->index()->comment('Référence unique (externe ou interne)');
            $table->string('numero')->nullable()->index()->comment('Numéro de chronologie/enregistrement');

            // 📊 Classification & Cycle de vie
            $table->tinyInteger('type')->default(0)->comment('0=entrant, 1=sortant, 2=interne');
            $table->tinyInteger('priorite')->default(0)->comment('0=normale, 1=urgente, 2=très_urgente');
            $table->tinyInteger('statut')->default(0)->comment('0=non_affecte, 1=affecte, 2=traite');

            // 📝 Contenu
            $table->string('objet')->nullable()->comment('Objet du courrier');
            $table->text('description')->nullable()->comment('Contenu détaillé ou notes');

        
            // 📅 Dates métier
            $table->date('date_reception')->nullable()->index();
            $table->date('date_envoi')->nullable();
            $table->timestamp('date_affectation')->nullable();
            $table->timestamp('date_traitement')->nullable();

            // 📎 Gestion des fichiers scannés
            $table->string('url_fichier')->nullable()->comment('Chemin relatif dans storage/app/public');
            $table->string('fichier_nom_original')->nullable()->comment('Nom du fichier lors de l\'upload');
            $table->string('fichier_mime_type')->nullable()->comment('Ex: application/pdf, image/jpeg');
            $table->unsignedBigInteger('fichier_taille')->nullable()->comment('Taille en octets');

            // 🔗 Relations (clés étrangères)
            // ⚠️ ADAPTE les noms de tables si tes modèles s'appellent différemment
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete()->comment('Agent responsable du traitement');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete()->comment('Service concerné');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->nullOnDelete()->comment('Utilisateur ayant déchargé/validé');
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->nullOnDelete()->comment('Organisation expéditrice/réceptrice');

            // 🗑️ Suppression logique (1=actif, 2=supprimé)
            $table->integer('etat')->default(1)->index()->comment('1=actif, 2=supprimé');

            // 🔍 Index composites pour les requêtes fréquentes
            $table->index(['service_id', 'statut', 'etat']);
            $table->index(['date_reception', 'type', 'etat']);

            // ⏱️ Timestamps standards
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courriers');
    }
};