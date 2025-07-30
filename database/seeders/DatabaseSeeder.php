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
            NiveauSeeder::class,     
            ParcoursSeeder::class,      
            SemestreSeeder::class,
            UserWithProfileSeeder::class, 
            ProgrammeSeeder::class,
            AuthorizedEmailSeeder::class,
        ]);

    }
}
