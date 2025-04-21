<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $attributeTypes = [
            'text',
            'textarea',
            'integer',
            'decimal',
            'boolean',
            'date',
            'datetime',
            'select',
            'multiselect',
            'file',
            'image',
            'string'
        ];

        // Basic validation rules that always apply
        $rules = [
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in($attributeTypes)],
            'description' => 'nullable|string',
            'is_required' => 'nullable|boolean',
            'entity_type_id' => 'required|exists:entity_types,id',
            'code' => 'required|string|max:50|regex:/^[a-z0-9_-]+$/'
        ];

        // Add unique validation with an ignore rule if we have an ID
        // This works for both create and update scenarios
        $attributeId = $this->attribute ?? null;
        if ($attributeId) {
            $rules['code'] .= '|unique:attributes,code,' . $attributeId;
        } else {
            $rules['code'] .= '|unique:attributes,code';
        }

        return $rules;
    }
}
