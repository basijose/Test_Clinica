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
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index']);
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update']);
        Route::post('/settings/test-email', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail']);
        
        // Inventory Routes
        Route::apiResource('inventory/categories', \App\Http\Controllers\Admin\Inventory\CategoryController::class);
        Route::apiResource('inventory/locations', \App\Http\Controllers\Admin\Inventory\LocationController::class);
        Route::apiResource('inventory/rubros', \App\Http\Controllers\Admin\Inventory\RubroController::class);
        Route::apiResource('inventory/equipment', \App\Http\Controllers\Admin\Inventory\EquipmentController::class);

        // Interventions
        Route::apiResource('interventions', \App\Http\Controllers\Admin\InterventionController::class);
    });

    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
