<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveau_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('parcour_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcour_id')->constrained('parcours')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveau_user');
        Schema::dropIfExists('parcour_user');
    }
};
