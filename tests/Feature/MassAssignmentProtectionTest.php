<?php

use App\Models\User;
use App\Models\Submission;
use App\Models\Service;
use App\Models\ServiceCategory;

uses()->group('security');

it('does not mass-assign role on User', function () {
    $user = User::factory()->create();

    // Attempt mass assignment of privileged fields
    $user->fill([
        'name' => 'Updated Name',
        'role' => User::ROLE_ADMIN,
        'is_active' => false,
    ]);

    expect($user->role)->not->toBe(User::ROLE_ADMIN)
        ->and($user->is_active)->toBeTrue(); // original value preserved because not fillable
});

it('does not mass-assign status or processed_by on Submission', function () {
    $category = ServiceCategory::factory()->create();
    $service = Service::factory()->create(['service_category_id' => $category->id]);
    $staff = User::factory()->staff()->create();

    $submission = Submission::factory()->create([
        'service_id' => $service->id,
        'status' => Submission::STATUS_PENDING,
    ]);

    $submission->fill([
        'customer_name' => 'New Name',
        'status' => Submission::STATUS_COMPLETED,
        'processed_by' => $staff->id,
        'staff_notes' => 'Hacked note',
    ]);

    expect($submission->status)->toBe(Submission::STATUS_PENDING)
        ->and($submission->processed_by)->toBeNull()
        ->and($submission->staff_notes)->toBeNull()
        ->and($submission->customer_name)->toBe('New Name'); // allowed field
});

it('admin create user does not return temp password', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->postJson('/admin/users', [
            'name' => 'New Staff',
            'email' => 'newstaff@example.com',
            'role' => User::ROLE_STAFF,
        ]);

    $response->assertCreated()
        ->assertJsonPath('status', 'success');

    // Temp password must never appear in the response
    expect($response->json('data'))->not->toHaveKey('temp_password')
        ->and($response->json())->not->toHaveKey('temp_password');
});
