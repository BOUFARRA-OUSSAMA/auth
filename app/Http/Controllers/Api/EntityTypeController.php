<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Services\EntityTypeService;
use App\Infrastructure\Models\EntityType;
use App\Http\Requests\EntityTypeRequest;
use Illuminate\Http\JsonResponse;

class EntityTypeController extends Controller
{
    protected $entityTypeService;

    public function __construct(EntityTypeService $entityTypeService)
    {
        $this->entityTypeService = $entityTypeService;
    }

    /**
     * Display a listing of the entity types.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $entityTypes = $this->entityTypeService->getAllEntityTypes();

        return response()->json([
            'success' => true,
            'data' => $entityTypes
        ]);
    }

    /**
     * Store a newly created entity type.
     *
     * @param EntityTypeRequest $request
     * @return JsonResponse
     */
    public function store(EntityTypeRequest $request): JsonResponse
    {
        $entityType = $this->entityTypeService->createEntityType($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Entity type created successfully',
            'data' => $entityType
        ], 201);
    }

    /**
     * Display the specified entity type.
     *
     * @param EntityType $entityType
     * @return JsonResponse
     */
    public function show(EntityType $entityType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $entityType
        ]);
    }

    /**
     * Update the specified entity type.
     *
     * @param EntityTypeRequest $request
     * @param EntityType $entityType
     * @return JsonResponse
     */
    public function update(EntityTypeRequest $request, EntityType $entityType): JsonResponse
    {
        $entityType = $this->entityTypeService->updateEntityType($entityType, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Entity type updated successfully',
            'data' => $entityType
        ]);
    }

    /**
     * Remove the specified entity type.
     *
     * @param EntityType $entityType
     * @return JsonResponse
     */
    public function destroy(EntityType $entityType): JsonResponse
    {
        $this->entityTypeService->deleteEntityType($entityType);

        return response()->json([
            'success' => true,
            'message' => 'Entity type deleted successfully'
        ]);
    }

    /**
     * Get all attributes for an entity type.
     *
     * @param EntityType $entityType
     * @return JsonResponse
     */
    public function attributes(EntityType $entityType): JsonResponse
    {
        $attributes = $this->entityTypeService->getEntityTypeAttributes($entityType);

        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }
}
