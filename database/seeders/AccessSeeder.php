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
        \App\Models\Access::updateOrCreate(['destino' => '/dashboard'], ['tipo' => 'menu', 'descripcion' => 'Dashboard', 'icono' => 'fa-solid fa-house', 'orden' => 1]);
        \App\Models\Access::updateOrCreate(['destino' => '/users'], ['tipo' => 'menu', 'descripcion' => 'Usuarios', 'icono' => 'fa-solid fa-users', 'orden' => 2]);
        \App\Models\Access::updateOrCreate(['destino' => '/roles'], ['tipo' => 'menu', 'descripcion' => 'Roles', 'icono' => 'fa-solid fa-shield-halved', 'orden' => 3]);
        \App\Models\Access::updateOrCreate(['destino' => '/accesses'], ['tipo' => 'menu', 'descripcion' => 'Accesos', 'icono' => 'fa-solid fa-key', 'orden' => 4]);
        \App\Models\Access::updateOrCreate(['destino' => '/inventory'], ['tipo' => 'menu', 'descripcion' => 'Inventario', 'icono' => 'fa-solid fa-boxes-stacked', 'orden' => 5]);
    }
}
