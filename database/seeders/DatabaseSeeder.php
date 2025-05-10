<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            NiveauSeeder::class,        // Les niveaux doivent être créés avant
            ParcoursSeeder::class,      // Les parcours doivent être créés avant
            SemestreSeeder::class,
            UserWithProfileSeeder::class, // Les utilisateurs en dernier car ils dépendent des autres tables
            ProgrammeSeeder::class,
            AuthorizedEmailSeeder::class,
        ]);

    }
}
