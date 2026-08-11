<?php
// database/seeders/UserSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mot de passe de test pour les 2 comptes : password123
        $password = bcrypt('password123');

        $president = User::create([
            'name' => 'Président AEERO',
            'email' => 'president@aeero.test',
            'password' => $password,
            'email_verified_at' => now(),
        ]);
        $president->assignRole('president');

        $secretaire = User::create([
            'name' => 'Secrétaire Général AEERO',
            'email' => 'secretaire@aeero.test',
            'password' => $password,
            'email_verified_at' => now(),
        ]);
        $secretaire->assignRole('secretaire_general');
    }
}
