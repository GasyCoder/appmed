<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'conversion_status')) {
                $table->string('conversion_status', 20)->default('none')->after('original_extension');
            }
            if (!Schema::hasColumn('documents', 'conversion_error')) {
                $table->text('conversion_error')->nullable()->after('conversion_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'conversion_error')) {
                $table->dropColumn('conversion_error');
            }
            if (Schema::hasColumn('documents', 'conversion_status')) {
                $table->dropColumn('conversion_status');
            }
        });
    }
};
