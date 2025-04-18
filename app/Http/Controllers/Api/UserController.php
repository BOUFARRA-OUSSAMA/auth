<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\DTOs\UserDTO;
use App\Application\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class UserController extends Controller
{
    use ApiResponseTrait;

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 15);

        // Extract filter criteria from request
        $criteria = [];
        if ($request->has('name')) {
            $criteria['name'] = $request->query('name');
        }
        if ($request->has('email')) {
            $criteria['email'] = $request->query('email');
        }
        if ($request->has('status')) {
            $criteria['status'] = $request->query('status');
        }

        $result = $this->userService->findUsers($criteria, $page, $perPage);

        return $this->paginatedResponse(
            array_map(fn($item) => $item->toArray(), $result['items']),
            $result['total'],
            $result['current_page'],
            $result['per_page'],
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,pending,suspended',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $userDTO = UserDTO::fromArray($request->all());
            $createdUser = $this->userService->createUser($userDTO, $request->password);
            return $this->successResponse($createdUser, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified user.
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
            return $this->successResponse($user);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,pending,suspended',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }

        try {
            $userDTO = UserDTO::fromArray(array_merge($request->all(), ['id' => $id]));
            $updatedUser = $this->userService->updateUser($id, $userDTO);
            return $this->successResponse($updatedUser, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified user from storage.
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
     * Get roles for a user
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
}
