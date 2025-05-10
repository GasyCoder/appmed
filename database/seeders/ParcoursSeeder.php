<?php

namespace Database\Seeders;

use App\Models\Parcour;
use Illuminate\Database\Seeder;

class ParcoursSeeder extends Seeder
{
    public function run(): void
    {
        $parcours = [
            [
                'sigle' => 'EPI R.C',
                'name' => 'Epidémiologie et Recherche Clinique',
                'status' => true,
            ],
        ];

        foreach ($parcours as $parcour) {
            Parcour::create($parcour);
        }
    }
}
