<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\DTOs\PermissionDTO;
use App\Application\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
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
        if ($request->has('group')) {
            $filters['group'] = $request->query('group');
        }

        // Get pagination parameters
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 15);

        try {
            $permissions = $this->permissionService->findPermissions($filters, $page, $perPage);

            // Convert objects to arrays for consistent output
            $result = [
                'items' => array_map(function ($permission) {
                    return $permission instanceof PermissionDTO ? $permission->toArray() : $permission;
                }, $permissions['items']),
                'total' => $permissions['total'],
                'current_page' => $permissions['current_page'],
                'per_page' => $permissions['per_page'],
                'last_page' => $permissions['last_page'],
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
            'code' => 'required|string|max:100|unique:permissions,code',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $permissionDTO = new PermissionDTO(
                $request->name,
                $request->code,
                $request->description,
                $request->group
            );

            $permission = $this->permissionService->createPermission($permissionDTO);
            return $this->successResponse($permission->toArray(), 'Permission created successfully', 201);
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
            $permission = $this->permissionService->getPermissionById($id);

            if (!$permission) {
                return $this->errorResponse('Permission not found', 404);
            }

            return $this->successResponse($permission->toArray());
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
            'code' => 'sometimes|required|string|max:100|unique:permissions,code,' . $id,
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            // Ensure ID is an integer
            $id = (int)$id;
            $permission = $this->permissionService->getPermissionById($id);

            if (!$permission) {
                return $this->errorResponse('Permission not found', 404);
            }

            // Update properties that are present in the request
            $updatedPermissionDTO = new PermissionDTO(
                $request->has('name') ? $request->name : $permission->getName(),
                $request->has('code') ? $request->code : $permission->getCode(),
                $request->has('description') ? $request->description : $permission->getDescription(),
                $request->has('group') ? $request->group : $permission->getGroup(),
                $id
            );

            $updatedPermission = $this->permissionService->updatePermission($updatedPermissionDTO);
            return $this->successResponse($updatedPermission->toArray(), 'Permission updated successfully');
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
            $result = $this->permissionService->deletePermission($id);

            if ($result) {
                return $this->successResponse(null, 'Permission deleted successfully');
            }
            return $this->errorResponse('Failed to delete permission', 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get all permission groups
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function groups()
    {
        try {
            $groups = $this->permissionService->getPermissionGroups();
            return $this->successResponse($groups);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get permissions by group
     * 
     * @param string $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function byGroup($group)
    {
        try {
            $permissions = $this->permissionService->getPermissionsByGroup($group);

            // Convert to array output for consistency
            $result = array_map(function ($permission) {
                return $permission instanceof PermissionDTO ? $permission->toArray() : $permission;
            }, $permissions);

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get permissions by role ID
     * 
     * @param int $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRole($roleId)
    {
        try {
            // Ensure ID is an integer
            $roleId = (int)$roleId;
            $permissions = $this->permissionService->getPermissionsByRoleId($roleId);

            // Convert to array output for consistency
            $result = array_map(function ($permission) {
                return $permission instanceof PermissionDTO ? $permission->toArray() : $permission;
            }, $permissions);

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
