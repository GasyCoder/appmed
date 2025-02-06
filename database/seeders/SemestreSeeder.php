<?php
// database/seeders/SemestreSeeder.php

namespace Database\Seeders;

use App\Models\Niveau;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    public function run(): void
    {
        // Structure des semestres par niveau
        $semestreStructure = [
            'M1' => ['S1', 'S2'],
            'M2' => ['S3', 'S4']
        ];

        foreach ($semestreStructure as $sigle => $semestres) {
            $niveau = Niveau::where('sigle', $sigle)->first();

            if ($niveau) {
                foreach ($semestres as $semestre) {
                    Semestre::create([
                        'name' => $semestre,
                        'niveau_id' => $niveau->id,
                        'is_active' => false,
                        'status' => true,
                    ]);
                }
            }
        }
    }
}
