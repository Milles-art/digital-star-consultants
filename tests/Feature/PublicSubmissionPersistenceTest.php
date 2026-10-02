<?php

use App\Jobs\SendNewSubmissionEmailJob;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceField;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('persists dynamic answers, customer notes, and uploaded documents', function () {
    Queue::fake();
    Storage::fake('private');

    $category = ServiceCategory::factory()->create();
    $service = Service::factory()->create(['service_category_id' => $category->id, 'is_active' => true]);
    $passportField = ServiceField::create([
        'service_id' => $service->id,
        'label' => 'Passport Number',
        'field_key' => 'passport_number',
        'field_type' => 'text',
        'is_required' => true,
        'sort_order' => 1,
    ]);
    $documentField = ServiceField::create([
        'service_id' => $service->id,
        'label' => 'Current Passport',
        'field_key' => 'current_passport',
        'field_type' => 'file',
        'is_required' => true,
        'sort_order' => 2,
    ]);

    $response = $this->post('/submit', [
        'service_id' => $service->id,
        'customer_name' => 'John Doe',
        'customer_phone' => '+255712345678',
        'customer_email' => 'john@example.com',
        'preferred_date' => now()->addDay()->format('Y-m-d'),
        'customer_notes' => 'Please process urgently.',
        'fields' => [
            'passport_number' => 'AB123456',
            'current_passport' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
        ],
    ], ['Accept' => 'application/json']);

    $response->assertCreated();
    $submission = Submission::latest('id')->firstOrFail();

    expect($submission->customer_notes)->toBe('Please process urgently.');

    $answer = SubmissionFieldValue::where('submission_id', $submission->id)->where('service_field_id', $passportField->id)->first();
    expect($answer)->not->toBeNull()->and($answer->value)->toBe('AB123456');

    $document = SubmissionFieldValue::where('submission_id', $submission->id)->where('service_field_id', $documentField->id)->first();
    expect($document)->not->toBeNull()->and($document->file_path)->not->toBeNull();
    Storage::disk('private')->assertExists($document->file_path);

    Queue::assertPushed(SendNewSubmissionEmailJob::class);
});


it('keeps a row for every configured field and preserves the submitted option label', function () {
    Queue::fake();
    Storage::fake('private');

    $category = ServiceCategory::factory()->create();
    $service = Service::factory()->create(['service_category_id' => $category->id, 'is_active' => true]);
    $select = ServiceField::create([
        'service_id' => $service->id,
        'label' => 'Passport purpose',
        'field_key' => 'passport_purpose',
        'field_type' => 'radio',
        'options' => ['Travel', 'Work', 'Study'],
        'is_required' => true,
        'sort_order' => 1,
    ]);
    $blank = ServiceField::create([
        'service_id' => $service->id,
        'label' => 'Optional middle name',
        'field_key' => 'middle_name',
        'field_type' => 'text',
        'is_required' => false,
        'sort_order' => 2,
    ]);

    $response = $this->post('/submit', [
        'service_id' => $service->id,
        'customer_name' => 'Jane Doe',
        'customer_phone' => '+255712000111',
        'fields' => ['passport_purpose' => 'Work'],
    ], ['Accept' => 'application/json']);

    $response->assertCreated();
    $submission = Submission::latest('id')->firstOrFail();

    expect(SubmissionFieldValue::where('submission_id', $submission->id)->count())->toBe(2)
        ->and(SubmissionFieldValue::where('submission_id', $submission->id)->where('service_field_id', $select->id)->value('value'))->toBe('Work')
        ->and(SubmissionFieldValue::where('submission_id', $submission->id)->where('service_field_id', $blank->id)->value('value'))->toBeNull();
});
