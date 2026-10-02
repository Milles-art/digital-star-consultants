<?php

use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders an admin submission detail page for an authorized management user', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'is_active' => true,
    ]);

    $submission = Submission::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.submissions.show', $submission));

    $response->assertSuccessful();
    $response->assertViewIs('admin.submissions.show');
    $response->assertSee($submission->reference_number);
});
