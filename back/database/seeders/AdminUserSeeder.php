<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin@aeero.bj';
    public const ADMIN_PASSWORD = 'password'; // À changer après le premier déploiement

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => 'Responsable Informatique AEERO',
                'password' => Hash::make(self::ADMIN_PASSWORD),
            ]
        );
    }
}
