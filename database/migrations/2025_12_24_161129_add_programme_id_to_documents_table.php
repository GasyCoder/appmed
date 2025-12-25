<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // nullable pour ne pas casser les anciens enregistrements
            $table->foreignId('programme_id')
                ->nullable()
                ->after('semestre_id') // adapte selon ta table
                ->constrained('programmes')
                ->nullOnDelete();

            $table->index('programme_id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['programme_id']);
            $table->dropIndex(['programme_id']);
            $table->dropColumn('programme_id');
        });
    }
};
