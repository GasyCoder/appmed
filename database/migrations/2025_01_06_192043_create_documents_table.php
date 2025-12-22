<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('original_filename')->nullable();
            $table->string('original_extension', 10)->nullable();
            $table->string('converted_from', 10)->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->string('protected_path')->nullable();
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->boolean('is_actif')->default(false);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);

            // Clés étrangères
            $table->foreignId('niveau_id')->constrained('niveaux'); // Spécifier la table explicitement
            $table->foreignId('semestre_id')->constrained('semestres')->onDelete('cascade');
            $table->foreignId('parcour_id')->constrained('parcours'); // Spécifier la table explicitement
            $table->foreignId('uploaded_by')->constrained('users');

            $table->index('original_extension', 'idx_documents_original_extension');
            $table->index('converted_from', 'idx_documents_converted_from');
            $table->index(['is_actif', 'niveau_id', 'parcour_id'], 'idx_documents_access');
            $table->index('converted_at', 'idx_documents_converted_at');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
