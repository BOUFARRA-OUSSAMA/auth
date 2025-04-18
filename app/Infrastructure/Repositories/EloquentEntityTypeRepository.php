<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\EntityType as EntityTypeEntity;
use App\Domain\Interfaces\Repositories\EntityTypeRepositoryInterface;
use App\Infrastructure\Persistence\Models\EntityType as EntityTypeModel;

class EloquentEntityTypeRepository implements EntityTypeRepositoryInterface
{
    /**
     * Find an entity type by ID
     *
     * @param int $id
     * @return EntityTypeEntity|null
     */
    public function findById(int $id): ?EntityTypeEntity
    {
        $model = EntityTypeModel::find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find an entity type by code
     *
     * @param string $code
     * @return EntityTypeEntity|null
     */
    public function findByCode(string $code): ?EntityTypeEntity
    {
        $model = EntityTypeModel::where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Get all entity types
     *
     * @return array
     */
    public function findAll(): array
    {
        $models = EntityTypeModel::all();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Save an entity type
     *
     * @param EntityTypeEntity $entityType
     * @return EntityTypeEntity
     */
    public function save(EntityTypeEntity $entityType): EntityTypeEntity
    {
        $data = [
            'code' => $entityType->getCode(),
            'name' => $entityType->getName(),
            'description' => $entityType->getDescription(),
        ];

        if ($entityType->getId()) {
            $model = EntityTypeModel::findOrFail($entityType->getId());
            $model->update($data);
        } else {
            $model = EntityTypeModel::create($data);
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Delete an entity type
     *
     * @param EntityTypeEntity $entityType
     * @return bool
     */
    public function delete(EntityTypeEntity $entityType): bool
    {
        if (!$entityType->getId()) {
            return false;
        }

        $model = EntityTypeModel::find($entityType->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param EntityTypeModel $model
     * @return EntityTypeEntity
     */
    private function mapModelToEntity(EntityTypeModel $model): EntityTypeEntity
    {
        return new EntityTypeEntity(
            $model->code,
            $model->name,
            $model->description,
            $model->id
        );
    }
}
