<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        return \App\Models\User::with('role')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apellido' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'estado' => 'required|string|in:activo,inactivo',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        $user = \App\Models\User::create($validated);

        return response()->json($user, 201);
    }

    public function show(\App\Models\User $user)
    {
        return $user->load('role');
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $validated = $request->validate([
            'apellido' => 'sometimes|string|max:255',
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id',
            'estado' => 'sometimes|string|in:activo,inactivo',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(\App\Models\User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
