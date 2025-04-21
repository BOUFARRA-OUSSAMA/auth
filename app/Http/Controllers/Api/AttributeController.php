<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Services\AttributeService;
use App\Infrastructure\Models\Attribute;
use App\Http\Requests\AttributeRequest;
use Illuminate\Http\JsonResponse;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * Display a listing of the attributes.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $entityTypeId = $request->query('entity_type_id');

        if ($entityTypeId) {
            $attributes = $this->attributeService->getAttributesByEntityType($entityTypeId);
        } else {
            $attributes = $this->attributeService->getAllAttributes();
        }

        return response()->json([
            'success' => true,
            'data' => $attributes
        ]);
    }

    /**
     * Store a newly created attribute.
     *
     * @param AttributeRequest $request
     * @return JsonResponse
     */
    public function store(AttributeRequest $request): JsonResponse
    {
        $attribute = $this->attributeService->createAttribute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute created successfully',
            'data' => $attribute
        ], 201);
    }

    /**
     * Display the specified attribute.
     *
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function show(Attribute $attribute): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $attribute
        ]);
    }

    /**
     * Update the specified attribute.
     *
     * @param AttributeRequest $request
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function update(AttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute = $this->attributeService->updateAttribute($attribute, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute updated successfully',
            'data' => $attribute
        ]);
    }

    /**
     * Remove the specified attribute.
     *
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $this->attributeService->deleteAttribute($attribute);

        return response()->json([
            'success' => true,
            'message' => 'Attribute deleted successfully'
        ]);
    }

    /**
     * Get all values for an attribute.
     *
     * @param Attribute $attribute
     * @return JsonResponse
     */
    public function values(Attribute $attribute): JsonResponse
    {
        $values = $this->attributeService->getAttributeValues($attribute);

        return response()->json([
            'success' => true,
            'data' => $values
        ]);
    }
}
