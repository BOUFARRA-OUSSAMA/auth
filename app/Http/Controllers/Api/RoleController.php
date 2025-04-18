<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\DTOs\RoleDTO;
use App\Application\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class RoleController extends Controller
{
    use ApiResponseTrait;

    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        return $this->successResponse($roles);
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
            'code' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $roleDTO = RoleDTO::fromArray($request->all());
            $createdRole = $this->roleService->createRole($roleDTO);
            return $this->successResponse($createdRole, 'Role created successfully', 201);
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
            $role = $this->roleService->getRoleById($id);
            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }
            return $this->successResponse($role);
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
            'code' => 'required|string|max:50|unique:roles,code,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $data = $request->all();
            $data['id'] = $id;
            $roleDTO = RoleDTO::fromArray($data);
            $updatedRole = $this->roleService->updateRole($roleDTO);
            return $this->successResponse($updatedRole, 'Role updated successfully');
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
     * Get users with this role
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function users($id)
    {
        try {
            $users = $this->roleService->getUsersByRole($id);
            return $this->successResponse($users);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
