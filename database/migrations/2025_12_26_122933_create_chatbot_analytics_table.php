<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->boolean('answered')->default(false);
            $table->integer('count')->default(1);
            $table->timestamps();
            
            $table->index('question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_analytics');
    }
};
