<?php

namespace Database\Seeders;

use App\Models\Programme;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProgrammeSeeder extends Seeder
{
    public function run(): void
    {
        // Premier regroupement (Semestre 1)
        $this->createUE1();
        $this->createUE2();
        $this->createUE3();
        $this->createUE4();

        // Deuxième regroupement (Semestre 2)
        $this->createUE5();
        $this->createUE6();
        $this->createUE7();
        $this->createUE8();
    }

    private function createUE1()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE1',
            'name' => 'Concepts en santé publique 1',
            'order' => 1,
            'semestre_id' => 1,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à la santé publique'],
            ['code' => 'EC2', 'name' => 'Prévention en santé'],
            ['code' => 'EC3', 'name' => 'Socio-anthropologie'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 1,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE2()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE2',
            'name' => 'Statistique descriptive',
            'order' => 2,
            'semestre_id' => 1,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Variables et organisation des données'],
            ['code' => 'EC2', 'name' => 'Mesures en statistique'],
            ['code' => 'EC3', 'name' => 'Description des données'],
            ['code' => 'EC4', 'name' => 'Représentation d\'une distribution'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 1,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE3()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE3',
            'name' => 'Épidémiologie descriptive et analytique',
            'order' => 3,
            'semestre_id' => 1,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Définition et concept en épidémiologie'],
            ['code' => 'EC2', 'name' => 'Mesures en épidémiologie'],
            ['code' => 'EC3', 'name' => 'Type d\'étude en épidémiologie'],
            ['code' => 'EC4', 'name' => 'Validité et biais en épidémiologie'],
            ['code' => 'EC5', 'name' => 'Standardisation en épidémiologie'],
            ['code' => 'EC6', 'name' => 'Évaluation d\'un test diagnostique'],
            ['code' => 'EC7', 'name' => 'Epidémiologie des maladies infectieuses'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 1,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE4()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE4',
            'name' => 'Base fondamentale de la recherche clinique',
            'order' => 4,
            'semestre_id' => 1,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à la recherche clinique'],
            ['code' => 'EC2', 'name' => 'Introduction à l\'éthique et bonne pratique clinique'],
            ['code' => 'EC3', 'name' => 'Base méthodologique des essais cliniques'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 1,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE5()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE5',
            'name' => 'Santé publique en situation d\'urgence',
            'order' => 5,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Communication en santé publique en situation d\'urgence'],
            ['code' => 'EC2', 'name' => 'Gestion des situations d\'urgence de santé publique'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 2,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE6()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE6',
            'name' => 'Concepts en santé publique 2',
            'order' => 6,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à l\'économie de la santé'],
            ['code' => 'EC2', 'name' => 'Introduction à la démographie'],
            ['code' => 'EC3', 'name' => 'Introduction à la « One Health »'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 2,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE7()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE7',
            'name' => 'Littératie en santé publique',
            'order' => 7,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Recherche bibliographique'],
            ['code' => 'EC3', 'name' => 'Lecture critique d\'articles scientifiques'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 2,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE8()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE8',
            'name' => 'Statistique inférentielle',
            'order' => 8,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        Programme::create([
            'type' => 'EC',
            'code' => 'EC1',
            'name' => 'Estimation',
            'order' => 1,
            'parent_id' => $ue->id,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);
    }
}
