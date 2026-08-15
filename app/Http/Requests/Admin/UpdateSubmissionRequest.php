<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin, ceo, gm can update submissions
        return auth()->user() && auth()->user()->isManagement();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'total_price' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed,rejected,awaiting_customer,cancelled',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'customer_name.max' => 'Name cannot exceed 255 characters',
            'customer_phone.max' => 'Phone number cannot exceed 20 characters',
            'customer_email.email' => 'Please enter a valid email address',
            'preferred_date.date' => 'Please enter a valid date',
            'total_price.numeric' => 'Price must be a valid number',
            'total_price.min' => 'Price cannot be negative',
            'status.in' => 'Invalid status selected',
        ];
    }
}
