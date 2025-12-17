<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Rubro;
use App\Models\Equipment;
use App\Models\Location;

class InventoryDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure a location exists
        $location = Location::firstOrCreate(
            ['nombre' => 'Almacén Central'],
            ['descripcion' => 'Ubicación principal de almacenamiento']
        );

        $categories = [
            'Equipamiento Médico' => [
                'Monitores Multiparamétricos',
                'Respiradores Artificiales',
                'Bombas de Infusión',
                'Camillas de Traslado'
            ],
            'Mobiliario de Oficina' => [
                'Escritorios Ejecutivos',
                'Sillas Ergonómicas',
                'Archivadores Metálicos',
                'Mesas de Reunión'
            ],
            'Informática' => [
                'Computadoras de Escritorio',
                'Impresoras Láser',
                'Monitores LED',
                'Laptops Corporativas'
            ]
        ];

        foreach ($categories as $catName => $rubros) {
            $category = Category::create([
                'nombre' => $catName,
                'descripcion' => 'Categoría de prueba para ' . $catName
            ]);

            foreach ($rubros as $rubroName) {
                $rubro = Rubro::create([
                    'category_id' => $category->id,
                    'nombre' => $rubroName,
                    'descripcion' => 'Rubro de ' . $rubroName
                ]);

                // Create 5 equipment for each rubro
                for ($i = 1; $i <= 5; $i++) {
                    Equipment::create([
                        'rubro_id' => $rubro->id,
                        'location_id' => $location->id,
                        'nombre' => $rubroName . ' - Unidad ' . $i,
                        'serial' => strtoupper(substr(md5(uniqid()), 0, 10)),
                        'estado' => 'operativo',
                        'attributes' => json_encode(['marca' => 'Genérica', 'modelo' => 'Modelo ' . $i])
                    ]);
                }
            }
        }
    }
}
