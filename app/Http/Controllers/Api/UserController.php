<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\DTOs\UserDTO;
use App\Application\Services\UserService;
use App\Application\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class UserController extends Controller
{
    use ApiResponseTrait;

    private UserService $userService;
    private RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [];
        if ($request->has('name')) {
            $filters['name'] = $request->query('name');
        }
        if ($request->has('email')) {
            $filters['email'] = $request->query('email');
        }
        if ($request->has('status')) {
            $filters['status'] = $request->query('status');
        }

        // Get pagination parameters
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 15);

        $result = $this->userService->findUsers($filters, $page, $perPage);

        // If roles need to be included
        if ($request->has('include_roles') && $request->query('include_roles') === 'true') {
            foreach ($result['items'] as &$userDTO) {
                $userId = $userDTO->getId();
                $roles = $this->userService->getUserRoles($userId);
                $userData = $userDTO->toArray();
                $userData['roles'] = $roles;
                $userDTO = $userData;
            }
        }

        return $this->successResponse($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'nullable|string|in:active,pending,suspended',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $userDTO = UserDTO::fromArray([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'status' => $request->status ?? 'pending'
            ]);

            // Create user
            $createdUser = $this->userService->createUser($userDTO, $request->password);

            // Assign roles if provided
            if ($request->has('role_ids') && is_array($request->role_ids)) {
                $this->userService->assignRoles($createdUser->getId(), $request->role_ids);

                // Get updated user with roles
                $userWithRoles = [
                    'user' => $createdUser->toArray(),
                    'roles' => $this->userService->getUserRoles($createdUser->getId())
                ];

                return $this->successResponse($userWithRoles, 'User created successfully', 201);
            }

            return $this->successResponse($createdUser, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }

            // Add roles to response
            $roles = $this->userService->getUserRoles($id);
            $userData = is_array($user) ? $user : $user->toArray();
            $userData['roles'] = $roles;

            return $this->successResponse($userData);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'phone' => 'nullable|string',
            'status' => 'nullable|string|in:active,pending,suspended',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $userData = $this->userService->getUserById($id);
            if (!$userData) {
                return $this->errorResponse('User not found', 404);
            }

            // Convert user data to DTO if needed
            $currentUser = is_array($userData) ? (object)$userData : $userData;

            $data = [
                'id' => $id,
                'name' => $request->name ?? $currentUser->name,
                'email' => $request->email ?? $currentUser->email,
                'phone' => $request->phone ?? $currentUser->phone,
                'status' => $request->status ?? $currentUser->status
            ];

            if ($request->has('password')) {
                $data['password'] = $request->password;
            }

            // Create DTO and update user
            $userDTO = UserDTO::fromArray($data);
            $updatedUser = $this->userService->updateUser($id, $userDTO);

            // Handle role assignments if provided
            if ($request->has('role_ids')) {
                $this->userService->assignRoles($id, $request->role_ids);
            }

            // Get updated user with roles
            $roles = $this->userService->getUserRoles($id);
            $updatedData = is_array($updatedUser) ? $updatedUser : $updatedUser->toArray();
            $updatedData['roles'] = $roles;

            return $this->successResponse($updatedData, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->userService->deleteUser($id);
            if ($result) {
                return $this->successResponse(null, 'User deleted successfully');
            }
            return $this->errorResponse('Failed to delete user', 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get roles for a specific user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles($id)
    {
        try {
            $roles = $this->userService->getUserRoles($id);
            return $this->successResponse($roles);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Assign roles to a user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRoles(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $result = $this->userService->assignRoles($id, $request->role_ids);
            if ($result) {
                // Get updated user with roles
                $userData = $this->userService->getUserById($id);
                $roles = $this->userService->getUserRoles($id);

                $response = is_array($userData) ? $userData : $userData->toArray();
                $response['roles'] = $roles;

                return $this->successResponse($response, 'Roles assigned successfully');
            }
            return $this->errorResponse('Failed to assign roles', 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
