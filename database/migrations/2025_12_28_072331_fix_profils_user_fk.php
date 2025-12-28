<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            // si le nom du FK est bien "profils_user_id_foreign"
            $table->dropForeign('profils_user_id_foreign');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('profils', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('_users')
                ->onDelete('cascade');
        });
    }
};
