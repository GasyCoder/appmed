<?php
// database/seeders/NiveauSeeder.php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    public function run(): void
    {
        $niveaux = [
            [
                'sigle' => 'M1',
                'name' => 'Master 1',
                'status' => true,
            ],
            [
                'sigle' => 'M2',
                'name' => 'Master 2',
                'status' => true,
            ],
        ];

        foreach ($niveaux as $niveau) {
            Niveau::create($niveau);
        }
    }
}
