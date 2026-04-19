<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();

            // 🪪 Identifiants
          
            $table->string('nom');
            $table->string('prenom');

            // 📞 Coordonnées
            $table->string('email')->unique()->nullable();
            $table->string('telephone')->nullable();

            // 💼 Informations métier
            $table->string('fonction')->nullable()->comment('Poste ou rôle attribué');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete()->comment('Service de rattachement');

            // 🔐 Lien avec l'authentification (optionnel mais recommandé)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Compte Laravel associé si l\'agent se connecte');

            // 🗑️ Gestion du cycle de vie (même logique que courriers)
            $table->integer('etat')->default(1)->index()->comment('1=actif, 2=inactif/supprimé');

            // 🔍 Index composites pour les recherches fréquentes
            $table->index(['service_id', 'etat']);
            $table->index(['nom', 'prenom']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};