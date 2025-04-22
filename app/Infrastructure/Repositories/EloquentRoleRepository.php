<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Permission as PermissionEntity;
use App\Domain\Entities\Role as RoleEntity;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Role as RoleModel;
use App\Infrastructure\Persistence\Models\Permission as PermissionModel;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    /**
     * Find role by ID
     *
     * @param int $id
     * @return RoleEntity|null
     */
    public function findById(int $id): ?RoleEntity
    {
        $model = RoleModel::with('permissions')->find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find role by code
     *
     * @param string $code
     * @return RoleEntity|null
     */
    public function findByCode(string $code): ?RoleEntity
    {
        $model = RoleModel::with('permissions')->where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find roles by criteria with pagination
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria = [], int $page = 1, int $perPage = 15): array
    {
        $query = RoleModel::with('permissions');

        if (isset($criteria['name'])) {
            $query->where('name', 'like', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['code'])) {
            $query->where('code', $criteria['code']);
        }

        // Get total count before pagination
        $total = $query->count();

        // Apply pagination
        $models = $query->orderBy('id', 'asc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        // Calculate last page
        $lastPage = ceil($total / $perPage);

        // Map models to entities
        $entities = $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();

        // Return with pagination structure
        return [
            'items' => $entities,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage
        ];
    }

    /**
     * Save role
     *
     * @param RoleEntity $role
     * @return RoleEntity
     */
    public function save(RoleEntity $role): RoleEntity
    {
        $data = [
            'name' => $role->getName(),
            'code' => $role->getCode(),
            'description' => $role->getDescription(),
        ];

        if ($role->getId()) {
            $model = RoleModel::findOrFail($role->getId());
            $model->update($data);
        } else {
            $model = RoleModel::create($data);
        }

        $roleEntity = $this->mapModelToEntity($model);

        return $roleEntity;
    }

    /**
     * Delete role
     *
     * @param RoleEntity $role
     * @return bool
     */
    public function delete(RoleEntity $role): bool
    {
        if (!$role->getId()) {
            return false;
        }

        $model = RoleModel::find($role->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
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
        $role = RoleModel::find($roleId);

        if (!$role) {
            return false;
        }

        $role->permissions()->sync($permissionIds);

        return true;
    }

    /**
     * Find roles by user ID
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        $models = RoleModel::with('permissions')->whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param RoleModel $model
     * @return RoleEntity
     */
    private function mapModelToEntity(RoleModel $model): RoleEntity
    {
        $role = new RoleEntity(
            $model->name,
            $model->code,
            $model->description
        );

        $role->setId($model->id);

        // Map permissions if they are loaded
        if ($model->relationLoaded('permissions')) {
            $permissions = [];
            foreach ($model->permissions as $permissionModel) {
                $permission = new PermissionEntity(
                    $permissionModel->name,
                    $permissionModel->code,
                    $permissionModel->description,
                    $permissionModel->group
                );
                $permission->setId($permissionModel->id);
                $permissions[] = $permission;
            }
            $role->setPermissions($permissions);
        }

        return $role;
    }
}
