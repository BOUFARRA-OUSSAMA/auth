<?php

namespace App\Application\Services;

use App\Application\DTOs\RoleDTO;
use App\Application\DTOs\PermissionDTO;
use App\Domain\Entities\Role;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Domain\Interfaces\Repositories\PermissionRepositoryInterface;
use App\Infrastructure\Persistence\Models\Role as RoleModel;
use App\Infrastructure\Persistence\Models\User as UserModel;

class RoleService
{
    private RoleRepositoryInterface $roleRepository;
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get a role by ID
     *
     * @param int $id
     * @return RoleDTO|null
     */
    public function getRoleById(int $id): ?RoleDTO
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return null;
        }

        return new RoleDTO(
            $role->getName(),
            $role->getCode(),
            $role->getDescription(),
            $role->getId()
        );
    }

    /**
     * Get roles with pagination and filters
     * 
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getRoles(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $result = $this->roleRepository->findByCriteria($filters, $page, $perPage);

        // Convert domain entities to DTOs
        $dtos = array_map(function (Role $role) {
            return new RoleDTO(
                $role->getName(),
                $role->getCode(),
                $role->getDescription(),
                $role->getId()
            );
        }, $result['items']);

        return [
            'items' => $dtos,
            'total' => $result['total'],
            'current_page' => $result['current_page'],
            'per_page' => $result['per_page'],
            'last_page' => $result['last_page'],
        ];
    }

    /**
     * Create a new role
     *
     * @param RoleDTO $roleDTO
     * @return RoleDTO
     */
    public function createRole(RoleDTO $roleDTO): RoleDTO
    {
        // Check if code already exists
        $existingRole = $this->roleRepository->findByCode($roleDTO->getCode());
        if ($existingRole) {
            throw new \InvalidArgumentException('Role code already exists');
        }

        // Create the role entity
        $role = new Role(
            $roleDTO->getName(),
            $roleDTO->getCode(),
            $roleDTO->getDescription()
        );

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Return DTO
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId()
        );
    }

    /**
     * Update an existing role
     *
     * @param RoleDTO $roleDTO
     * @return RoleDTO
     */
    public function updateRole(RoleDTO $roleDTO): RoleDTO
    {
        if (!$roleDTO->getId()) {
            throw new \InvalidArgumentException('Role ID is required for update');
        }

        // Find the role
        $role = $this->roleRepository->findById($roleDTO->getId());
        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        // Check if code already exists (if changed)
        if ($role->getCode() !== $roleDTO->getCode()) {
            $existingRole = $this->roleRepository->findByCode($roleDTO->getCode());
            if ($existingRole && $existingRole->getId() !== $roleDTO->getId()) {
                throw new \InvalidArgumentException('Role code already exists');
            }
        }

        // Update the role entity
        $role->setName($roleDTO->getName());
        $role->setCode($roleDTO->getCode());
        $role->setDescription($roleDTO->getDescription());

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Return updated DTO
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId()
        );
    }

    /**
     * Delete a role
     *
     * @param int $id
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        return $this->roleRepository->delete($role);
    }

    /**
     * Assign permissions to a role
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        // Check if role exists
        $role = $this->getRoleById($roleId);
        if (!$role) {
            throw new \InvalidArgumentException('Role not found');
        }

        $roleModel = RoleModel::find($roleId);
        $roleModel->permissions()->sync($permissionIds);

        return true;
    }

    /**
     * Get a role with permissions by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getRoleWithPermissions(int $id): ?array
    {
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            return null;
        }

        $roleDTO = new RoleDTO(
            $role->getName(),
            $role->getCode(),
            $role->getDescription(),
            $role->getId()
        );

        // Get permissions
        $permissions = $this->permissionRepository->findByRoleId($id);

        $permissionDTOs = array_map(function ($permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $permissions);

        $result = $roleDTO->toArray();
        $result['permissions'] = array_map(function ($permissionDTO) {
            return $permissionDTO->toArray();
        }, $permissionDTOs);

        return $result;
    }

    /**
     * Get users by role ID
     *
     * @param int $id
     * @return array|null
     */
    public function getUsersByRoleId(int $id): ?array
    {
        // Check if role exists
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            return null;
        }

        // Get users with this role
        $users = UserModel::whereHas('roles', function ($query) use ($id) {
            $query->where('roles.id', $id);
        })->get();

        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
            ];
        })->toArray();
    }

    /**
     * Get roles by user ID
     *
     * @param int $userId
     * @return array
     */
    public function getRolesByUserId(int $userId): array
    {
        $roleModels = RoleModel::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();

        return $roleModels->map(function ($model) {
            $role = new Role(
                $model->name,
                $model->code,
                $model->description,
                $model->id
            );

            return new RoleDTO(
                $role->getName(),
                $role->getCode(),
                $role->getDescription(),
                $role->getId()
            );
        })->toArray();
    }
}
