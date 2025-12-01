<?php

use App\Models\User;
use App\Models\Joke;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->joke = Joke::factory()->create();
});

test('prevents guests from voting', function () {
    $response = $this->postJson(route('votes.store', $this->joke), ['value' => 1]);
    $response->assertStatus(401);
});

test('allows logged in users to add a vote', function () {
    $this->actingAs($this->user);

    $response = $this->postJson(route('votes.store', $this->joke), ['value' => 1]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('votes', [
        'user_id' => $this->user->id,
        'joke_id' => $this->joke->id,
        'value' => 1,
    ]);
});

test('allows logged in users to edit their vote', function () {
    $this->actingAs($this->user);

    // First vote
    $this->postJson(route('votes.store', $this->joke), ['value' => 1]);

    // Update vote
    $response = $this->postJson(route('votes.store', $this->joke), ['value' => -1]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('votes', [
        'user_id' => $this->user->id,
        'joke_id' => $this->joke->id,
        'value' => -1,
    ]);
});

test('allows logged in users to delete their vote', function () {
    $this->actingAs($this->user);

    // Add a vote first
    $this->postJson(route('votes.store', $this->joke), ['value' => 1]);

    // Delete the vote
    $response = $this->deleteJson(route('votes.destroy', $this->joke));

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('votes', [
        'user_id' => $this->user->id,
        'joke_id' => $this->joke->id,
    ]);
});
