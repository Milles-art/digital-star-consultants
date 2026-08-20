<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the homepage displays active service categories from the database', function () {
    $category = ServiceCategory::create([
        'name' => 'Online & Government Services',
        'slug' => 'online-government-services',
        'is_active' => true,
    ]);

    Service::create([
        'service_category_id' => $category->id,
        'name' => 'NIDA Application Support',
        'slug' => 'nida-application-support',
        'is_active' => true,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Online & Government Services')
        ->assertSee('NIDA Application Support');
});

test('a browser can open an active service and view its submission form', function () {
    $category = ServiceCategory::create([
        'name' => 'Technology',
        'slug' => 'technology',
        'is_active' => true,
    ]);
    $service = Service::create([
        'service_category_id' => $category->id,
        'name' => 'Website Development',
        'slug' => 'website-development',
        'is_active' => true,
    ]);

    $this->get(route('public.services.show', $service->slug))
        ->assertOk()
        ->assertSee($service->name)
        ->assertSee('Start your request');
});
