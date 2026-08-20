<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use App\Models\User;

uses()->group('staff');

beforeEach(function () {
    $this->category = ServiceCategory::factory()->create();
    $this->service = Service::factory()->create([
        'service_category_id' => $this->category->id,
    ]);

    $this->staff = User::factory()->staff()->create();
    $this->otherStaff = User::factory()->staff()->create();
    $this->admin = User::factory()->admin()->create();
});

it('staff can only list submissions assigned to them', function () {
    $mine = Submission::factory()->assignedTo($this->staff)->create([
        'service_id' => $this->service->id,
    ]);
    $theirs = Submission::factory()->assignedTo($this->otherStaff)->create([
        'service_id' => $this->service->id,
    ]);

    $response = $this->actingAs($this->staff)
        ->getJson('/staff/submissions');

    $response->assertOk();

    $ids = collect($response->json('data.data') ?? $response->json('data'))
        ->pluck('id')
        ->all();

    expect($ids)->toContain($mine->id)
        ->and($ids)->not->toContain($theirs->id);
});

it('staff cannot view another staff members submission', function () {
    $theirs = Submission::factory()->assignedTo($this->otherStaff)->create([
        'service_id' => $this->service->id,
    ]);

    $this->actingAs($this->staff)
        ->getJson("/staff/submissions/{$theirs->id}")
        ->assertForbidden();
});

it('staff can view and complete their own assigned submission', function () {
    $mine = Submission::factory()->assignedTo($this->staff)->create([
        'service_id' => $this->service->id,
        'status' => Submission::STATUS_IN_PROGRESS,
    ]);

    $this->actingAs($this->staff)
        ->getJson("/staff/submissions/{$mine->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $mine->id);

    $this->actingAs($this->staff)
        ->postJson("/staff/submissions/{$mine->id}/complete")
        ->assertOk()
        ->assertJsonPath('status', 'success');

    expect($mine->fresh()->status)->toBe(Submission::STATUS_COMPLETED);
});

it('guest cannot access staff routes', function () {
    $this->getJson('/staff/submissions')->assertUnauthorized();
});

it('admin can access all submissions', function () {
    Submission::factory()->count(2)->create([
        'service_id' => $this->service->id,
    ]);

    $this->actingAs($this->admin)
        ->getJson('/admin/submissions')
        ->assertOk();
});
