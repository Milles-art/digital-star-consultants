<?php

namespace Database\Seeders\Concerns;

use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Support\Str;

trait SeedsServiceFields
{
    /**
     * Phone + Email — repeated on almost every service, worth a template.
     * Most other fields differ enough per service that writing them inline
     * is clearer than forcing them into shared templates.
     */
    protected function contactFields(): array
    {
        return [
            ['label' => 'Phone Number', 'field_key' => 'phone_number', 'field_type' => 'tel'],
            ['label' => 'Email', 'field_key' => 'email', 'field_type' => 'email', 'is_required' => false],
        ];
    }

    /**
     * Create (or update) a service under a category and reconcile its fields.
     *
     * @param  array<int, array<string, mixed>>  $fields  flat list of field
     *         defs, e.g. ['label' => ..., 'field_key' => ..., 'field_type' => ...]
     *         Use `...$this->contactFields()` to splice in a template.
     */
    protected function seedService(
        int $categoryId,
        string $name,
        array $fields,
        ?string $description = null,
        int $sortOrder = 0,
    ): Service {
        $service = Service::updateOrCreate(
            ['slug' => Str::slug($name)],
            [
                'service_category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]
        );

        // Never delete field records here: submission_field_values reference
        // service_fields and deletion would destroy historical customer data.
        // Reconcile by stable (service_id + field_key), archive removed fields,
        // and reactivate/update the fields that belong to the current service form.
        ServiceField::where('service_id', $service->id)->update(['is_active' => false]);

        foreach (array_values($fields) as $order => $field) {
            $payload = [
                'label' => $field['label'],
                'field_type' => $field['field_type'] ?? 'text',
                'options' => $field['options'] ?? null,
                'placeholder' => $field['placeholder'] ?? null,
                'help_text' => $field['help_text'] ?? null,
                'default_value' => $field['default_value'] ?? null,
                'is_required' => $field['is_required'] ?? true,
                'sort_order' => $order,
                'is_active' => true,
            ];

            ServiceField::updateOrCreate(
                ['service_id' => $service->id, 'field_key' => $field['field_key']],
                $payload
            );
        }

        return $service;
    }
}
