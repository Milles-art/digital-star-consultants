<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceFieldRequest extends FormRequest
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
            'label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number,email,tel,date,time,datetime,select,checkbox,radio,file',
            'options' => 'nullable|array',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Field label is required',
            'label.max' => 'Label cannot exceed 255 characters',
            'field_type.required' => 'Please select a field type',
            'field_type.in' => 'Invalid field type selected',
            'sort_order.integer' => 'Sort order must be a number',
            'sort_order.min' => 'Sort order cannot be negative',
        ];
    }
}
