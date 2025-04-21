<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Models\Attribute;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeRepository
{
    /**
     * Get all attributes.
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getAllAttributes()
    {
        return Attribute::all();
    }

    /**
     * Get attributes by entity type.
     *
     * @param int $entityTypeId
     * @return Collection|LengthAwarePaginator
     */
    public function getAttributesByEntityType(int $entityTypeId)
    {
        return Attribute::where('entity_type_id', $entityTypeId)->get();
    }

    /**
     * Create a new attribute.
     *
     * @param array $data
     * @return Attribute
     */
    public function createAttribute(array $data): Attribute
    {
        return Attribute::create($data);
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
        $attribute->update($data);
        return $attribute->fresh();
    }

    /**
     * Delete an attribute.
     *
     * @param Attribute $attribute
     * @return bool
     */
    public function deleteAttribute(Attribute $attribute): bool
    {
        return $attribute->delete();
    }
}
