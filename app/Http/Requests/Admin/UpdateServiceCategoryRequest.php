<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->isManagement();
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id|different:id',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Category name cannot exceed 255 characters',
            'parent_id.exists' => 'Selected parent category does not exist',
            'color.max' => 'Color must be a valid hex code (e.g., #1E3A8A)',
        ];
    }
}
