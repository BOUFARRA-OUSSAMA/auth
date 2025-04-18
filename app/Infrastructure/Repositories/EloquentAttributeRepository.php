<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Attribute as AttributeEntity;
use App\Domain\Entities\EntityType as EntityTypeEntity;
use App\Domain\Interfaces\Repositories\AttributeRepositoryInterface;
use App\Infrastructure\Persistence\Models\Attribute as AttributeModel;

class EloquentAttributeRepository implements AttributeRepositoryInterface
{
    private EloquentEntityTypeRepository $entityTypeRepository;

    public function __construct(EloquentEntityTypeRepository $entityTypeRepository)
    {
        $this->entityTypeRepository = $entityTypeRepository;
    }

    /**
     * Find an attribute by ID
     *
     * @param int $id
     * @return AttributeEntity|null
     */
    public function findById(int $id): ?AttributeEntity
    {
        $model = AttributeModel::with('entityType')->find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find an attribute by code
     *
     * @param string $code
     * @return AttributeEntity|null
     */
    public function findByCode(string $code): ?AttributeEntity
    {
        $model = AttributeModel::with('entityType')->where('code', $code)->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find attributes by entity type
     *
     * @param EntityTypeEntity $entityType
     * @return array
     */
    public function findByEntityType(EntityTypeEntity $entityType): array
    {
        $models = AttributeModel::with('entityType')
            ->where('entity_type_id', $entityType->getId())
            ->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function findAll(): array
    {
        $models = AttributeModel::with('entityType')->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Save an attribute
     *
     * @param AttributeEntity $attribute
     * @return AttributeEntity
     */
    public function save(AttributeEntity $attribute): AttributeEntity
    {
        $data = [
            'code' => $attribute->getCode(),
            'name' => $attribute->getName(),
            'type' => $attribute->getType(),
            'entity_type_id' => $attribute->getEntityType() ? $attribute->getEntityType()->getId() : null,
            'is_required' => $attribute->isRequired(),
            'description' => $attribute->getDescription(),
        ];

        if ($attribute->getId()) {
            $model = AttributeModel::findOrFail($attribute->getId());
            $model->update($data);
        } else {
            $model = AttributeModel::create($data);
        }

        return $this->mapModelToEntity($model->fresh(['entityType']));
    }

    /**
     * Delete an attribute
     *
     * @param AttributeEntity $attribute
     * @return bool
     */
    public function delete(AttributeEntity $attribute): bool
    {
        if (!$attribute->getId()) {
            return false;
        }

        $model = AttributeModel::find($attribute->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param AttributeModel $model
     * @return AttributeEntity
     */
    private function mapModelToEntity(AttributeModel $model): AttributeEntity
    {
        $entityType = null;
        if ($model->entityType) {
            $entityType = new EntityTypeEntity(
                $model->entityType->code,
                $model->entityType->name,
                $model->entityType->description,
                $model->entityType->id
            );
        }

        return new AttributeEntity(
            $model->code,
            $model->name,
            $model->type,
            $entityType,
            (bool) $model->is_required,
            $model->description,
            $model->id
        );
    }
}
