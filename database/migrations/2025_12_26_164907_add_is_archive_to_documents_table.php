<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'is_archive')) {
                $table->boolean('is_archive')->default(false)->after('is_actif');
            }
        });

        // Remplacer l'index existant idx_documents_access pour inclure is_archive
        // (sinon les listes "archives / non archives" seront moins optimisées)
        $hasOldIndex = false;

        try {
            $rows = DB::select("SHOW INDEX FROM `documents` WHERE Key_name = 'idx_documents_access'");
            $hasOldIndex = !empty($rows);
        } catch (\Throwable $e) {
            // Si SHOW INDEX échoue (rare), on n'empêche pas la migration
            $hasOldIndex = false;
        }

        Schema::table('documents', function (Blueprint $table) use ($hasOldIndex) {
            if ($hasOldIndex) {
                $table->dropIndex('idx_documents_access');
            }

            // Index optimisé pour accès étudiant + filtre archive
            $table->index(['is_actif', 'is_archive', 'niveau_id', 'parcour_id'], 'idx_documents_access');
        });
    }

    public function down(): void
    {
        // On restaure l'ancien index sans is_archive
        $hasIndex = false;

        try {
            $rows = DB::select("SHOW INDEX FROM `documents` WHERE Key_name = 'idx_documents_access'");
            $hasIndex = !empty($rows);
        } catch (\Throwable $e) {
            $hasIndex = false;
        }

        Schema::table('documents', function (Blueprint $table) use ($hasIndex) {
            if ($hasIndex) {
                $table->dropIndex('idx_documents_access');
            }

            // Ancien index (comme dans ta migration initiale)
            $table->index(['is_actif', 'niveau_id', 'parcour_id'], 'idx_documents_access');

            if (Schema::hasColumn('documents', 'is_archive')) {
                $table->dropColumn('is_archive');
            }
        });
    }
};
