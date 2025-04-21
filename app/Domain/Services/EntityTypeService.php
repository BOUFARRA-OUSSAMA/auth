<?php

namespace App\Domain\Services;

use App\Infrastructure\Models\EntityType;
use App\Infrastructure\Repositories\EntityTypeRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EntityTypeService
{
    protected $entityTypeRepository;

    public function __construct(EntityTypeRepository $entityTypeRepository)
    {
        $this->entityTypeRepository = $entityTypeRepository;
    }

    /**
     * Get all entity types.
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getAllEntityTypes()
    {
        return $this->entityTypeRepository->getAllEntityTypes();
    }

    /**
     * Create a new entity type.
     *
     * @param array $data
     * @return EntityType
     */
    public function createEntityType(array $data): EntityType
    {
        return $this->entityTypeRepository->createEntityType($data);
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
        return $this->entityTypeRepository->updateEntityType($entityType, $data);
    }

    /**
     * Delete an entity type.
     *
     * @param EntityType $entityType
     * @return bool
     */
    public function deleteEntityType(EntityType $entityType): bool
    {
        return $this->entityTypeRepository->deleteEntityType($entityType);
    }

    /**
     * Get all attributes for an entity type.
     *
     * @param EntityType $entityType
     * @return Collection
     */
    public function getEntityTypeAttributes(EntityType $entityType): Collection
    {
        return $entityType->attributes;
    }
}
