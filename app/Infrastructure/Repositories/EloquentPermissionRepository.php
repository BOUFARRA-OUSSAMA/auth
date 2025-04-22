<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Permission as PermissionEntity;
use App\Domain\Interfaces\Repositories\PermissionRepositoryInterface;
use App\Infrastructure\Persistence\Models\Permission as PermissionModel;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Find permission by ID
     *
     * @param int $id
     * @return PermissionEntity|null
     */
    public function findById(int $id): ?PermissionEntity
    {
        $model = PermissionModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find permission by code
     *
     * @param string $code
     * @return PermissionEntity|null
     */
    public function findByCode(string $code): ?PermissionEntity
    {
        $model = PermissionModel::where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find permissions by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findByCriteria(array $criteria): array
    {
        $query = PermissionModel::query();

        if (isset($criteria['name'])) {
            $query->where('name', 'like', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['code'])) {
            $query->where('code', $criteria['code']);
        }

        if (isset($criteria['group'])) {
            $query->where('group', $criteria['group']);
        }

        $models = $query->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Find permissions by group
     *
     * @param string $group
     * @return array
     */
    public function findByGroup(string $group): array
    {
        $models = PermissionModel::where('group', $group)->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Find permissions by role ID
     *
     * @param int $roleId
     * @return array
     */
    public function findByRoleId(int $roleId): array
    {
        $models = PermissionModel::whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Save permission
     *
     * @param PermissionEntity $permission
     * @return PermissionEntity
     */
    public function save(PermissionEntity $permission): PermissionEntity
    {
        $data = [
            'name' => $permission->getName(),
            'code' => $permission->getCode(),
            'description' => $permission->getDescription(),
            'group' => $permission->getGroup(),
        ];

        if ($permission->getId()) {
            $model = PermissionModel::findOrFail($permission->getId());
            $model->update($data);
        } else {
            $model = PermissionModel::create($data);
        }

        $permissionEntity = $this->mapModelToEntity($model);

        return $permissionEntity;
    }

    /**
     * Delete permission
     *
     * @param PermissionEntity $permission
     * @return bool
     */
    public function delete(PermissionEntity $permission): bool
    {
        if (!$permission->getId()) {
            return false;
        }

        $model = PermissionModel::find($permission->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Get all permissions
     *
     * @return array
     */
    public function findAll(): array
    {
        $models = PermissionModel::all();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param PermissionModel $model
     * @return PermissionEntity
     */
    private function mapModelToEntity(PermissionModel $model): PermissionEntity
    {
        $permission = new PermissionEntity(
            $model->name,
            $model->code,
            $model->description,
            $model->group
        );

        $permission->setId($model->id);

        return $permission;
    }
}
