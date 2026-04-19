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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();

            // 🏢 Identification
            $table->string('nom')->index()->comment('Nom officiel de l\'organisation');
            $table->string('sigle')->unique()->nullable()->index()->comment('Sigle ou acronyme (ex: MINFI, DGSN, UNESCO)');
            $table->tinyInteger('type')->default(0)->comment('0=externe, 1=interne, 2=gouvernementale, 3=privée, 4=ONG');

            // 📍 Coordonnées
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_principal')->nullable()->comment('Personne de contact principale');

            // 🗑️ Cycle de vie (cohérent avec le reste du projet)
            $table->integer('etat')->default(1)->index()->comment('1=actif, 2=inactif/supprimé');

            // 🔍 Index composites pour les recherches fréquentes
            $table->index(['type', 'etat']);
    

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};