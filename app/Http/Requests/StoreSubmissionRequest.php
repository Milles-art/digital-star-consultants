<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|integer|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_notes' => 'nullable|string|max:2000',
            'preferred_date' => 'nullable|date',
            'fields' => 'nullable|array|max:100',
        ];
    }

    /**
     * Attribute names shown in validation messages (public form wizard).
     */
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

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => __('site.form_errors.service_required'),
            'service_id.exists' => __('site.form_errors.service_invalid'),
            'customer_name.required' => __('site.form_errors.name_required'),
            'customer_phone.required' => __('site.form_errors.phone_required'),
        ];
    }
}
