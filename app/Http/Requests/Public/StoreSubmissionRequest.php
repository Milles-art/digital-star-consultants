<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Public access - anyone can submit
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string',
            'preferred_date' => 'nullable|date',
            'fields' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service',
            'service_id.exists' => 'Selected service does not exist',
            'customer_name.required' => 'Your name is required',
            'customer_name.max' => 'Name cannot exceed 255 characters',
            'customer_phone.required' => 'Phone number is required',
            'customer_phone.max' => 'Phone number cannot exceed 20 characters',
            'customer_email.email' => 'Please enter a valid email address',
            'preferred_date.date' => 'Please enter a valid date',
        ];
    }
}
