<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Permission as PermissionEntity;
use App\Domain\Interfaces\Repositories\PermissionRepositoryInterface;
use App\Infrastructure\Persistence\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\DB;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Find a permission by ID
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
     * Find a permission by code
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
     * Save a permission (create or update)
     *
     * @param PermissionEntity $permission
     * @return PermissionEntity
     */
    public function save(PermissionEntity $permission): PermissionEntity
    {
        if ($permission->getId()) {
            // Update
            $model = PermissionModel::find($permission->getId());
            if (!$model) {
                throw new \RuntimeException('Permission not found');
            }
        } else {
            // Create
            $model = new PermissionModel();
        }

        $model->name = $permission->getName();
        $model->code = $permission->getCode();
        $model->description = $permission->getDescription();
        $model->group = $permission->getGroup();

        $model->save();

        // Set the ID if it was a creation
        if (!$permission->getId()) {
            $permission = new PermissionEntity(
                $permission->getName(),
                $permission->getCode(),
                $permission->getDescription(),
                $permission->getGroup(),
                $model->id
            );
        }

        return $permission;
    }

    /**
     * Delete a permission
     *
     * @param PermissionEntity $permission
     * @return bool
     */
    public function delete(PermissionEntity $permission): bool
    {
        $model = PermissionModel::find($permission->getId());

        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Find permissions by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria, int $page = 1, int $perPage = 15): array
    {
        $query = PermissionModel::query();

        // Apply filters
        if (!empty($criteria['name'])) {
            $query->where('name', 'like', '%' . $criteria['name'] . '%');
        }

        if (!empty($criteria['code'])) {
            $query->where('code', 'like', '%' . $criteria['code'] . '%');
        }

        if (!empty($criteria['group'])) {
            $query->where('group', $criteria['group']);
        }

        // Get total count
        $total = $query->count();

        // Apply pagination
        $items = $query->orderBy('id', 'asc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        // Calculate last page
        $lastPage = ceil($total / $perPage);

        // Map models to entities
        $entities = $items->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();

        return [
            'items' => $entities,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage
        ];
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
        })->toArray();
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
        })->toArray();
    }

    /**
     * Get all permission groups
     *
     * @return array
     */
    public function findGroups(): array
    {
        return PermissionModel::select('group')
            ->distinct()
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group')
            ->toArray();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param PermissionModel $model
     * @return PermissionEntity
     */
    private function mapModelToEntity(PermissionModel $model): PermissionEntity
    {
        return new PermissionEntity(
            $model->name,
            $model->code,
            $model->description,
            $model->group,
            $model->id
        );
    }
}
