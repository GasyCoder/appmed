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

    }
}
