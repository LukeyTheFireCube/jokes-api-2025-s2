<?php

use App\Models\User;
use App\Models\Joke;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

const API_VER = 'v2';

uses(RefreshDatabase::class);

test('prevents guests from voting', function () {
    $joke = Joke::factory()->create();

    $response = $this->postJson('/api/' . API_VER . "/jokes/{$joke->id}/vote", ['value' => 1]);
    $response->assertStatus(401);
});

test('allows logged in users to add a vote', function () {
    authUser('client');
    $joke = Joke::factory()->create();

    $response = $this->postJson('/api/' . API_VER . "/jokes/{$joke->id}/vote", ['value' => 1]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('votes', [
        'user_id' => auth()->id(),
        'joke_id' => $joke->id,
        'value' => 1,
    ]);
});

test('allows logged in users to edit their vote', function () {
    authUser('client');
    $joke = Joke::factory()->create(['user_id' => auth()->id()]);

    // Create initial vote
    Vote::create([
        'user_id' => auth()->id(),
        'joke_id' => $joke->id,
        'value' => 1,
    ]);

    // Update vote
    $response = $this->postJson('/api/' . API_VER . "/jokes/{$joke->id}/vote", ['value' => -1]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('votes', [
        'user_id' => auth()->id(),
        'joke_id' => $joke->id,
        'value' => -1,
    ]);
});

test('allows logged in users to delete their vote', function () {
    authUser('client');
    $joke = Joke::factory()->create(['user_id' => auth()->id()]);

    // Create initial vote
    Vote::create([
        'user_id' => auth()->id(),
        'joke_id' => $joke->id,
        'value' => 1,
    ]);

    // Delete the vote
    $response = $this->deleteJson('/api/' . API_VER . "/jokes/{$joke->id}/vote");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('votes', [
        'user_id' => auth()->id(),
        'joke_id' => $joke->id,
    ]);
});
