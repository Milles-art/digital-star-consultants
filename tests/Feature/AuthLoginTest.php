<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

uses()->group('auth');

it('allows active staff to login', function () {
    $user = User::factory()->staff()->create([
        'email' => 'staff@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/login', [
        'email' => 'staff@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.user.role', User::ROLE_STAFF);

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    User::factory()->create([
        'email' => 'staff@example.com',
        'password' => bcrypt('password123'),
    ]);

    $this->postJson('/login', [
        'email' => 'staff@example.com',
        'password' => 'wrong-password',
    ])->assertUnauthorized()
        ->assertJsonPath('status', 'error');
});

it('rejects inactive users', function () {
    User::factory()->inactive()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password123'),
    ]);

    $this->postJson('/login', [
        'email' => 'inactive@example.com',
        'password' => 'password123',
    ])->assertForbidden()
        ->assertJsonPath('status', 'error');
});

it('rate limits login after too many attempts', function () {
    $email = 'bruteforce@example.com';
    User::factory()->create([
        'email' => $email,
        'password' => bcrypt('password123'),
    ]);

    // Hit the limiter 5 times
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/login', [
            'email' => $email,
            'password' => 'wrong',
        ]);
    }

    $this->postJson('/login', [
        'email' => $email,
        'password' => 'wrong',
    ])->assertStatus(429);
});
