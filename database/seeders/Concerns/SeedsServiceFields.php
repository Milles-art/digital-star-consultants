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
     * Create (or update) a service under a category and replace its fields.
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

        // Re-running the seeder shouldn't leave stale/duplicate fields
        // behind if the field list changed since last run.
        $service->fields()->delete();

        foreach (array_values($fields) as $order => $field) {
            ServiceField::create($field + [
                'service_id' => $service->id,
                'is_required' => $field['is_required'] ?? true,
                'sort_order' => $order,
            ]);
        }

        return $service;
    }
}
