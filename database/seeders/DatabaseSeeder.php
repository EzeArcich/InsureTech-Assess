<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()

    
    {
        // \App\Models\User::factory(10)->create();

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'CEO', 'guard_name' => 'web', 'created_at' => '2022-05-08 18:50:23', 'updated_at' => '2022-05-18 09:02:05'],
            ['id' => 5, 'name' => 'Coordinaciones', 'guard_name' => 'web', 'created_at' => '2022-05-08 18:50:23', 'updated_at' => '2022-05-18 09:02:05'],
            ['id' => 2, 'name' => 'Hojas de ruta', 'guard_name' => 'web', 'created_at' => '2022-05-08 18:50:31', 'updated_at' => '2022-05-25 23:28:21'],
            ['id' => 3, 'name' => 'Inspecciones', 'guard_name' => 'web', 'created_at' => '2022-05-18 09:01:28', 'updated_at' => '2022-05-18 09:01:59'],
            ['id' => 4, 'name' => 'Inspectores', 'guard_name' => 'web', 'created_at' => '2022-05-18 09:01:50', 'updated_at' => '2022-05-18 09:01:50'],
            ['id' => 6, 'name' => 'Supervisor técnico', 'guard_name' => 'web', 'created_at' => '2022-05-25 23:40:07', 'updated_at' => '2022-05-25 23:40:07'],
            ['id' => 7, 'name' => 'Terceros', 'guard_name' => 'web', 'created_at' => '2022-06-27 19:25:02', 'updated_at' => '2022-06-27 19:25:02'],
        ]);

        // $this->call(SeederTablaPermisos::class);

        DB::table('permissions')->insert([
            ['name' => 'ver-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'crear-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editar-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'borrar-rol', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        
            // Operaciones sobre tabla blogs
            ['name' => 'ver-blog', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'crear-blog', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editar-blog', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'borrar-blog', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        
            // Operaciones sobre tabla usuarios
            ['name' => 'ver-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'crear-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editar-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'borrar-usuario', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        
            // Operaciones sobre tabla siniestros
            ['name' => 'ver-siniestro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'crear-siniestro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editar-siniestro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'borrar-siniestro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'derivar-siniestro', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        
        

        // Insertar usuario con rol
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Super admin',
            'email' => 'admina@gmail.com',
            'telefono' => null,
            'email_verified_at' => null,
            'password' => '$2y$10$wEtEjvw8AQ8mHM.n5KHfu.PlctXcuG7ro8sVO4xJkO6scNkLfZeki',
            'remember_token' => null,
            'created_at' => '2022-05-08 14:38:48',
            'updated_at' => '2022-05-11 11:20:59',
        ]);

        // Asignar rol al usuario
        DB::table('model_has_roles')->insert([
            'role_id' => 1, // 'Super admin' tiene el rol 1
            'model_type' => 'App\\Models\\User',
            'model_id' => 1, // El usuario con id 1
        ]);

        

    }
}
