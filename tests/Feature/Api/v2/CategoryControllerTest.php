<?php

use \App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

const API_VER = 'v2';

uses(RefreshDatabase::class);

test('retrieve all categories', function () {
    authUser();
    // Arrange
    Category::factory(5)->create();
    $categories = Category::all();

    $data = [
        'message' => "Categories retrieved",
        'success' => true,
        'data' => $categories->toArray(),
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/categories');

    // Assert
    $response
        ->assertStatus(200)
        ->assertJsonCount($categories->count(), 'data')
        ->assertJson($data);
});

test('retrieve one category', function () {
    authUser();
    // Arrange
    Category::factory(1)->create();
    $categories = Category::all()->toArray();

    $data = [
        'success' => true,
        'message' => "Category retrieved",
        'data' => [$categories[0]],
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/categories/1');

    // Assert
    $response
        ->assertStatus(200)
        ->assertJson($data)
        ->assertJsonCount(1, 'data');
});


test('return error on missing category', function () {
    authUser();
    // Arrange
    $categories = Category::factory(1)->create();

    $data = [
        'message' => "Category not found",
        'success' => false,
        'data' => [],
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/categories/9999');

    // Assert
    $response
        ->assertStatus(404)
        ->assertJson($data)
        ->assertJsonCount(0, 'data');
});


test('create a new category', function () {
    authUser();
    // Arrange
    $data = [
        'title' => 'Fake Category',
        'description' => 'Fake Category Description',
    ];

    $dataResponse = [
        'message' => "Category created",
        'success' => true,
        'data' => $data
    ];

    // Act
    $response = $this->postJson('/api/' . API_VER . '/categories', $data);

    // Assert
    $response
        ->assertStatus(201)
        ->assertJson($dataResponse)
        ->assertJsonCount(5, 'data');
});


test('create category with title and description errors', function () {
    authUser();
    $data = [
        'title' => '',
        'description' => '1234',
    ];

    $response = $this->postJson('/api/' . API_VER . '/categories', $data);

    // 422 Unprocessable Entity
    // The HTTP 422 Unprocessable Entity status code means that while the server was able to interpret
    // the request sent, it is still not able to process it. The major issue here is when a server is
    // capable of interpreting a request, understanding its message, format, and structure, but still
    // cannot process due to some logical error.
    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'title',
            'description'
        ]);
});

test('create category title too short error', function () {
    authUser();
    $data = [
        'title' => '',
    ];

    $response = $this->postJson('/api/' . API_VER . '/categories', $data);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'title',
        ]);
});

test('create category description too short error', function () {
    authUser();
    $data = [
        'title' => 'This is a test category',
        'description' =>'short' // The description is too short
    ];

    $response = $this->postJson('/api/' . API_VER . '/categories', $data);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'description',
        ]);
});
