<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création du Super Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'password' => Hash::make('Admin@@123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

    }
}
