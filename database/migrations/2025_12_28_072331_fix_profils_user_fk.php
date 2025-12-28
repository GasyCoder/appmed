<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Nettoyer les orphelins AVANT toute FK vers users
        DB::statement("
            DELETE p FROM profils p
            LEFT JOIN users u ON u.id = p.user_id
            WHERE u.id IS NULL
        ");

        // 2) Trouver et dropper toute FK existante sur profils.user_id (quel que soit son nom)
        $dbName = DB::getDatabaseName();

        $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', 'profils')
            ->where('COLUMN_NAME', 'user_id')
            ->whereNotNull('CONSTRAINT_NAME')
            ->where('CONSTRAINT_NAME', '!=', 'PRIMARY')
            ->value('CONSTRAINT_NAME');

        if ($fkName) {
            DB::statement("ALTER TABLE `profils` DROP FOREIGN KEY `$fkName`");
        }

        // 3) Ajouter la FK correcte vers users.id (si elle n'existe pas déjà)
        Schema::table('profils', function (Blueprint $table) {
            // Important : s'assurer que user_id est du bon type
            // (si besoin : $table->unsignedBigInteger('user_id')->change();)

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
