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
            'service_id.required' => __('site.form_errors.service_required'),
            'service_id.exists' => __('site.form_errors.service_invalid'),
            'customer_name.required' => __('site.form_errors.name_required'),
            'customer_phone.required' => __('site.form_errors.phone_required'),
            'customer_email.email' => __('site.contact.email_invalid'),
            'preferred_date.after_or_equal' => __('site.services.date_past'),
        ];
    }

    public function attributes(): array
    {
        return [
            'service_id' => __('site.services.title'),
            'customer_name' => __('site.services.detail.full_name'),
            'customer_phone' => __('site.services.detail.phone'),
            'customer_email' => __('site.services.detail.email'),
            'customer_notes' => __('site.services.notes'),
            'preferred_date' => __('site.services.detail.preferred_date'),
            'fields' => __('site.services.fields'),
        ];
    }
}
