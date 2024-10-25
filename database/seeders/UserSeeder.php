<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->truncate(); // Elimina todos los usuarios existentes

        $users = [
            [
                'name' => 'Admin',
                'email' => 'danielpcpx@hotmail.com',
                'password' => bcrypt('Lomejor12*'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cliente 1',
                'email' => 'cliente1@example.com',
                'password' => bcrypt('contraseña1'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cliente 2',
                'email' => 'cliente2@example.com',
                'password' => bcrypt('contraseña2'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cliente 3',
                'email' => 'cliente3@example.com',
                'password' => bcrypt('contraseña3'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
