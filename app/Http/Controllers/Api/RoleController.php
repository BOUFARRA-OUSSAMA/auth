<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\DTOs\RoleDTO;
use App\Application\Services\RoleService;
use App\Application\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class RoleController extends Controller
{
    use ApiResponseTrait;

    private RoleService $roleService;
    private UserService $userService;

    public function __construct(RoleService $roleService, UserService $userService)
    {
        $this->roleService = $roleService;
        $this->userService = $userService;
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
        if ($request->has('code')) {
            $filters['code'] = $request->query('code');
        }

        // Get pagination parameters
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 15);

        try {
            $roles = $this->roleService->getRoles($filters, $page, $perPage);

            // Convert to array output for consistency
            $result = [
                'items' => array_map(function ($role) {
                    return $role instanceof RoleDTO ? $role->toArray() : $role;
                }, $roles['items']),
                'total' => $roles['total'],
                'current_page' => $roles['current_page'],
                'per_page' => $roles['per_page'],
                'last_page' => $roles['last_page'],
            ];

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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
            'code' => 'required|string|max:100|unique:roles,code',
            'description' => 'nullable|string',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $roleDTO = new RoleDTO(
                $request->name,
                $request->code,
                $request->description
            );

            $role = $this->roleService->createRole($roleDTO);

            // Assign permissions if provided
            if ($request->has('permission_ids') && !empty($request->permission_ids)) {
                $this->roleService->assignPermissions($role->getId(), $request->permission_ids);

                // Get updated role with permissions
                $role = $this->roleService->getRoleWithPermissions($role->getId());
            }

            // Convert to array format
            $result = $role instanceof RoleDTO ? $role->toArray() : $role;

            return $this->successResponse($result, 'Role created successfully', 201);
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
            // Ensure ID is an integer
            $id = (int)$id;
            $role = $this->roleService->getRoleWithPermissions($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            // Convert to array format
            $result = $role instanceof RoleDTO ? $role->toArray() : $role;

            return $this->successResponse($result);
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
            'code' => 'sometimes|required|string|max:100|unique:roles,code,' . $id,
            'description' => 'nullable|string',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            // Ensure ID is an integer
            $id = (int)$id;
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            // Update properties
            $updatedRoleDTO = new RoleDTO(
                $request->has('name') ? $request->name : $role->getName(),
                $request->has('code') ? $request->code : $role->getCode(),
                $request->has('description') ? $request->description : $role->getDescription(),
                $id
            );

            $updatedRole = $this->roleService->updateRole($updatedRoleDTO);

            // Update permissions if provided
            if ($request->has('permission_ids')) {
                $this->roleService->assignPermissions($id, $request->permission_ids);
            }

            // Get updated role with permissions
            $roleWithPermissions = $this->roleService->getRoleWithPermissions($id);

            // Convert to array format
            $result = $roleWithPermissions instanceof RoleDTO ? $roleWithPermissions->toArray() : $roleWithPermissions;

            return $this->successResponse($result, 'Role updated successfully');
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
            // Ensure ID is an integer
            $id = (int)$id;
            $result = $this->roleService->deleteRole($id);

            if ($result) {
                return $this->successResponse(null, 'Role deleted successfully');
            }
            return $this->errorResponse('Failed to delete role', 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get permissions for a role
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissions($id)
    {
        try {
            // Ensure ID is an integer
            $id = (int)$id;
            $role = $this->roleService->getRoleWithPermissions($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            // If role is a DTO, extract permissions
            $permissions = [];
            if ($role instanceof RoleDTO) {
                $permissions = array_map(function ($permission) {
                    return $permission->toArray();
                }, $role->getPermissions() ?? []);
            } else if (isset($role['permissions'])) {
                $permissions = $role['permissions'];
            }

            return $this->successResponse($permissions);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Assign permissions to a role
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissions(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            // Ensure ID is an integer
            $id = (int)$id;
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            $result = $this->roleService->assignPermissions($id, $request->permission_ids);

            if ($result) {
                // Get updated role with permissions
                $updatedRole = $this->roleService->getRoleWithPermissions($id);

                // Convert to array format
                $data = $updatedRole instanceof RoleDTO ? $updatedRole->toArray() : $updatedRole;

                return $this->successResponse($data, 'Permissions assigned successfully');
            }

            return $this->errorResponse('Failed to assign permissions', 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get users for a specific role
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function users($id)
    {
        try {
            // Ensure ID is an integer
            $id = (int)$id;
            $users = $this->roleService->getUsersByRoleId($id);

            if ($users === null) {
                return $this->errorResponse('Role not found', 404);
            }

            return $this->successResponse($users);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
