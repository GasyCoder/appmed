<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('type')->default('info'); // info|warning|success|danger
            $table->string('title');
            $table->text('body');

            // CTA optionnel
            $table->string('action_label')->nullable();
            $table->string('action_url')->nullable();

            // Activation
            $table->boolean('is_active')->default(true);

            // Audience: null = tout le monde, sinon ["student","teacher","admin"]
            $table->json('audience_roles')->nullable();

            // Période (optionnelle)
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Auteur
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
