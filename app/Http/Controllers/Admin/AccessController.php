<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function index()
    {
        return \App\Models\Access::orderBy('orden')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destino' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'required|integer',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        $access = \App\Models\Access::create($validated);

        return response()->json($access, 201);
    }

    public function show(\App\Models\Access $access)
    {
        return $access;
    }

    public function update(Request $request, \App\Models\Access $access)
    {
        $validated = $request->validate([
            'destino' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string|max:255',
            'icono' => 'nullable|string|max:255',
            'orden' => 'sometimes|integer',
            'estado' => 'sometimes|string|in:activo,inactivo',
        ]);

        $access->update($validated);

        return response()->json($access);
    }

    public function destroy(\App\Models\Access $access)
    {
        $access->delete();

        return response()->json(null, 204);
    }
}
