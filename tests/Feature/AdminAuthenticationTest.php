<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

test('management login page is reachable', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertViewIs('auth.admin-login');
});

test('management can login and is redirected to the admin dashboard', function () {
    $user = User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $this->post('/admin/login', [
        'email' => 'ADMIN@example.com',
        'password' => 'CorrectPassword123!',
    ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('invalid management credentials are rejected without json server errors', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $this->from('/admin/login')
        ->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('staff cannot use the management login', function () {
    User::factory()->staff()->create([
        'email' => 'staff@example.com',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $this->from('/admin/login')
        ->post('/admin/login', [
            'email' => 'staff@example.com',
            'password' => 'CorrectPassword123!',
        ])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('inactive management accounts cannot login', function () {
    User::factory()->admin()->inactive()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $this->from('/admin/login')
        ->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'CorrectPassword123!',
        ])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('management logout returns to management login', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $this->post('/admin/logout')
        ->assertRedirect('/admin/login');

    $this->assertGuest();
});
