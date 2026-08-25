<?php

use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use App\Models\User;

uses()->group('admin');

it('loads management admin pages for an authorized user', function () {
    $admin = User::factory()->admin()->create();
    $service = Service::factory()->create();
    Submission::factory()->for($service)->create();
    ContactMessage::factory()->create([
        'name' => 'Client Sender',
        'email' => 'client@example.com',
        'message' => 'I need help with a service request.',
    ]);

    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))->assertOk()->assertSee('Executive Dashboard');
    $this->get(route('admin.submissions.index'))->assertOk()->assertSee('Submissions');
    $this->get(route('admin.categories.index'))->assertOk()->assertSee('Service Categories');
    $this->get(route('admin.services.index'))->assertOk()->assertSee('Services');
    $this->get(route('admin.fields.index', $service))->assertOk()->assertSee('Form builder');
    $this->get(route('admin.users.index'))->assertOk()->assertSee('Team access');
    $this->get(route('admin.reports.index'))->assertOk()->assertSee('Reports');
    $this->get(route('admin.contact-messages.index'))->assertOk()->assertSee('Contact Messages');
});

it('keeps admin JSON resource endpoints available', function () {
    $admin = User::factory()->admin()->create();
    ServiceCategory::factory()->create();

    $this->actingAs($admin)
        ->getJson(route('admin.categories.index'))
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure(['data']);
});

it('lets staff view only their assigned queue and details', function () {
    $staff = User::factory()->staff()->create();
    $assigned = Submission::factory()->assignedTo($staff)->create();
    $other = Submission::factory()->create();

    $this->actingAs($staff)
        ->get(route('staff.submissions'))
        ->assertOk()
        ->assertSee($assigned->reference_number)
        ->assertDontSee($other->reference_number);

    $this->actingAs($staff)
        ->get(route('staff.submissions.show', $assigned))
        ->assertOk()
        ->assertSee($assigned->reference_number);
});

it('blocks staff from management admin pages', function () {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});
