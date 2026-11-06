<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeederUsuariosPorRol extends Seeder
{
    /**
     * Crear una cuenta de prueba por rol. Requiere los roles ya cargados.
     * Contraseña inicial para todas las cuentas: InsureTech2026!
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $usuarios = [
            ['CEO', 'ceo@example.com'],
            ['Coordinaciones', 'coordinaciones@example.com'],
            ['Hojas de ruta', 'hojas-de-ruta@example.com'],
            ['Inspecciones', 'inspecciones@example.com'],
            ['Inspectores', 'inspectores@example.com'],
            ['Supervisor técnico', 'supervisor-tecnico@example.com'],
            ['Terceros', 'terceros@example.com'],
        ];

        DB::transaction(function () use ($usuarios) {
            foreach ($usuarios as [$nombreRol, $email]) {
                $rol = Role::findByName($nombreRol, 'web');

                $usuario = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nombreRol,
                        'password' => Hash::make('InsureTech2026!'),
                    ]
                );

                $usuario->assignRole($rol);
            }
        });
    }
}
