<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\Role::create(['nombre_corto' => 'Admin', 'descripcion' => 'Administrador del sistema']);
        \App\Models\Role::create(['nombre_corto' => 'Usuario', 'descripcion' => 'Usuario estándar']);
        \App\Models\Role::create(['nombre_corto' => 'Invitado', 'descripcion' => 'Usuario invitado']);

        // Assign all accesses to Admin
        $accesses = \App\Models\Access::all();
        $admin->accesses()->attach($accesses);

        // Assign Dashboard access to Invitado
        $dashboardAccess = \App\Models\Access::where('descripcion', 'Dashboard')->first();
        if ($dashboardAccess) {
            $invitado = \App\Models\Role::where('nombre_corto', 'Invitado')->first();
            $invitado->accesses()->attach($dashboardAccess);
        }
    }
}
