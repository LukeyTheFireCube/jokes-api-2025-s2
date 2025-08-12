<?php

use \App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

const API_VER = 'v2';

uses(RefreshDatabase::class);

test('retrieve all categories', function () {
    // Arrange
    $categories = Category::factory(5)->create();

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
        ->assertJsonCount(5, 'data')
        ->assertJson($data);
});

test('retrieve one category', function () {
    // Arrange
    $categories = Category::factory(1)->create();

    $data = [
        'message' => "Category retrieved",
        'success' => true,
        'data' => $categories->toArray(),
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
