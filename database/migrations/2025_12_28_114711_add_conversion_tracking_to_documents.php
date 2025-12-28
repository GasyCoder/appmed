<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Optionnel : pour tracker les échecs de conversion
            $table->boolean('conversion_failed')->default(false)->after('converted_at');
            $table->text('conversion_error')->nullable()->after('conversion_failed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['conversion_failed', 'conversion_error']);
        });
    }
};