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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('type');                // UE ou EC
            $table->string('code');                // UE1, EC1, etc.
            $table->string('name');                // Nom complet
            $table->integer('order');              // Ordre d'affichage
            $table->unsignedBigInteger('parent_id')->nullable();  // Pour lier EC à son UE
            $table->foreignId('semestre_id')->constrained('semestres')->onDelete('cascade');
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->foreignId('parcour_id')->constrained('parcours')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();  // Pour la suppression logique

            // Self-referencing foreign key
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('programmes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
