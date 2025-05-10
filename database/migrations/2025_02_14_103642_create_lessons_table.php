<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained()->onDelete('cascade');
            $table->foreignId('parcour_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('semestre_id')->constrained()->onDelete('cascade'); // Ajout de la relation avec semestres
            $table->foreignId('programme_id')->constrained()->onDelete('cascade');
            $table->string('color')->default('#2563eb');
            $table->integer('weekday');  // 1-6 pour Lundi-Samedi
            $table->time('start_time');
            $table->time('end_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('salle');
            $table->string('type_cours')->nullable(); // CM, TD, TP
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
