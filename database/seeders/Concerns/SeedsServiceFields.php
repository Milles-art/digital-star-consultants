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
     * @param  string|null  $slug  Pass an explicit slug when renaming a service
     *         in another language: the slug is the service's identity, so
     *         changing it would orphan existing submissions.
     */
    protected function seedService(
        int $categoryId,
        string $name,
        array $fields,
        ?string $description = null,
        int $sortOrder = 0,
        ?string $slug = null,
        ?int $price = null,
    ): Service {
        $profile = \App\Support\GovernmentServiceCatalogue::definition($slug ?? Str::slug($name));
        if ($profile) {
            $name = $profile['name'];
            $description = $profile['description'];
            $fields = $profile['fields'];
            if ($profile['category']) {
                $categoryId = \App\Models\ServiceCategory::firstOrCreate(
                    ['slug' => $profile['category']],
                    ['name' => 'Huduma za Biashara', 'is_active' => true, 'sort_order' => 2],
                )->id;
            }
        }

        $service = Service::updateOrCreate(
            ['slug' => $slug ?? Str::slug($name)],
            [
                'service_category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]
        );

        // The name is only refreshed when a slug was supplied explicitly, so a
        // renamed service never silently re-slugs itself on the next seed run.
        if ($slug !== null && $service->name !== $name) {
            $service->name = $name;
        }
        if ($price !== null && $service->price != $price) {
            $service->price = $price;
        }
        if ($service->isDirty()) {
            $service->save();
        }

        // Service::updating() regenerates the slug from the name whenever the
        // name changes. That breaks every existing /services/{slug} link, so an
        // explicitly supplied slug is re-asserted here and stays authoritative.
        if ($slug !== null && $service->slug !== $slug) {
            $service->slug = $slug;
            $service->save();
        }

        if ($profile) {
            $service->metadata = array_replace($service->metadata ?? [], [
                'government_requirements' => [
                    'version' => \App\Support\GovernmentServiceCatalogue::VERSION,
                    'reviewed_on' => '2026-11-06',
                    'sources' => $profile['sources'],
                    'document_sources' => $profile['document_sources'] ?? [],
                    'official_verification' => \App\Support\GovernmentRequirementEvidence::forService($slug ?? $service->slug),
                    'fields' => collect($fields)->mapWithKeys(fn ($field) => [
                        $field['field_key'] => [
                            'review_required' => $field['review_required'] ?? false,
                            'applies_when' => $field['applies_when'] ?? [],
                            'preparation_visible' => $field['preparation_visible'] ?? true,
                        ],
                    ])->all(),
                ],
            ]);
            $service->save();
        }

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
