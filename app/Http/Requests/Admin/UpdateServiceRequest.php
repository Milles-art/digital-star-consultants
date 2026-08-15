<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->isManagement();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'service_category_id' => 'nullable|exists:service_categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
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
            'service_category_id.exists' => 'Selected category does not exist',
            'name.max' => 'Service name cannot exceed 255 characters',
            'price.numeric' => 'Price must be a valid number',
            'price.min' => 'Price cannot be negative',
            'duration_minutes.integer' => 'Duration must be a valid number',
            'duration_minutes.min' => 'Duration must be at least 1 minute',
        ];
    }
}
