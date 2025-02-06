<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\Niveau;
use App\Models\Parcour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserWithProfileSeeder extends Seeder
{
    public function run()
    {
        // Enseignants M1
        $m1Teacher = User::create([
            'name' => 'Dr. Rakoto',
            'email' => 'rakoto@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => true,
        ]);

        $m1Teacher->assignRole('teacher');

        Profil::create([
            'user_id' => $m1Teacher->id,
            'sexe' => 'homme',
            'grade' => 'Docteur',
            'telephone' => '0320000002',
            'adresse' => '45 Rue Rakoto',
            'ville' => 'Antananarivo',
            'departement' => 'Médecine Générale',
        ]);

        $niveauM1 = Niveau::where('sigle', 'M1')->first();
        $parcourMG = Parcour::where('sigle', 'MG')->first();

        if ($niveauM1) {
            $m1Teacher->teacherNiveaux()->attach($niveauM1->id);
        }
        if ($parcourMG) {
            $m1Teacher->teacherParcours()->attach($parcourMG->id);
        }

        // Enseignants M2
        $m2Teacher = User::create([
            'name' => 'Dr. Rabe',
            'email' => 'rabe@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => true,
        ]);

        $m2Teacher->assignRole('teacher');

        Profil::create([
            'user_id' => $m2Teacher->id,
            'sexe' => 'femme',
            'grade' => 'Professeur',
            'telephone' => '0320000003',
            'adresse' => '78 Rue Rabe',
            'ville' => 'Antananarivo',
            'departement' => 'Médecine Spécialisée',
        ]);

        $niveauM2 = Niveau::where('sigle', 'M2')->first();
        $parcourMS = Parcour::where('sigle', 'MS')->first();

        if ($niveauM2) {
            $m2Teacher->teacherNiveaux()->attach($niveauM2->id);
        }
        if ($parcourMS) {
            $m2Teacher->teacherParcours()->attach($parcourMS->id);
        }

        // Étudiants M1
        $studentM1 = User::create([
            'name' => 'Rasoa',
            'email' => 'rasoa@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => true,
            'niveau_id' => $niveauM1?->id,
            'parcour_id' => $parcourMG?->id,
        ]);

        $studentM1->assignRole('student');

        Profil::create([
            'user_id' => $studentM1->id,
            'sexe' => 'femme',
            'telephone' => '0330000001',
            'adresse' => '23 Rue Rasoa',
            'ville' => 'Antananarivo',
            'departement' => 'Étudiant',
        ]);

        // Étudiants M2
        $studentM2 = User::create([
            'name' => 'Randria',
            'email' => 'randria@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'status' => true,
            'niveau_id' => $niveauM2?->id,
            'parcour_id' => $parcourMS?->id,
        ]);

        $studentM2->assignRole('student');

        Profil::create([
            'user_id' => $studentM2->id,
            'sexe' => 'homme',
            'telephone' => '0330000002',
            'adresse' => '56 Rue Randria',
            'ville' => 'Antananarivo',
            'departement' => 'Étudiant',
        ]);
    }
}
