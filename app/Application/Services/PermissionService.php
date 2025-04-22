<?php

namespace App\Application\Services;

use App\Application\DTOs\PermissionDTO;
use App\Domain\Entities\Permission;
use App\Domain\Interfaces\Repositories\PermissionRepositoryInterface;
use InvalidArgumentException;

class PermissionService
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get a permission by ID
     *
     * @param int $id
     * @return PermissionDTO|null
     */
    public function getPermissionById(int $id): ?PermissionDTO
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            return null;
        }

        return new PermissionDTO(
            $permission->getName(),
            $permission->getCode(),
            $permission->getDescription(),
            $permission->getGroup(),
            $permission->getId()
        );
    }

    /**
     * Get a permission by code
     *
     * @param string $code
     * @return PermissionDTO|null
     */
    public function getPermissionByCode(string $code): ?PermissionDTO
    {
        $permission = $this->permissionRepository->findByCode($code);

        if (!$permission) {
            return null;
        }

        return new PermissionDTO(
            $permission->getName(),
            $permission->getCode(),
            $permission->getDescription(),
            $permission->getGroup(),
            $permission->getId()
        );
    }

    /**
     * Create a new permission
     *
     * @param PermissionDTO $permissionDTO
     * @return PermissionDTO
     */
    public function createPermission(PermissionDTO $permissionDTO): PermissionDTO
    {
        // Check if code already exists
        $existingPermission = $this->permissionRepository->findByCode($permissionDTO->getCode());
        if ($existingPermission) {
            throw new InvalidArgumentException('Permission code already exists');
        }

        // Create the permission entity
        $permission = new Permission(
            $permissionDTO->getName(),
            $permissionDTO->getCode(),
            $permissionDTO->getDescription(),
            $permissionDTO->getGroup()
        );

        // Save the permission
        $savedPermission = $this->permissionRepository->save($permission);

        // Return DTO with the generated ID
        return new PermissionDTO(
            $savedPermission->getName(),
            $savedPermission->getCode(),
            $savedPermission->getDescription(),
            $savedPermission->getGroup(),
            $savedPermission->getId()
        );
    }

    /**
     * Update an existing permission
     *
     * @param PermissionDTO $permissionDTO
     * @return PermissionDTO
     */
    public function updatePermission(PermissionDTO $permissionDTO): PermissionDTO
    {
        if (!$permissionDTO->getId()) {
            throw new InvalidArgumentException('Permission ID is required for update');
        }

        // Find the permission
        $permission = $this->permissionRepository->findById($permissionDTO->getId());
        if (!$permission) {
            throw new InvalidArgumentException('Permission not found');
        }

        // Check if code already exists (if changed)
        if ($permission->getCode() !== $permissionDTO->getCode()) {
            $existingPermission = $this->permissionRepository->findByCode($permissionDTO->getCode());
            if ($existingPermission && $existingPermission->getId() !== $permissionDTO->getId()) {
                throw new InvalidArgumentException('Permission code already exists');
            }
        }

        // Update the permission entity
        $permission->setName($permissionDTO->getName());
        $permission->setCode($permissionDTO->getCode());
        $permission->setDescription($permissionDTO->getDescription());
        $permission->setGroup($permissionDTO->getGroup());

        // Save the permission
        $savedPermission = $this->permissionRepository->save($permission);

        // Return updated DTO
        return new PermissionDTO(
            $savedPermission->getName(),
            $savedPermission->getCode(),
            $savedPermission->getDescription(),
            $savedPermission->getGroup(),
            $savedPermission->getId()
        );
    }

    /**
     * Delete a permission
     *
     * @param int $id
     * @return bool
     */
    public function deletePermission(int $id): bool
    {
        $permission = $this->permissionRepository->findById($id);
        if (!$permission) {
            throw new InvalidArgumentException('Permission not found');
        }

        return $this->permissionRepository->delete($permission);
    }

    /**
     * Find permissions by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findPermissions(array $criteria = []): array
    {
        $permissions = $this->permissionRepository->findByCriteria($criteria);

        // Convert domain entities to DTOs
        return array_map(function (Permission $permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $permissions);
    }

    /**
     * Get all permissions
     *
     * @return array
     */
    public function getAllPermissions(): array
    {
        return $this->findPermissions();
    }

    /**
     * Get permissions by group
     *
     * @param string $group
     * @return array
     */
    public function getPermissionsByGroup(string $group): array
    {
        $permissions = $this->permissionRepository->findByGroup($group);

        // Convert domain entities to DTOs
        return array_map(function (Permission $permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $permissions);
    }

    /**
     * Get permissions by role ID
     *
     * @param int $roleId
     * @return array
     */
    public function getPermissionsByRoleId(int $roleId): array
    {
        $permissions = $this->permissionRepository->findByRoleId($roleId);

        // Convert domain entities to DTOs
        return array_map(function (Permission $permission) {
            return new PermissionDTO(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $permission->getId()
            );
        }, $permissions);
    }
}
