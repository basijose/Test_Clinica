<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('all')) {
            return Location::where('estado', 'activo')->get();
        }

        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        return Location::orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $location = Location::create($validated);

        return response()->json($location, 201);
    }

    public function show(Location $location)
    {
        return $location;
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $location->update($validated);

        return response()->json($location);
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return response()->json(null, 204);
    }
}
