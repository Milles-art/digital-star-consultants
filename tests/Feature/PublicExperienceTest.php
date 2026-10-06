<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\User;

function trackedRequest(string $reference): Submission
{
    $category = ServiceCategory::create(['name' => 'Business', 'slug' => 'business', 'is_active' => true]);
    $service = Service::create(['service_category_id' => $category->id, 'name' => 'Business registration', 'slug' => 'registration', 'is_active' => true]);
    return Submission::create(['reference_number' => $reference, 'service_id' => $service->id, 'customer_name' => 'Test Customer', 'customer_phone' => '0712345678']);
}

test('custom references track through the form and both endpoints after the prefix changes', function () {
    trackedRequest('STAR7-20261006-ABC123');
    Setting::put('operations.reference_prefix', 'NEW');
    $this->get('/track?reference=star7-20261006-abc123')
        ->assertRedirect(route('public.track.show', 'STAR7-20261006-ABC123'));
    $this->get('/track/status/STAR7-20261006-ABC123')->assertOk()->assertSee('Business registration');
    $this->getJson('/track/STAR7-20261006-ABC123')->assertOk()->assertJsonPath('data.reference_number', 'STAR7-20261006-ABC123');
});

test('original DSC references remain usable after changing the prefix', function () {
    trackedRequest('DSC-20261006-ABC123');
    Setting::put('operations.reference_prefix', 'NEW');
    $this->getJson('/track/DSC-20261006-ABC123')->assertOk();
    $this->get('/track/status/DSC-20261006-ABC123')->assertOk()->assertSee('Business registration');
});

test('invalid and partial tracking references return form validation errors', function () {
    $this->from('/track')->get('/track?reference=20261006-ABC123')->assertRedirect('/track')->assertSessionHasErrors('reference');
    $this->getJson('/track/STAR-20261006-ABC123-extra')->assertNotFound();
});

test('public visitors cannot create staff accounts', function () {
    $count = User::count();
    $this->get('/register')->assertNotFound();
    $this->postJson('/register', ['name' => 'Visitor', 'email' => 'visitor@example.com', 'role' => 'admin'])->assertNotFound();
    expect(User::count())->toBe($count);
});

test('swahili selection persists and changes rendered public navigation and content', function () {
    $this->get('/locale/sw')->assertRedirect()->assertSessionHas('locale', 'sw');
    $this->get('/')->assertOk()->assertSee('lang="sw"', false)->assertSee('Nyumbani');
    $this->get('/work')->assertOk()->assertSee('Kazi zetu');
    $this->get('/?lang=en')->assertOk()->assertSee('lang="en"', false)->assertSee('Your trusted digital partner');
});

test('unsupported locales fall back to english', function () {
    $this->get('/?lang=xx')->assertOk()->assertSee('lang="en"', false);
});

test('portfolio contains the supplied projects without eagerly loading video files', function () {
    $this->get('/work')->assertOk()->assertSee('Chichi Family')->assertSee('Wasafi Car Wash')->assertSee('ASMA Lingerie')->assertSee('preload="none"', false);
});
