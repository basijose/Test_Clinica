<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user();
        
        if ($user && $user->role) {
            $role = $user->role;
        } else {
            $role = \App\Models\Role::where('nombre_corto', 'Invitado')->first();
        }

        if (!$role) {
            return response()->json([]);
        }

        $accesses = $role->accesses()
            ->where('estado', 'activo')
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('roles.id', $role->id);
                })->where('estado', 'activo')->orderBy('orden');
            }])
            ->orderBy('orden')
            ->get();

        return response()->json($accesses);
    }
}
