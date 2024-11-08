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
                'full_name' => 'Admin',
                'identification' => '1234567890',
                'phone' => '123456789',
                'email' => 'danielpcpx@hotmail.com',
                'city' => 'Bogotá',
                'password' => bcrypt('Lomejor12*'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Cliente 1',
                'identification' => '9876543210',
                'phone' => '987654321',
                'email' => 'cliente1@example.com',
                'city' => 'Medellín',
                'password' => bcrypt('contraseña1'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Cliente 2',
                'identification' => '4567891230',
                'phone' => '456789123',
                'email' => 'cliente2@example.com',
                'city' => 'Cali',
                'password' => bcrypt('contraseña2'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Cliente 3',
                'identification' => '3216549870',
                'phone' => '321654987',
                'email' => 'cliente3@example.com',
                'city' => 'Cartagena',
                'password' => bcrypt('contraseña3'),
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
