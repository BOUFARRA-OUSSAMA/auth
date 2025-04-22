<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Permission;

interface PermissionRepositoryInterface
{
    /**
     * Find permission by ID
     *
     * @param int $id
     * @return Permission|null
     */
    public function findById(int $id): ?Permission;

    /**
     * Find permission by code
     *
     * @param string $code
     * @return Permission|null
     */
    public function findByCode(string $code): ?Permission;

    /**
     * Find permissions by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findByCriteria(array $criteria): array;

    /**
     * Find permissions by group
     *
     * @param string $group
     * @return array
     */
    public function findByGroup(string $group): array;

    /**
     * Find permissions by role ID
     *
     * @param int $roleId
     * @return array
     */
    public function findByRoleId(int $roleId): array;

    /**
     * Save permission
     *
     * @param Permission $permission
     * @return Permission
     */
    public function save(Permission $permission): Permission;

    /**
     * Delete permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function delete(Permission $permission): bool;

    /**
     * Get all permissions
     *
     * @return array
     */
    public function findAll(): array;
}
