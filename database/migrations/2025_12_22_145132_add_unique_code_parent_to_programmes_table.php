<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            // Supprimer l'ancien index unique sur 'code' s'il existe
            // $table->dropUnique(['code']); // Décommente si tu avais un index unique sur code
            
            // Ajouter une contrainte unique composite : code + parent_id
            // Cela permet d'avoir EC1, EC2, etc. dans chaque UE
            $table->unique(['code', 'parent_id'], 'unique_code_per_parent');
        });
    }

    public function down(): void
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropUnique('unique_code_per_parent');
        });
    }
};