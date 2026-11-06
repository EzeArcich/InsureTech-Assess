<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdministrativeRoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('Administrativo', 'web');
    }
}
