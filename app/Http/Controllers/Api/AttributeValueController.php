<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Services\AttributeValueService;
use App\Infrastructure\Models\AttributeValue;
use App\Http\Requests\AttributeValueRequest;
use Illuminate\Http\JsonResponse;

class AttributeValueController extends Controller
{
    protected $attributeValueService;

    public function __construct(AttributeValueService $attributeValueService)
    {
        $this->attributeValueService = $attributeValueService;
    }

    /**
     * Display a listing of the attribute values.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $attributeId = $request->query('attribute_id');
        $entityType = $request->query('entity_type');
        $entityId = $request->query('entity_id');

        if ($attributeId && $entityType && $entityId) {
            // Get specific attribute value
            $values = $this->attributeValueService->getAttributeValue($attributeId, $entityType, $entityId);
        } elseif ($entityType && $entityId) {
            // Get all attribute values for an entity
            $values = $this->attributeValueService->getEntityAttributeValues($entityType, $entityId);
        } elseif ($attributeId) {
            // Get all values for an attribute
            $values = $this->attributeValueService->getAttributeAllValues($attributeId);
        } else {
            // Get all attribute values (paginated)
            $values = $this->attributeValueService->getAllAttributeValues();
        }

        return response()->json([
            'success' => true,
            'data' => $values
        ]);
    }

    /**
     * Store a newly created attribute value.
     *
     * @param AttributeValueRequest $request
     * @return JsonResponse
     */
    public function store(AttributeValueRequest $request): JsonResponse
    {
        $attributeValue = $this->attributeValueService->createAttributeValue($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute value created successfully',
            'data' => $attributeValue
        ], 201);
    }

    /**
     * Display the specified attribute value.
     *
     * @param AttributeValue $attributeValue
     * @return JsonResponse
     */
    public function show(AttributeValue $attributeValue): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $attributeValue
        ]);
    }

    /**
     * Update the specified attribute value.
     *
     * @param AttributeValueRequest $request
     * @param AttributeValue $attributeValue
     * @return JsonResponse
     */
    public function update(AttributeValueRequest $request, AttributeValue $attributeValue): JsonResponse
    {
        $attributeValue = $this->attributeValueService->updateAttributeValue($attributeValue, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute value updated successfully',
            'data' => $attributeValue
        ]);
    }

    /**
     * Remove the specified attribute value.
     *
     * @param AttributeValue $attributeValue
     * @return JsonResponse
     */
    public function destroy(AttributeValue $attributeValue): JsonResponse
    {
        $this->attributeValueService->deleteAttributeValue($attributeValue);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value deleted successfully'
        ]);
    }

    /**
     * Batch update attribute values for an entity.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
            'values' => 'required|array',
            'values.*.attribute_id' => 'required|exists:attributes,id',
            'values.*.value' => 'required|string',
        ]);

        $result = $this->attributeValueService->batchUpdateValues(
            $validated['entity_type'],
            $validated['entity_id'],
            $validated['values']
        );

        return response()->json([
            'success' => true,
            'message' => 'Attribute values updated successfully',
            'data' => $result
        ]);
    }
}
