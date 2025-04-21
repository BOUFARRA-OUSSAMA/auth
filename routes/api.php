<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\EntityTypeController;
use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AttributeValueController;
use Tymon\JWTAuth\Facades\JWTAuth;

// Health check
Route::get('test', fn() => response()->json(['message' => 'API is working']));

// Public auth endpoints
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login',    [AuthController::class, 'login'])->    name('auth.login');
});

// Protected JWT endpoints
Route::middleware('jwt.auth')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me',       [AuthController::class, 'me'])->      name('auth.me');
        Route::post('refresh', [AuthController::class, 'refresh'])-> name('auth.refresh');
        Route::post('logout',  [AuthController::class, 'logout'])->  name('auth.logout');
    });

    // User & Role resources
    Route::apiResource('users', ApiUserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('users/{user}/roles', [ApiUserController::class, 'roles'])-> name('users.roles');
    Route::get('roles/{role}/users', [RoleController::class,      'users'])-> name('roles.users');

    // EAV system
    Route::apiResource('entity-types',    EntityTypeController::class);
    Route::apiResource('attributes',      AttributeController::class);
    Route::apiResource('attribute-values',AttributeValueController::class);

    // EAV relationships & batch
    Route::get('entity-types/{entityType}/attributes', [EntityTypeController::class, 'attributes'])
         ->name('entity-types.attributes');
    Route::get('attributes/{attribute}/values', [AttributeController::class, 'values'])
         ->name('attributes.values');
    Route::post('attribute-values/batch', [AttributeValueController::class,'batchUpdate'])
         ->name('attribute-values.batch-update');
});

// 404 fallback
Route::fallback(fn() => response()->json(['error' => 'Not Found'], 404))
     ->name('api.fallback');


Route::get('token/debug', function (Request $request) {
    try {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json([
                'error' => 'Token not provided',
                'headers' => $request->headers->all(),
                'auth_header' => $request->header('Authorization')
            ], 400);
        }

        $payload = JWTAuth::getPayload($token)->toArray();
        return response()->json([
            'valid' => true,
            'expired' => false,
            'payload' => $payload,
            'expires_at' => date('Y-m-d H:i:s', $payload['exp'])
        ]);
    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['valid' => false, 'error' => 'Token has expired'], 401);
    } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['valid' => false, 'error' => 'Token is invalid'], 401);
    } catch (\Exception $e) {
        return response()->json([
            'valid' => false,
            'error' => $e->getMessage(),
            'headers' => $request->headers->all(),
            'auth_header' => $request->header('Authorization')
        ], 401);
    }
});