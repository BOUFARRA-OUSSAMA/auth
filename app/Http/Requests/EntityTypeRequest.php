<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntityTypeRequest extends FormRequest
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
        // Basic validation rules that always apply
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:50|regex:/^[a-z0-9_-]+$/'
        ];

        // Add unique validation with an ignore rule if we have an ID
        // This works for both create and update scenarios
        $entityTypeId = $this->entityType ?? null;
        if ($entityTypeId) {
            $rules['code'] .= '|unique:entity_types,code,' . $entityTypeId;
        } else {
            $rules['code'] .= '|unique:entity_types,code';
        }

        return $rules;
    }
}
