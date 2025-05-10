<?php
// database/seeders/AuthorizedEmailSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthorizedEmail;
use Illuminate\Support\Str;

class AuthorizedEmailSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            // Enseignants de la Faculté de Médecine
            'francineravaotiana@gmail.com',
            'lanjamanarivo@gmail.com',
            'ismaelarazafy@gmail.com',
            'anjaramed@gmail.com',
            'anna.razafimamonjy@gmail.com',
            'ianjarasoatoto@gmail.com',
            'razafiarimananahonore9@gmail.com',
            'lalanaval@gmail.com',
            'tanjona87@yahoo.fr',
            'dr.herijosy@gmail.com',
            'heliarisoafanja@gmail.com',
            'jolliottelysee@gmail.com',
            'fanilonikristy@gmail.com',
            'zafymioga@gmail.com',
            'norohasinaanjarasoa@gmail.com',
            'soamiarina25@gmail.com',
            'emmavavy50@gmail.com',
            'andriamiaranatobimalalatiana@gmail.com',
            'vita.frosin@yahoo.fr',
            'mrasoanjara@gmail.com',
            'Sitrakerymaxillo@gmail.com',
            't.trefinjaraimran@gmail.com',
            'nantenainjanaharyinous@yahoo.com',
            'rberakoetxea@gmail.com',
            'herisoathierrysolofondraibe@gmail.com',
            'andrinambininazo@gmail.com',
            'yves.michael28@gmail.com',
            'herrilich.1314@gmail.com',
            'sitrakaulrich@gmail.com',
            'rnpfabien@gmail.com',
            'tokiniainarf@gmail.com',
            'justrataba@gmail.com',
            'rovatiana2318@gmail.com',
            'zarabe.francesca@yahoo.fr',
            'juliotrinah@gmail.com',
            'beasjohnn@gmail.com',
            'niarison.howard@gmail.com',
            'annierzou@gmail.com'
        ];

        foreach ($emails as $email) {
            AuthorizedEmail::create([
                'email' => strtolower($email), // Conversion en minuscules pour uniformité
                'is_registered' => false,
                'verification_token' => null,
                'token_expires_at' => null
            ]);
        }

        $this->command->info(count($emails) . ' adresses email autorisées ont été ajoutées avec succès.');
    }
}
