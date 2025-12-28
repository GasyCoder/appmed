<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Ajouter les colonnes manquantes utilisées par ton code
        Schema::table('documents', function (Blueprint $table) {

            if (!Schema::hasColumn('documents', 'source_url')) {
                $table->string('source_url', 2048)->nullable()->after('file_path');
            }

            // Colonne utilisée par ton code actuel
            if (!Schema::hasColumn('documents', 'file_size_bytes')) {
                $table->unsignedBigInteger('file_size_bytes')->nullable()->after('converted_from');
            }

            if (!Schema::hasColumn('documents', 'is_archive')) {
                $table->boolean('is_archive')->default(false)->after('is_actif');
            }

            // Ton code insère programme_id (vu dans tes logs précédents)
            if (!Schema::hasColumn('documents', 'programme_id')) {
                $table->foreignId('programme_id')->nullable()->after('semestre_id')->index();
                // FK si table programmes existe (sinon commente la ligne suivante)
                $table->foreign('programme_id')->references('id')->on('programmes')->nullOnDelete();
            }
        });

        // 2) Corriger file_size (NOT NULL) => le rendre tolérant
        // Important: on évite doctrine/dbal, on passe par SQL direct.
        if (Schema::hasColumn('documents', 'file_size')) {
            // MySQL/MariaDB : rendre nullable + default 0
            DB::statement("ALTER TABLE documents MODIFY file_size BIGINT NULL DEFAULT 0");
        }

        // 3) Backfill: si file_size existe déjà, recopier dans file_size_bytes si vide
        if (Schema::hasColumn('documents', 'file_size') && Schema::hasColumn('documents', 'file_size_bytes')) {
            DB::table('documents')
                ->whereNull('file_size_bytes')
                ->update(['file_size_bytes' => DB::raw('file_size')]);
        }
    }

    public function down(): void
    {
        // Rollback raisonnable (sans casser)
        Schema::table('documents', function (Blueprint $table) {

            if (Schema::hasColumn('documents', 'programme_id')) {
                // drop FK si présente
                try { $table->dropForeign(['programme_id']); } catch (\Throwable $e) {}
                $table->dropColumn('programme_id');
            }

            if (Schema::hasColumn('documents', 'is_archive')) {
                $table->dropColumn('is_archive');
            }

            if (Schema::hasColumn('documents', 'file_size_bytes')) {
                $table->dropColumn('file_size_bytes');
            }

            if (Schema::hasColumn('documents', 'source_url')) {
                $table->dropColumn('source_url');
            }
        });

        // Remettre file_size NOT NULL (optionnel)
        if (Schema::hasColumn('documents', 'file_size')) {
            DB::statement("ALTER TABLE documents MODIFY file_size BIGINT NOT NULL");
        }
    }
};
