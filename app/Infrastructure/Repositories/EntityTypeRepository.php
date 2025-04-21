<?php

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Models\EntityType;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EntityTypeRepository
{
    /**
     * Get all entity types.
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getAllEntityTypes()
    {
        return EntityType::all();
    }

    /**
     * Create a new entity type.
     *
     * @param array $data
     * @return EntityType
     */
    public function createEntityType(array $data): EntityType
    {
        return EntityType::create($data);
    }

    /**
     * Update an existing entity type.
     *
     * @param EntityType $entityType
     * @param array $data
     * @return EntityType
     */
    public function updateEntityType(EntityType $entityType, array $data): EntityType
    {
        $entityType->update($data);
        return $entityType->fresh();
    }

    /**
     * Delete an entity type.
     *
     * @param EntityType $entityType
     * @return bool
     */
    public function deleteEntityType(EntityType $entityType): bool
    {
        return $entityType->delete();
    }
}
