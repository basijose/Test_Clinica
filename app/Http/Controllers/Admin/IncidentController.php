<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        return Incident::with(['creator', 'assignee'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['estado'] = 'abierto';

        $incident = Incident::create($validated);

        return response()->json($incident, 201);
    }

    public function show(Incident $incident)
    {
        return $incident->load(['creator', 'assignee']);
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'estado' => 'sometimes|in:abierto,en_progreso,resuelto,cerrado',
            'prioridad' => 'sometimes|in:baja,media,alta,critica',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $incident->update($validated);

        return response()->json($incident);
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return response()->json(null, 204);
    }
}
