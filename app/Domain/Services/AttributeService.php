<?php

namespace App\Domain\Services;

use App\Infrastructure\Models\Attribute;
use App\Infrastructure\Repositories\AttributeRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeService
{
    protected $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Get all attributes.
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getAllAttributes()
    {
        return $this->attributeRepository->getAllAttributes();
    }

    /**
     * Get attributes by entity type.
     *
     * @param int $entityTypeId
     * @return Collection|LengthAwarePaginator
     */
    public function getAttributesByEntityType(int $entityTypeId)
    {
        return $this->attributeRepository->getAttributesByEntityType($entityTypeId);
    }

    /**
     * Create a new attribute.
     *
     * @param array $data
     * @return Attribute
     */
    public function createAttribute(array $data): Attribute
    {
        return $this->attributeRepository->createAttribute($data);
    }

    /**
     * Update an existing attribute.
     *
     * @param Attribute $attribute
     * @param array $data
     * @return Attribute
     */
    public function updateAttribute(Attribute $attribute, array $data): Attribute
    {
        return $this->attributeRepository->updateAttribute($attribute, $data);
    }

    /**
     * Delete an attribute.
     *
     * @param Attribute $attribute
     * @return bool
     */
    public function deleteAttribute(Attribute $attribute): bool
    {
        return $this->attributeRepository->deleteAttribute($attribute);
    }

    /**
     * Get all values for an attribute.
     *
     * @param Attribute $attribute
     * @return Collection
     */
    public function getAttributeValues(Attribute $attribute): Collection
    {
        return $attribute->values;
    }
}