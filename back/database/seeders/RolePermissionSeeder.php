<?php

// database/seeders/RolePermissionSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'president', 'guard_name' => 'web']);
        Role::create(['name' => 'secretaire_general', 'guard_name' => 'web']);
    }
}
