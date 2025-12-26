<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->index(); // Pour les visiteurs non connectés
            $table->enum('role', ['user', 'assistant'])->default('user');
            $table->text('message');
            $table->json('metadata')->nullable(); // Pour stocker infos supplémentaires
            $table->timestamps();
            
            $table->index(['session_id', 'created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};