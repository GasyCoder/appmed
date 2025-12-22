<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('ID de l\'enseignant');
            
            $table->foreignId('programme_id')
                  ->constrained('programmes')
                  ->onDelete('cascade')
                  ->comment('ID du programme (EC)');
            
            // Heures d'enseignement
            $table->integer('heures_cm')->default(0)->comment('Heures Cours Magistral');
            $table->integer('heures_td')->default(0)->comment('Heures Travaux Dirigés');
            $table->integer('heures_tp')->default(0)->comment('Heures Travaux Pratiques');
            
            // Enseignant responsable de l'EC
            $table->boolean('is_responsable')->default(false)->comment('Enseignant responsable du programme');
            
            // Métadonnées
            $table->text('note')->nullable()->comment('Notes ou remarques');
            $table->timestamps();
            
            // Index et contraintes
            $table->unique(['user_id', 'programme_id'], 'unique_user_programme');
            $table->index(['programme_id', 'is_responsable']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programme_user');
    }
};