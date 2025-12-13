<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return \App\Models\Role::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_corto' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|string|in:activo,inactivo',
            'accesses' => 'array',
            'accesses.*' => 'exists:accesses,id',
        ]);

        $role = \App\Models\Role::create($validated);

        if (isset($validated['accesses'])) {
            $role->accesses()->sync($validated['accesses']);
        }

        return response()->json($role->load('accesses'), 201);
    }

    public function show(\App\Models\Role $role)
    {
        return $role->load('accesses');
    }

    public function update(Request $request, \App\Models\Role $role)
    {
        $validated = $request->validate([
            'nombre_corto' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'sometimes|string|in:activo,inactivo',
            'accesses' => 'array',
            'accesses.*' => 'exists:accesses,id',
        ]);

        $role->update($validated);

        if (isset($validated['accesses'])) {
            $role->accesses()->sync($validated['accesses']);
        }

        return response()->json($role->load('accesses'));
    }

    public function destroy(\App\Models\Role $role)
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
