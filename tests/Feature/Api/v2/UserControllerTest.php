<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Gate::define('editRole', fn($user) => true);
    Gate::define('forceLogout', fn($user) => true);
    Gate::define('editStatus', fn($user) => true);

    authUser();
});

test('lists users with pagination', function () {
    User::factory()->count(30)->create();

    $response = $this->getJson('/api/v2/users?perPage=10');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'current_page',
                'data' => [
                    '*' => ['id', 'name', 'email', 'roles']
                ],
                'per_page',
                'total',
            ],
        ]);
});

test('creates a new user', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'role' => 'client',
    ];

    $response = $this->postJson('/api/v2/users', $payload);

    $response->assertCreated()
        ->assertJson([
            'message' => 'User created successfully',
            'data' => ['name' => 'John Doe', 'email' => 'john@example.com']
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

test('shows a user', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/v2/users/{$user->id}");

    $response->assertOk()
        ->assertJson([
            'data' => ['id' => $user->id, 'email' => $user->email],
            'message' => 'User retrieved',
        ]);
});

test('updates a user', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword',
        'role' => 'admin',
    ];

    $response = $this->patchJson("/api/v2/users/{$user->id}", $payload);

    $response->assertOk()
        ->assertJson([
            'message' => 'User updated',
            'data' => ['name' => 'Updated Name', 'email' => 'updated@example.com']
        ]);

    $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);
});

test('deletes a user', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/v2/users/{$user->id}");

    $response->assertOk()
        ->assertJson(['message' => 'User deleted successfully']);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('force logs out a user', function () {
    $user = User::factory()->create(['remember_token' => 'abc123']);

    $response = $this->postJson("/api/v2/users/{$user->id}/force-logout");

    $response->assertOk()
        ->assertJson(['message' => 'User force-logged-out']);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'remember_token' => null]);
});

test('updates a user status', function () {
    $user = User::factory()->create(['status' => 'active']);

    $response = $this->postJson("/api/v2/users/{$user->id}/status", [
        'status' => 'banned'
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'User status updated to banned',
            'data' => ['id' => $user->id, 'status' => 'banned']
        ]);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'banned']);
});

