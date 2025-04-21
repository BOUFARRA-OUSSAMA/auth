<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Models\AttributeValue;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeValueRepository
{
    /**
     * Get all attribute values (paginated).
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllAttributeValues(int $perPage = 15): LengthAwarePaginator
    {
        return AttributeValue::paginate($perPage);
    }

    /**
     * Get a specific attribute value.
     *
     * @param int $attributeId
     * @param string $entityType
     * @param int $entityId
     * @return AttributeValue|null
     */
    public function getAttributeValue(int $attributeId, string $entityType, int $entityId)
    {
        return AttributeValue::where('attribute_id', $attributeId)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->first();
    }

    /**
     * Get all attribute values for an entity.
     *
     * @param string $entityType
     * @param int $entityId
     * @return Collection
     */
    public function getEntityAttributeValues(string $entityType, int $entityId): Collection
    {
        return AttributeValue::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->get();
    }

    /**
     * Get all values for an attribute.
     *
     * @param int $attributeId
     * @return Collection
     */
    public function getAttributeAllValues(int $attributeId): Collection
    {
        return AttributeValue::where('attribute_id', $attributeId)->get();
    }

    /**
     * Create a new attribute value.
     *
     * @param array $data
     * @return AttributeValue
     */
    public function createAttributeValue(array $data): AttributeValue
    {
        return AttributeValue::create($data);
    }

    /**
     * Update an existing attribute value.
     *
     * @param AttributeValue $attributeValue
     * @param array $data
     * @return AttributeValue
     */
    public function updateAttributeValue(AttributeValue $attributeValue, array $data): AttributeValue
    {
        $attributeValue->update($data);
        return $attributeValue->fresh();
    }

    /**
     * Delete an attribute value.
     *
     * @param AttributeValue $attributeValue
     * @return bool
     */
    public function deleteAttributeValue(AttributeValue $attributeValue): bool
    {
        return $attributeValue->delete();
    }
}
