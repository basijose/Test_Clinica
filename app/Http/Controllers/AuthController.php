<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(\Illuminate\Http\Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'user' => \Illuminate\Support\Facades\Auth::user()->load('role'),
                'message' => 'Login successful'
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        // Invalidate the session regardless of auth state
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logout successful']);
    }
}
