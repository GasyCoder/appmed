<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['UE', 'EC'])->comment('Type de programme: UE ou EC');
            $table->string('code', 20)->comment('Code unique du programme');
            $table->string('name')->comment('Nom complet du programme');
            $table->integer('order')->default(1)->comment('Ordre d\'affichage');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('ID de l\'UE parente pour les ECs');
            
            // Relations
            $table->foreignId('semestre_id')->constrained('semestres')->onDelete('cascade');
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->foreignId('parcour_id')->constrained('parcours')->onDelete('cascade');
            
            // Nouveaux champs
            $table->integer('credits')->nullable()->comment('Crédits ECTS');
            $table->decimal('coefficient', 5, 2)->nullable()->comment('Coefficient');
            
            $table->boolean('status')->default(true)->comment('Statut actif/inactif');
            $table->timestamps();
            $table->softDeletes();

            // Self-referencing foreign key
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('programmes')
                  ->onDelete('cascade');

            // Index pour optimiser les requêtes
            $table->index(['type', 'status']);
            $table->index(['semestre_id', 'order']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};