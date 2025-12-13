<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Access::create(['destino' => '/dashboard', 'tipo' => 'menu', 'descripcion' => 'Dashboard', 'icono' => 'home', 'orden' => 1]);
        \App\Models\Access::create(['destino' => '/users', 'tipo' => 'menu', 'descripcion' => 'Usuarios', 'icono' => 'users', 'orden' => 2]);
        \App\Models\Access::create(['destino' => '/roles', 'tipo' => 'menu', 'descripcion' => 'Roles', 'icono' => 'shield', 'orden' => 3]);
        \App\Models\Access::create(['destino' => '/accesses', 'tipo' => 'menu', 'descripcion' => 'Accesos', 'icono' => 'lock', 'orden' => 4]);
    }
}
