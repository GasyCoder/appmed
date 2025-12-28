<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colonnes (si pas déjà présentes)
        Schema::table('document_views', function (Blueprint $table) {
            if (!Schema::hasColumn('document_views', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('document_views', 'downloaded_at')) {
                $table->timestamp('downloaded_at')->nullable()->after('viewed_at');
            }
        });

        // Unicité doc/user (évite doublons)
        Schema::table('document_views', function (Blueprint $table) {
            $table->unique(['document_id', 'user_id'], 'document_views_document_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('document_views', function (Blueprint $table) {
            $table->dropUnique('document_views_document_user_unique');

            if (Schema::hasColumn('document_views', 'downloaded_at')) {
                $table->dropColumn('downloaded_at');
            }
            if (Schema::hasColumn('document_views', 'viewed_at')) {
                $table->dropColumn('viewed_at');
            }
        });
    }
};
