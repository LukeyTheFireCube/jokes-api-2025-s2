<?php

use App\Http\Controllers\Api\v1\AuthController as AuthControllerV1;
use App\Http\Controllers\Api\v2\CategoryController as CategoryControllerV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Version 2 Routes
 */

/**
 * User API Routes
 * - Register, Login (no authentication)
 * - Profile, Logout, User details (authentication required)
 */

Route::prefix('auth')
    ->group(function () {
        Route::post('register', [AuthControllerV1::class, 'register']);
        Route::post('login', [AuthControllerV1::class, 'login']);

        Route::get('profile', [AuthControllerV1::class, 'profile'])
            ->middleware(['auth:sanctum',]);
        Route::post('logout', [AuthControllerV1::class, 'logout'])
            ->middleware(['auth:sanctum',]);

    });


Route::ApiResource('categories', CategoryControllerV2::class);
