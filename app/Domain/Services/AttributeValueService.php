<?php

namespace App\Domain\Services;

use App\Infrastructure\Models\AttributeValue;
use App\Infrastructure\Repositories\AttributeValueRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeValueService
{
    protected $attributeValueRepository;

    public function __construct(AttributeValueRepository $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Get all attribute values (paginated).
     *
     * @return LengthAwarePaginator
     */
    public function getAllAttributeValues()
    {
        return $this->attributeValueRepository->getAllAttributeValues();
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
        return $this->attributeValueRepository->getAttributeValue($attributeId, $entityType, $entityId);
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
        return $this->attributeValueRepository->getEntityAttributeValues($entityType, $entityId);
    }

    /**
     * Get all values for an attribute.
     *
     * @param int $attributeId
     * @return Collection
     */
    public function getAttributeAllValues(int $attributeId): Collection
    {
        return $this->attributeValueRepository->getAttributeAllValues($attributeId);
    }

    /**
     * Create a new attribute value.
     *
     * @param array $data
     * @return AttributeValue
     */
    public function createAttributeValue(array $data): AttributeValue
    {
        return $this->attributeValueRepository->createAttributeValue($data);
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
        return $this->attributeValueRepository->updateAttributeValue($attributeValue, $data);
    }

    /**
     * Delete an attribute value.
     *
     * @param AttributeValue $attributeValue
     * @return bool
     */
    public function deleteAttributeValue(AttributeValue $attributeValue): bool
    {
        return $this->attributeValueRepository->deleteAttributeValue($attributeValue);
    }

    /**
     * Batch update attribute values for an entity.
     *
     * @param string $entityType
     * @param int $entityId
     * @param array $values
     * @return array
     */
    public function batchUpdateValues(string $entityType, int $entityId, array $values): array
    {
        $results = [];

        foreach ($values as $valueData) {
            $attributeId = $valueData['attribute_id'];
            $value = $valueData['value'];

            $attributeValue = $this->attributeValueRepository->getAttributeValue($attributeId, $entityType, $entityId);

            if ($attributeValue) {
                // Update existing value
                $attributeValue = $this->attributeValueRepository->updateAttributeValue($attributeValue, [
                    'value' => $value
                ]);
            } else {
                // Create new value
                $attributeValue = $this->attributeValueRepository->createAttributeValue([
                    'attribute_id' => $attributeId,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'value' => $value
                ]);
            }

            $results[] = $attributeValue;
        }

        return $results;
    }
}
