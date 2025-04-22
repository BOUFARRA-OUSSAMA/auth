<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Permission;

interface PermissionRepositoryInterface
{
    /**
     * Find a permission by ID
     *
     * @param int $id
     * @return Permission|null
     */
    public function findById(int $id): ?Permission;

    /**
     * Find a permission by code
     *
     * @param string $code
     * @return Permission|null
     */
    public function findByCode(string $code): ?Permission;

    /**
     * Save a permission (create or update)
     *
     * @param Permission $permission
     * @return Permission
     */
    public function save(Permission $permission): Permission;

    /**
     * Delete a permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function delete(Permission $permission): bool;

    /**
     * Find permissions by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria, int $page = 1, int $perPage = 15): array;

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
     * Get all permission groups
     *
     * @return array
     */
    public function findGroups(): array;
}
