<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'apellido' => 'Admin',
            'nombre' => 'Sistema',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role_id' => 1,
            'estado' => 'activo',
        ]);
    }
}
