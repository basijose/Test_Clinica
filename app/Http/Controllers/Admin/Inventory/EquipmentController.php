<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with(['rubro.category', 'location']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhere('attributes', 'like', "%{$search}%") // Basic JSON search
                  ->orWhereHas('rubro', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('rubro_id')) {
            $query->where('rubro_id', $request->rubro_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rubro_id' => 'required|exists:rubros,id',
            'location_id' => 'nullable|exists:locations,id',
            'nombre' => 'required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'estado' => 'required|in:operativo,reparacion,baja',
            'attributes' => 'nullable|array',
        ]);

        $equipment = Equipment::create($validated);

        return response()->json($equipment, 201);
    }

    public function show(Equipment $equipment)
    {
        return $equipment->load(['rubro.category', 'rubro.fields', 'location']);
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'rubro_id' => 'required|exists:rubros,id',
            'location_id' => 'nullable|exists:locations,id',
            'nombre' => 'required|string|max:255',
            'serial' => 'nullable|string|max:255',
            'estado' => 'required|in:operativo,reparacion,baja',
            'attributes' => 'nullable|array',
        ]);

        $equipment->update($validated);

        return response()->json($equipment);
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return response()->json(null, 204);
    }
}
