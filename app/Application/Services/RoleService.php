<?php

namespace App\Application\Services;

use App\Application\DTOs\PermissionDTO;
use App\Application\DTOs\RoleDTO;
use App\Application\DTOs\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Domain\Interfaces\Repositories\PermissionRepositoryInterface;
use InvalidArgumentException;

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

        // Map permissions to DTOs
        $permissions = array_map(function ($permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $role->getPermissions());

        return new RoleDTO(
            $role->getName(),
            $role->getCode(),
            $role->getDescription(),
            $role->getId(),
            $permissions
        );
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
            throw new InvalidArgumentException('Role code already exists');
        }

        // Create the role entity
        $role = new Role(
            $roleDTO->getName(),
            $roleDTO->getCode(),
            $roleDTO->getDescription()
        );

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Assign permissions if provided
        if (!empty($roleDTO->getPermissions())) {
            $permissionIds = array_map(function ($permission) {
                return is_array($permission) ? $permission['id'] : $permission;
            }, $roleDTO->getPermissions());

            $this->roleRepository->assignPermissions($savedRole->getId(), $permissionIds);

            // Reload the role with permissions
            $savedRole = $this->roleRepository->findById($savedRole->getId());
        }

        // Map permissions to DTOs
        $permissions = array_map(function ($permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $savedRole->getPermissions());

        // Return DTO with the generated ID
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId(),
            $permissions
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
            throw new InvalidArgumentException('Role ID is required for update');
        }

        // Find the role
        $role = $this->roleRepository->findById($roleDTO->getId());
        if (!$role) {
            throw new InvalidArgumentException('Role not found');
        }

        // Check if code already exists (if changed)
        if ($role->getCode() !== $roleDTO->getCode()) {
            $existingRole = $this->roleRepository->findByCode($roleDTO->getCode());
            if ($existingRole && $existingRole->getId() !== $roleDTO->getId()) {
                throw new InvalidArgumentException('Role code already exists');
            }
        }

        // Update the role entity
        $role->setName($roleDTO->getName());
        $role->setCode($roleDTO->getCode());
        $role->setDescription($roleDTO->getDescription());

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Update permissions if provided
        if ($roleDTO->getPermissions() !== null) {
            $permissionIds = array_map(function ($permission) {
                return is_array($permission) ? $permission['id'] : $permission;
            }, $roleDTO->getPermissions());

            $this->roleRepository->assignPermissions($savedRole->getId(), $permissionIds);

            // Reload the role with permissions
            $savedRole = $this->roleRepository->findById($savedRole->getId());
        }

        // Map permissions to DTOs
        $permissions = array_map(function ($permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $savedRole->getPermissions());

        // Return updated DTO
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId(),
            $permissions
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
            throw new InvalidArgumentException('Role not found');
        }

        return $this->roleRepository->delete($role);
    }

    /**
     * Get all roles
     *
     * @return array
     */
    public function getAllRoles(): array
    {
        $roles = $this->roleRepository->findByCriteria([]);

        // Convert domain entities to DTOs
        return array_map(function ($role) {
            // Map permissions to DTOs
            $permissions = array_map(function ($permission) {
                return new PermissionDTO(
                    $permission->getName(),
                    $permission->getCode(),
                    $permission->getDescription(),
                    $permission->getGroup(),
                    $permission->getId()
                );
            }, $role->getPermissions());

            return new RoleDTO(
                $role->getName(),
                $role->getCode(),
                $role->getDescription(),
                $role->getId(),
                $permissions
            );
        }, $roles);
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return array
     */
    public function getUsersByRole(int $roleId): array
    {
        // This would need to be implemented in the repository
        // For now, we can just return an empty array
        return [];
    }

    /**
     * Assign permissions to role
     * 
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function assignPermissions(int $roleId, array $permissionIds): bool
    {
        // Check if role exists
        $role = $this->roleRepository->findById($roleId);
        if (!$role) {
            throw new InvalidArgumentException('Role not found');
        }

        // Check if all permissions exist
        foreach ($permissionIds as $permissionId) {
            $permission = $this->permissionRepository->findById($permissionId);
            if (!$permission) {
                throw new InvalidArgumentException("Permission with ID {$permissionId} not found");
            }
        }

        return $this->roleRepository->assignPermissions($roleId, $permissionIds);
    }

    /**
     * Get roles by user ID
     *
     * @param int $userId
     * @return array
     */
    public function getRolesByUserId(int $userId): array
    {
        $roles = $this->roleRepository->findByUserId($userId);

        // Convert domain entities to DTOs
        return array_map(function ($role) {
            // Map permissions to DTOs
            $permissions = array_map(function ($permission) {
                return new PermissionDTO(
                    $permission->getName(),
                    $permission->getCode(),
                    $permission->getDescription(),
                    $permission->getGroup(),
                    $permission->getId()
                );
            }, $role->getPermissions());

            return new RoleDTO(
                $role->getName(),
                $role->getCode(),
                $role->getDescription(),
                $role->getId(),
                $permissions
            );
        }, $roles);
    }
}
