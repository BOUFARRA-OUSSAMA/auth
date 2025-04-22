<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Services\AuthService;
use App\Application\Services\UserService;
use App\Application\Services\RoleService;  // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    private AuthService $authService;
    private UserService $userService;
    private RoleService $roleService;  // Add this line

    public function __construct(AuthService $authService, UserService $userService, RoleService $roleService) // Add RoleService
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->roleService = $roleService; // Add this line
        // PRESERVED: Your original middleware declaration
        $this->middleware('jwt.auth', ['except' => ['login', 'register', 'refresh']]);
    }

    /**
     * User login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $result = $this->authService->login($request->email, $request->password);
            return $this->successResponse(['token' => $result['token']], $result['message']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $result = $this->authService->register(
                $request->name,
                $request->email,
                $request->password,
                $request->phone ?? null
            );

            return $this->successResponse($result['user'], $result['message'], 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * User logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse(null, 'User logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Refresh token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::parseToken()->refresh();
            return $this->successResponse(['token' => $token], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }

    /**
     * Get authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            // Create a DTO directly as you did before
            $userDTO = new \App\Application\DTOs\UserDTO(
                $user->name,
                $user->email,
                $user->phone ?? null,
                $user->status ?? 'active',
                $user->id
            );

            // Get user data
            $userData = $userDTO->toArray();

            // ADD ROLES & PERMISSIONS: Enhance with role information
            try {
                $roles = $this->roleService->getRolesByUserId($user->id);
                $userData['roles'] = array_map(function ($role) {
                    return [
                        'id' => $role->getId(),
                        'name' => $role->getName(),
                        'code' => $role->getCode(),
                        'description' => $role->getDescription(),
                        'permissions' => array_map(function ($permission) {
                            return [
                                'id' => $permission->getId(),
                                'name' => $permission->getName(),
                                'code' => $permission->getCode(),
                                'group' => $permission->getGroup(),
                                'description' => $permission->getDescription()
                            ];
                        }, $role->getPermissions())
                    ];
                }, $roles);
            } catch (\Exception $e) {
                // If roles can't be fetched, continue without them
                // This maintains backward compatibility
                $userData['roles'] = [];
            }

            return $this->successResponse($userData);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 401);
        }
    }
}
