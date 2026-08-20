<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendNewSubmissionEmailJob;

uses()->group('public');

beforeEach(function () {
    $this->category = ServiceCategory::factory()->create();
    $this->service = Service::factory()->create([
        'service_category_id' => $this->category->id,
        'is_active' => true,
    ]);
});

it('allows anyone to submit a service request without login', function () {
    Queue::fake();

    $response = $this->postJson('/submit', [
        'service_id' => $this->service->id,
        'customer_name' => 'John Doe',
        'customer_phone' => '+255712345678',
        'customer_email' => 'john@example.com',
        'customer_notes' => 'Please call me',
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure([
            'data' => ['reference_number', 'status', 'customer_name', 'service_name'],
        ]);

    $this->assertDatabaseHas('submissions', [
        'customer_name' => 'John Doe',
        'customer_phone' => '+255712345678',
        'service_id' => $this->service->id,
        'status' => Submission::STATUS_PENDING,
    ]);

    Queue::assertPushed(SendNewSubmissionEmailJob::class);
});

it('rejects submission for inactive service', function () {
    $inactive = Service::factory()->inactive()->create([
        'service_category_id' => $this->category->id,
    ]);

    $response = $this->postJson('/submit', [
        'service_id' => $inactive->id,
        'customer_name' => 'John Doe',
        'customer_phone' => '+255712345678',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('status', 'error');
});

it('validates required fields on public submit', function () {
    $response = $this->postJson('/submit', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['service_id', 'customer_name', 'customer_phone']);
});

it('track endpoint returns limited data and no PII', function () {
    $submission = Submission::factory()->create([
        'service_id' => $this->service->id,
        'customer_name' => 'Jane Secret',
        'customer_phone' => '+255700000000',
        'customer_email' => 'secret@example.com',
        'staff_notes' => 'Internal note – must not leak',
    ]);

    $response = $this->getJson("/track/{$submission->reference_number}");

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.reference_number', $submission->reference_number)
        ->assertJsonPath('data.service_name', $this->service->name);

    // PII must NOT be present
    $response->assertJsonMissing([
        'customer_phone' => '+255700000000',
        'customer_email' => 'secret@example.com',
        'staff_notes' => 'Internal note – must not leak',
    ]);

    expect($response->json('data'))->not->toHaveKey('customer_phone')
        ->and($response->json('data'))->not->toHaveKey('customer_email')
        ->and($response->json('data'))->not->toHaveKey('staff_notes');
});

it('returns 404 for unknown track reference', function () {
    $this->getJson('/track/DSC-DOES-NOT-EXIST')
        ->assertNotFound()
        ->assertJsonPath('status', 'error');
});
