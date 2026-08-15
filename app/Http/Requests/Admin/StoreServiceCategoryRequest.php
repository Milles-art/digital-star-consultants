<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin, ceo, gm can create categories
        return auth()->user() && auth()->user()->isManagement();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.max' => 'Category name cannot exceed 255 characters',
            'parent_id.exists' => 'Selected parent category does not exist',
            'color.max' => 'Color must be a valid hex code (e.g., #1E3A8A)',
        ];
    }
}
