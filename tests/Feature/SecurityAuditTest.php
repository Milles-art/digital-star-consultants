<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

uses()->group('security');

test('public tracking does not expose customer PII or submitted field values', function () {
    $category = ServiceCategory::factory()->create();
    $service = Service::factory()->create(['service_category_id' => $category->id, 'is_active' => true]);
    $submission = Submission::factory()->create([
        'service_id' => $service->id,
        'customer_name' => 'Secret Customer',
        'customer_phone' => '+255700000000',
        'customer_email' => 'secret@example.com',
        'staff_notes' => 'Internal note',
    ]);

    $this->getJson(route('public.submissions.track', $submission->reference_number))
        ->assertOk()
        ->assertJsonMissingPath('data.customer_phone')
        ->assertJsonMissingPath('data.customer_email')
        ->assertJsonMissingPath('data.staff_notes')
        ->assertJsonMissingPath('data.fields');
});

test('non-admin management cannot create or promote privileged management accounts', function () {
    $ceo = User::factory()->ceo()->create();

    $this->actingAs($ceo)
        ->postJson(route('admin.users.store'), [
            'name' => 'Escalated User',
            'email' => 'escalated@example.com',
            'role' => User::ROLE_ADMIN,
        ])
        ->assertForbidden();

    expect(User::where('email', 'escalated@example.com')->exists())->toBeFalse();
});

test('management cannot assign a request to an inactive account', function () {
    $admin = User::factory()->admin()->create();
    $inactiveStaff = User::factory()->staff()->inactive()->create();
    $category = ServiceCategory::factory()->create();
    $service = Service::factory()->create(['service_category_id' => $category->id]);
    $submission = Submission::factory()->create(['service_id' => $service->id]);

    $this->actingAs($admin)
        ->postJson(route('admin.submissions.assign', $submission), [
            'staff_id' => $inactiveStaff->id,
        ])
        ->assertStatus(422);
});

test('password reset request does not reveal whether an email exists', function () {
    $response = $this->postJson(route('password.email'), [
        'email' => 'does-not-exist@example.com',
    ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'If an account exists for that email, a password reset link has been sent.');
});

test('private uploaded files use the private storage disk', function () {
    expect(config('filesystems.disks.private.root'))->toContain('storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'private');
});
