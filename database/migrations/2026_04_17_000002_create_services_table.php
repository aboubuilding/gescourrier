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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // 🏢 Identification
            $table->string('nom')->index()->comment('Nom complet du service');
            $table->text('description')->nullable();

            // 📍 Coordonnées & localisation
            $table->string('localisation')->nullable()->comment('Bâtiment, étage, bureau');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();

            // 🔗 Rattachement organisationnel
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->nullOnDelete()
                  ->comment('Organisation ou entité de rattachement');

            // 🗑️ Cycle de vie (cohérent avec courriers & agents)
            $table->integer('etat')->default(1)->index()->comment('1=actif, 2=inactif/supprimé');

            // 🔍 Index composites pour les requêtes courantes
            $table->index(['organisation_id', 'etat']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};