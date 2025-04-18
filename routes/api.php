<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController as ApiUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// IMPORTANT: Keep existing login/register routes with current controller
// to maintain backward compatibility and ensure login/register keeps working
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

// New auth routes under a different prefix
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
});

// Protected routes for resource management
Route::middleware('auth:api')->group(function () {
    // API resources
    Route::apiResource('users', ApiUserController::class);
    Route::apiResource('roles', RoleController::class);

    // Additional user-role relationships
    Route::get('roles/{role}/users', [RoleController::class, 'users']);
    Route::get('users/{user}/roles', [ApiUserController::class, 'roles']);
});

// Keep this route for backwards compatibility
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
