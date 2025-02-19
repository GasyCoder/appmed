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
            'student1@univ-thies.sn',
            'student2@univ-thies.sn',
            'student3@univ-thies.sn',
            'student4@univ-thies.sn'
        ];

        foreach ($emails as $email) {
            AuthorizedEmail::create([
                'email' => $email,
                'is_registered' => false,
                'verification_token' => null,
                'token_expires_at' => null
            ]);
        }
    }
}
