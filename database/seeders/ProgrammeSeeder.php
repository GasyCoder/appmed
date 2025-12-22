<?php

namespace Database\Seeders;

use App\Models\Programme;
use Illuminate\Database\Seeder;

class ProgrammeSeeder extends Seeder
{
    public function run(): void
    {
        // 4ème année - Semestre 1 (S1)
        $this->createUE1();
        $this->createUE2();
        $this->createUE3();
        $this->createUE4();

        // 4ème année - Semestre 2 (S2)
        $this->createUE5();
        $this->createUE6();
        $this->createUE7();
        $this->createUE8();
        $this->createUE9();
        $this->createUE10();

        // 5ème année - Semestre 3 (S3)
        $this->createUE11();
        $this->createUE12();
        $this->createUE13();
        $this->createUE14();
        $this->createUE15();
        $this->createUE16();
        $this->createUE17();

        // 5ème année - Semestre 4 (S4)
        $this->createUE18();
    }

    // ============================================================================
    // SEMESTRE 1 - 4ème ANNÉE
    // ============================================================================

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
            'credits' => 6,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à la santé publique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Prévention en santé', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Socio-anthropologie', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 8,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Variables et organisation des données', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Mesures en statistique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Description des données', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC4', 'name' => 'Représentation d\'une distribution', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 14,
            'coefficient' => 3,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Définition et concept en épidémiologie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Mesures en épidémiologie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Type d\'étude en épidémiologie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC4', 'name' => 'Validité et biais en épidémiologie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC5', 'name' => 'Standardisation en épidémiologie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC6', 'name' => 'Évaluation d\'un test diagnostique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC7', 'name' => 'Epidémiologie des maladies infectieuses', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 6,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à la recherche clinique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Introduction à l\'éthique et bonne pratique clinique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Base méthodologique des essais cliniques', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
            ]);
        }
    }

    // ============================================================================
    // SEMESTRE 2 - 4ème ANNÉE
    // ============================================================================

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
            'credits' => 6,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Introduction à l\'économie de la santé', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Introduction à la démographie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Introduction à la « One Health »', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Recherche bibliographique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Lecture critique d\'articles scientifiques', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Estimation', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Tests statistiques', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Utilisation du logiciel libre épi-info dans la recherche en santé', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Utilisation du logiciel dans la recherche en santé R, Stata', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 8,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Généralités sur l\'élaboration d\'un outil de recueil', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Recueil de données', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Initiation à Googleform / Kobo Collect®', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC4', 'name' => 'Le questionnaire', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
            ]);
        }
    }

    private function createUE10()
    {
        $ue = Programme::create([
            'type' => 'UE',
            'code' => 'UE10',
            'name' => 'Méthodologie de recherche',
            'order' => 10,
            'semestre_id' => 2,
            'niveau_id' => 1,
            'parcour_id' => 1,
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Rédaction de protocole de recherche', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Introduction à la méthode qualitative', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
            ]);
        }
    }

    // ============================================================================
    // SEMESTRE 3 - 5ème ANNÉE
    // ============================================================================

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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Gestion axée sur les résultats', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Planification sanitaire', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Système d\'information sanitaire', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Système de surveillance épidémiologique', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Correspondances administratives', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Anglais', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Communication en santé publique en situation d\'urgence', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Gestion des situations d\'urgence de santé publique', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Collectes de données en recherche qualitative', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Analyse de données en recherche qualitative', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 12,
            'coefficient' => 3,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Principes de base d\'analyse statistique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Régression logistique binomiale', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC3', 'name' => 'Régression linéaire', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC4', 'name' => 'Analyse de survie', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC5', 'name' => 'Analyse des séries temporelles', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC6', 'name' => 'Analyse géospatiale des données de santé', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
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
            'credits' => 4,
            'coefficient' => 2,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Méta-analyse et Revue Systématique', 'credits' => 2, 'coefficient' => 1],
            ['code' => 'EC2', 'name' => 'Rédaction et publication d\'articles scientifiques', 'credits' => 2, 'coefficient' => 1],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
            ]);
        }
    }

    // ============================================================================
    // SEMESTRE 4 - 5ème ANNÉE
    // ============================================================================

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
            'credits' => 30,
            'coefficient' => 5,
            'status' => true,
        ]);

        $ecs = [
            ['code' => 'EC1', 'name' => 'Stages de terrain/Rédaction du mémoire', 'credits' => 20, 'coefficient' => 3],
            ['code' => 'EC2', 'name' => 'Soutenance de mémoire', 'credits' => 10, 'coefficient' => 2],
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
                'credits' => $ec['credits'],
                'coefficient' => $ec['coefficient'],
                'status' => true,
            ]);
        }
    }
}