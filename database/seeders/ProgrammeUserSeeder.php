<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Programme;
use Illuminate\Database\Seeder;

class ProgrammeUserSeeder extends Seeder
{
    public function run(): void
    {
        // Récupère l'enseignant créé dans UserWithProfileSeeder
        $teacher = User::role('teacher')->first();

        if (!$teacher) {
            $this->command->warn('⚠️  Aucun enseignant trouvé. Exécutez d\'abord UserWithProfileSeeder.');
            return;
        }

        $this->command->info('👨‍🏫 Assignation de l\'enseignant aux programmes...');

        // Assigner l'enseignant à quelques ECs comme exemple
        // UE1 - Concepts en santé publique 1
        $ue1 = Programme::where('code', 'UE1')->first();
        if ($ue1) {
            $ecsUE1 = Programme::where('type', 'EC')
                ->where('parent_id', $ue1->id)
                ->get();

            foreach ($ecsUE1 as $index => $ec) {
                $teacher->programmes()->attach($ec->id, [
                    'heures_cm' => 10,
                    'heures_td' => 15,
                    'heures_tp' => 5,
                    'is_responsable' => $index === 0, // Le premier EC est responsable
                    'note' => $index === 0 ? 'Enseignant responsable de cette matière' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // UE2 - Statistique descriptive
        $ue2 = Programme::where('code', 'UE2')->first();
        if ($ue2) {
            $ecsUE2 = Programme::where('type', 'EC')
                ->where('parent_id', $ue2->id)
                ->limit(2) // Seulement 2 ECs pour varier
                ->get();

            foreach ($ecsUE2 as $index => $ec) {
                $teacher->programmes()->attach($ec->id, [
                    'heures_cm' => 12,
                    'heures_td' => 18,
                    'heures_tp' => 0,
                    'is_responsable' => $index === 0,
                    'note' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Enseignants assignés aux programmes avec succès !');
    }
}