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
                'sigle' => 'MG',
                'name' => 'Médecine Générale',
                'status' => true,
            ],
            [
                'sigle' => 'MS',
                'name' => 'Médecine Spécialisée',
                'status' => true,
            ]
        ];

        foreach ($parcours as $parcour) {
            Parcour::create($parcour);
        }
    }
}
