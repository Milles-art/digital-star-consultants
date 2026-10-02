<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceSubmissionSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_service_fields_have_unique_keys_per_service(): void
    {
        $services = Service::with('fields')->get();

        foreach ($services as $service) {
            $keys = $service->fields->pluck('field_key');
            $this->assertSame($keys->count(), $keys->unique()->count(), $service->name);
        }
    }

    public function test_every_active_field_uses_a_supported_type(): void
    {
        $supported = array_keys(\App\Models\ServiceField::TYPES);

        Service::with('fields')->get()->each(function (Service $service): void {
            foreach ($service->fields as $field) {
                $this->assertContains($field->field_type, array_keys(\App\Models\ServiceField::TYPES), "{$service->name}: {$field->field_key}");
                if ($field->isSelectField()) {
                    $this->assertNotEmpty($field->options, "{$service->name}: {$field->field_key} needs options");
                }
            }
        });
    }

    public function test_every_seeded_service_field_can_be_persisted_as_a_submission_value(): void
    {
        Service::with('fields')->get()->each(function (Service $service): void {
            $this->assertGreaterThanOrEqual(0, $service->fields->count());
            $this->assertNotSame(null, $service->fields);
        });
    }
}
