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
        // Root Items
        \App\Models\Access::updateOrCreate(['destino' => '/dashboard'], ['tipo' => 'menu', 'descripcion' => 'Dashboard', 'icono' => 'fa-solid fa-house', 'orden' => 1, 'parent_id' => null]);
        \App\Models\Access::updateOrCreate(['destino' => '/inventory'], ['tipo' => 'menu', 'descripcion' => 'Inventario', 'icono' => 'fa-solid fa-boxes-stacked', 'orden' => 2, 'parent_id' => null]);
        
        // Configuración Parent
        $config = \App\Models\Access::updateOrCreate(['destino' => '#'], ['tipo' => 'menu', 'descripcion' => 'Configuración', 'icono' => 'fa-solid fa-gear', 'orden' => 99, 'parent_id' => null]);

        // Configuración Children
        \App\Models\Access::updateOrCreate(['destino' => '/users'], ['tipo' => 'menu', 'descripcion' => 'Usuarios', 'icono' => 'fa-solid fa-users', 'orden' => 1, 'parent_id' => $config->id]);
        \App\Models\Access::updateOrCreate(['destino' => '/roles'], ['tipo' => 'menu', 'descripcion' => 'Roles', 'icono' => 'fa-solid fa-shield-halved', 'orden' => 2, 'parent_id' => $config->id]);
        \App\Models\Access::updateOrCreate(['destino' => '/accesses'], ['tipo' => 'menu', 'descripcion' => 'Accesos', 'icono' => 'fa-solid fa-key', 'orden' => 3, 'parent_id' => $config->id]);
        \App\Models\Access::updateOrCreate(['destino' => '/settings'], ['tipo' => 'menu', 'descripcion' => 'Preferencias', 'icono' => 'fa-solid fa-sliders', 'orden' => 4, 'parent_id' => $config->id]);

        // Assign all accesses to Admin
        $admin = \App\Models\Role::where('nombre_corto', 'Admin')->first();
        if ($admin) {
            $allAccesses = \App\Models\Access::all();
            $admin->accesses()->sync($allAccesses);
        }
    }
}
