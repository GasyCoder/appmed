<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type'); // pdf, jpg, png
            $table->unsignedBigInteger('file_size');
            $table->string('academic_year'); // ex: 2024-2025
            $table->enum('type', ['emploi_du_temps', 'planning_examens', 'calendrier'])->default('emploi_du_temps');
            
            // Relations optionnelles
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('cascade');
            $table->foreignId('parcour_id')->nullable()->constrained('parcours')->onDelete('cascade');
            $table->foreignId('semestre_id')->nullable()->constrained('semestres')->onDelete('cascade');
            
            // Période d'application
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};