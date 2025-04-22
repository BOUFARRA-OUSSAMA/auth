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
        $criteria = [];

        if ($request->has('name')) {
            $criteria['name'] = $request->query('name');
        }

        if ($request->has('code')) {
            $criteria['code'] = $request->query('code');
        }

        if ($request->has('group')) {
            $criteria['group'] = $request->query('group');
        }

        if ($request->has('group_only') && $request->query('group_only') === 'true') {
            // Get unique groups for grouping in the UI
            $permissions = $this->permissionService->getAllPermissions();
            $groups = [];

            foreach ($permissions as $permission) {
                $group = $permission->getGroup();
                if ($group && !in_array($group, $groups)) {
                    $groups[] = $group;
                }
            }

            return $this->successResponse($groups);
        }

        $permissions = $this->permissionService->findPermissions($criteria);
        return $this->successResponse($permissions);
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
            'code' => 'required|string|max:100|unique:permissions',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $permissionDTO = PermissionDTO::fromArray($request->all());
            $createdPermission = $this->permissionService->createPermission($permissionDTO);
            return $this->successResponse($createdPermission, 'Permission created successfully', 201);
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
            $permission = $this->permissionService->getPermissionById($id);
            if (!$permission) {
                return $this->errorResponse('Permission not found', 404);
            }
            return $this->successResponse($permission);
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:permissions,code,' . $id,
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['id'] = $id;
            $permissionDTO = PermissionDTO::fromArray($data);
            $updatedPermission = $this->permissionService->updatePermission($permissionDTO);
            return $this->successResponse($updatedPermission, 'Permission updated successfully');
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
     * Get permissions by group.
     *
     * @param  string  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function byGroup($group)
    {
        try {
            $permissions = $this->permissionService->getPermissionsByGroup($group);
            return $this->successResponse($permissions);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get permissions by role ID.
     *
     * @param  int  $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byRole($roleId)
    {
        try {
            $permissions = $this->permissionService->getPermissionsByRoleId($roleId);
            return $this->successResponse($permissions);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
