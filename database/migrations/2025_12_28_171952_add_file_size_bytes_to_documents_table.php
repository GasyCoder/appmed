<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'file_size_bytes')) {
                // Taille en octets du fichier local
                $table->unsignedBigInteger('file_size_bytes')
                    ->nullable()
                    ->after('converted_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'file_size_bytes')) {
                $table->dropColumn('file_size_bytes');
            }
        });
    }
};
