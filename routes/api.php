<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewPasswordController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [NewPasswordController::class, 'store']);
Route::post('/reset-password', [NewPasswordController::class, 'update']);
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('accesses', AccessController::class);
        Route::apiResource('incidents', \App\Http\Controllers\Admin\IncidentController::class);
    });

    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
