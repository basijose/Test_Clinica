<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function index(Request $request)
    {
        $query = Intervention::with(['equipment.rubro', 'equipment.location']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('equipment', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhereHas('rubro', function ($q2) use ($search) {
                      $q2->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'fecha_intervencion' => 'required|date',
            'hora_intervencion' => 'required',
            'duracion' => 'required|string',
            'fecha_finalizacion' => 'nullable|date',
            'detalle' => 'required|string',
        ]);

        $intervention = Intervention::create($validated);

        return response()->json($intervention, 201);
    }

    public function show(Intervention $intervention)
    {
        return $intervention->load(['equipment.rubro', 'equipment.location']);
    }

    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'equipment_id' => 'sometimes|exists:equipment,id',
            'fecha_intervencion' => 'sometimes|date',
            'hora_intervencion' => 'sometimes',
            'duracion' => 'sometimes|string',
            'fecha_finalizacion' => 'nullable|date',
            'detalle' => 'sometimes|string',
        ]);

        $intervention->update($validated);

        return response()->json($intervention);
    }

    public function destroy(Intervention $intervention)
    {
        $intervention->delete();
        return response()->json(null, 204);
    }
}
