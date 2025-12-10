<?php

use App\Models\Joke;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Laravel\Sanctum\Sanctum;

const API_VER = 'v2';

uses(RefreshDatabase::class);

/**
 * Helper: make a joke with categories
 */
function makeJoke($countCategories = 2): Joke {
    $joke = Joke::factory()->create();
    $categories = Category::factory()->count($countCategories)->create();
    $joke->categories()->sync($categories->pluck('id'));
    return $joke;
}

/*----------------------------------------------------------------------
|  INDEX
----------------------------------------------------------------------*/
test('can list jokes', function () {

    Joke::factory(3)->create();
    $jokes = Joke::with('categories', 'user')->get();

    $data = [
        'message' => "Jokes retrieved",
        'success' => true,
        'data' => $jokes->toArray(),
    ];

    $response = $this->getJson('/api/' . API_VER . '/jokes');

    $response
        ->assertStatus(200)
        ->assertJsonCount($jokes->count(), 'data')
        ->assertJson($data);
});

/*----------------------------------------------------------------------
|  STORE
----------------------------------------------------------------------*/
test('can create a joke', function () {
    authUser();
    $categories = Category::factory()->count(2)->create();

    $payload = [
        'title' => 'New Joke Title',
        'content' => 'Funny content here',
        'categories' => $categories->pluck('id')->toArray(),
    ];

    $response = $this->postJson('/api/' . API_VER . '/jokes', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'New Joke Title');

    $this->assertDatabaseHas('jokes', [
        'title' => 'New Joke Title'
    ]);

    $this->assertDatabaseCount('category_joke', 5);
});

/*----------------------------------------------------------------------
|  SHOW
----------------------------------------------------------------------*/
test('retrieve a single joke', function () {
    authUser();
    $joke = makeJoke();

    $response = $this->getJson("/api/" . API_VER . "/jokes/{$joke->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $joke->id);
});

test('returns 404 for non-existent joke', function () {
    authUser();
    $response = $this->getJson('/api/' . API_VER . '/jokes/999999');

    $response->assertStatus(404);
});

/*----------------------------------------------------------------------
|  UPDATE
----------------------------------------------------------------------*/
test('can update a joke', function () {
    authUser();
    $joke = makeJoke();
    $newCategories = Category::factory()->count(1)->create();

    $payload = [
        'title' => 'Updated Joke Title',
        'categories' => $newCategories->pluck('id')->toArray(),
    ];

    $response = $this->putJson("/api/" . API_VER . "/jokes/{$joke->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Joke Title');

    $this->assertDatabaseHas('jokes', [
        'id' => $joke->id,
        'title' => 'Updated Joke Title'
    ]);
});

/*----------------------------------------------------------------------
|  DESTROY (soft delete)
----------------------------------------------------------------------*/
test('can soft delete a joke', function () {
    authUser();
    $joke = makeJoke();

    $response = $this->deleteJson("/api/" . API_VER . "/jokes/{$joke->id}");

    $response->assertOk();
    $this->assertSoftDeleted('jokes', ['id' => $joke->id]);
});

/*----------------------------------------------------------------------
|  TRASH LIST
----------------------------------------------------------------------*/
test('can list trashed jokes', function () {
    authUser();
    $joke = makeJoke();
    $joke->delete();

    $response = $this->getJson('/api/' . API_VER . '/jokes/trash');

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => []
        ]);
});

/*----------------------------------------------------------------------
|  RECOVER ONE
----------------------------------------------------------------------*/
test('can restore one joke from trash', function () {
    authUser();
    $joke = makeJoke();
    $joke->delete();

    $response = $this->postJson("/api/" . API_VER . "/jokes/trash/{$joke->id}/recover");

    $response->assertOk();
    $this->assertDatabaseHas('jokes', [
        'id' => $joke->id,
        'deleted_at' => null,
    ]);
});

/*----------------------------------------------------------------------
|  REMOVE ONE (force delete)
----------------------------------------------------------------------*/
test('can force delete one joke from trash', function () {
    authUser();
    $joke = makeJoke();
    $joke->delete();

    $response = $this->deleteJson("/api/" . API_VER . "/jokes/trash/{$joke->id}/remove");

    $response->assertOk();

    $this->assertDatabaseMissing('jokes', [
        'id' => $joke->id,
    ]);
});

/*----------------------------------------------------------------------
|  RECOVER ALL
----------------------------------------------------------------------*/
test('can recover all trashed jokes', function () {
    authUser();
    $jokes = Joke::factory()->count(3)->create();
    Joke::query()->delete();

    $response = $this->postJson('/api/' . API_VER . '/jokes/trash/recover');

    $response->assertOk();
    $this->assertDatabaseCount('jokes', 5);
});

/*----------------------------------------------------------------------
|  REMOVE ALL (force delete all)
----------------------------------------------------------------------*/
test('can force delete all trashed jokes', function () {
    authUser();
    $jokes = Joke::factory()->count(3)->create();
    Joke::query()->delete();

    $response = $this->deleteJson('/api/' . API_VER . '/jokes/trash/empty');

    $response->assertOk();
    $this->assertDatabaseCount('jokes', 0);
});

