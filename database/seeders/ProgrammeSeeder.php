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
        $this->createUE9();
        $this->createUE10();

        // Troisième regroupement (Semestre 3)
        $this->createUE11();
        $this->createUE12();
        $this->createUE13();
        $this->createUE14();
        $this->createUE15();
        $this->createUE16();
        $this->createUE17();

        // Quatrième regroupement (Semestre 4)
        $this->createUE18();
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
            'name' => 'Concepts en santé publique 2',
            'order' => 5,
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

    private function createUE6()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE6',
            'name' => 'Littératie en santé publique',
            'order' => 6,
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

    private function createUE7()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE7',
            'name' => 'Statistique inférentielle',
            'order' => 7,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Estimation'],
            ['code' => 'EC2', 'name' => 'Tests statistiques'],
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
            'name' => 'Bases informatiques pour le traitement des données',
            'order' => 8,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Utilisation du logiciel libre épi-info dans la recherche en santé'],
            ['code' => 'EC2', 'name' => 'Utilisation du logiciel dans la recherche en santé R, Stata'],
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

    private function createUE9()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE9',
            'name' => 'Technique d\'enquête en épidémiologie',
            'order' => 9,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Généralités sur l\'élaboration d\'un outil de recueil'],
            ['code' => 'EC2', 'name' => 'Recueil de données'],
            ['code' => 'EC3', 'name' => 'Initiation à Googleform / Kobo Collect®'],
            ['code' => 'EC4', 'name' => 'Le questionnaire'],
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

    private function createUE10()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'U10',
            'name' => 'Méthodologie de recherche',
            'order' => 10,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Rédaction de protocole de recherche'],
            ['code' => 'EC2', 'name' => 'Introduction à la méthode qualitative'],
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

    private function createUE11()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE11',
            'name' => 'Administration du système de santé',
            'order' => 11,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Gestion axée sur les résultats'],
            ['code' => 'EC2', 'name' => 'Planification sanitaire'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE12()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE12',
            'name' => 'Information sanitaire',
            'order' => 12,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Système d\'information sanitaire'],
            ['code' => 'EC2', 'name' => 'Système de surveillance épidémiologique'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE13()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE13',
            'name' => 'Communication',
            'order' => 13,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Correspondances administratives'],
            ['code' => 'EC2', 'name' => 'Anglais'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE14()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE14',
            'name' => 'Santé publique en situation d\'urgence',
            'order' => 14,
            'semestre_id' => 3,
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
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE15()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE15',
            'name' => 'Méthodes de recherche qualitative',
            'order' => 15,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Collectes de données en recherche qualitative'],
            ['code' => 'EC2', 'name' => 'Analyse de données en recherche qualitative'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE16()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE16',
            'name' => 'Analyse des données de santé',
            'order' => 16,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Principes de base d\'analyse statistique'],
            ['code' => 'EC2', 'name' => 'Régression logistique binomiale'],
            ['code' => 'EC3', 'name' => 'Régression linéaire'],
            ['code' => 'EC4', 'name' => 'Analyse de survie'],
            ['code' => 'EC5', 'name' => 'Analyse des séries temporelles'],
            ['code' => 'EC6', 'name' => 'Analyse géospatiale des données de santé'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE17()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE17',
            'name' => 'Synthèse et diffusion des résultats de recherche',
            'order' => 17,
            'semestre_id' => 3,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Méta-analyse et Revue Systématique'],
            ['code' => 'EC2', 'name' => 'Rédaction et publication d\'articles scientifiques'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 3,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }

    private function createUE18()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE18',
            'name' => 'Rédaction et soutenance du mémoire',
            'order' => 18,
            'semestre_id' => 4,
            'niveau_id' => 1,
            'parcour_id' => 1,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Stages de terrain/Rédaction du mémoire'],
            ['code' => 'EC2', 'name' => 'Soutenance de mémoire'],
        ];

        foreach ($ecs as $index => $ec) {
            Programme::create([
                'type' => 'EC',
                'code' => $ec['code'],
                'name' => $ec['name'],
                'order' => $index + 1,
                'parent_id' => $ue->id,
                'semestre_id' => 4,
                'niveau_id' => 1,
                'parcour_id' => 1,
            ]);
        }
    }
}
