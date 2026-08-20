<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'fields' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service',
            'service_id.exists' => 'Selected service does not exist',
            'customer_name.required' => 'Your name is required',
            'customer_phone.required' => 'Phone number is required',
            'customer_email.email' => 'Please enter a valid email address',
            'preferred_date.after_or_equal' => 'Preferred date cannot be in the past',
        ];
    }
}
