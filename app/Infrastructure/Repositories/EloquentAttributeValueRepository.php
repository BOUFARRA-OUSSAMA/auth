<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\AttributeValue as AttributeValueEntity;
use app\Domain\Interfaces\Repositories\AttributeValueRepositoryInterface;
use App\Infrastructure\Persistence\Models\AttributeValue as AttributeValueModel;

class EloquentAttributeValueRepository implements AttributeValueRepositoryInterface
{
    private EloquentAttributeRepository $attributeRepository;

    public function __construct(EloquentAttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Find an attribute value by ID
     *
     * @param int $id
     * @return AttributeValueEntity|null
     */
    public function findById(int $id): ?AttributeValueEntity
    {
        $model = AttributeValueModel::with('attribute.entityType')->find($id);

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Find attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return array
     */
    public function findByEntity(string $entityType, int $entityId): array
    {
        $models = AttributeValueModel::with('attribute.entityType')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->get();

        return $models->map(function ($model) {
            return $this->mapModelToEntity($model);
        })->all();
    }

    /**
     * Find a specific attribute value for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $attributeId
     * @return AttributeValueEntity|null
     */
    public function findByEntityAndAttribute(string $entityType, int $entityId, int $attributeId): ?AttributeValueEntity
    {
        $model = AttributeValueModel::with('attribute.entityType')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('attribute_id', $attributeId)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Save an attribute value
     *
     * @param AttributeValueEntity $attributeValue
     * @return AttributeValueEntity
     */
    public function save(AttributeValueEntity $attributeValue): AttributeValueEntity
    {
        // Check if this value already exists
        $existing = AttributeValueModel::where('attribute_id', $attributeValue->getAttribute()->getId())
            ->where('entity_type', $attributeValue->getEntityType())
            ->where('entity_id', $attributeValue->getEntityId())
            ->first();

        $data = [
            'attribute_id' => $attributeValue->getAttribute()->getId(),
            'entity_type' => $attributeValue->getEntityType(),
            'entity_id' => $attributeValue->getEntityId(),
            'value' => $attributeValue->getValue(),
        ];

        if ($existing) {
            // Update
            $existing->update($data);
            $model = $existing->fresh(['attribute.entityType']);
        } else {
            // Create
            $model = AttributeValueModel::create($data);
            $model->load('attribute.entityType');
        }

        return $this->mapModelToEntity($model);
    }

    /**
     * Delete an attribute value
     *
     * @param AttributeValueEntity $attributeValue
     * @return bool
     */
    public function delete(AttributeValueEntity $attributeValue): bool
    {
        if (!$attributeValue->getId()) {
            return false;
        }

        $model = AttributeValueModel::find($attributeValue->getId());
        if (!$model) {
            return false;
        }

        return (bool) $model->delete();
    }

    /**
     * Delete all attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return bool
     */
    public function deleteByEntity(string $entityType, int $entityId): bool
    {
        return (bool) AttributeValueModel::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();
    }

    /**
     * Map Eloquent model to domain entity
     *
     * @param AttributeValueModel $model
     * @return AttributeValueEntity
     */
    private function mapModelToEntity(AttributeValueModel $model): AttributeValueEntity
    {
        // Get the attribute entity
        $attribute = $this->attributeRepository->findById($model->attribute_id);

        return new AttributeValueEntity(
            $attribute,
            $model->entity_type,
            $model->entity_id,
            $model->value,
            $model->id
        );
    }
}
