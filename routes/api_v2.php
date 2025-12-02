<?php

use App\Http\Controllers\Api\v1\AuthController as AuthControllerV1;
use App\Http\Controllers\Api\v2\UserController as UserControllerV2;
use App\Http\Controllers\Api\v2\CategoryController as CategoryControllerV2;
use App\Http\Controllers\Api\v2\RoleController as RoleControllerV2;
use App\Http\Controllers\Api\v2\JokeController as JokeControllerV2;
use App\Http\Controllers\Api\v2\VoteController as VoteControllerV2;
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

/* Users Routes ------------------------------------------------------ */
Route::resource("users", UserControllerV2::class);

Route::post('users/{user}/delete', [UserControllerV2::class, 'delete'])
    ->name('users.delete');

Route::get('users/{user}/delete', function () {
    return redirect()->route('admin.users.index');
});

Route::post('/users/{user}/force-logout', [UserControllerV2::class, 'forceLogout'])
    ->name('users.force-logout');

/* Categories Routes ------------------------------------------------------ */
Route::get('categories/trash', [CategoryControllerV2::class, 'trash'])
    ->name('categories.trash');

Route::delete('categories/trash/empty', [CategoryControllerV2::class, 'removeAll'])
    ->name('categories.trash.remove.all');

Route::post('categories/trash/recover', [CategoryControllerV2::class, 'recoverAll'])
    ->name('categories.trash.recover.all');

Route::delete('categories/trash/{id}/remove', [CategoryControllerV2::class, 'removeOne'])
    ->name('categories.trash.remove.one');

Route::post('categories/trash/{id}/recover', [CategoryControllerV2::class, 'recoverOne'])
    ->name('categories.trash.recover.one');

/** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
Route::get('categories/trash/{id}/{method}', [CategoryControllerV2::class, 'trash']);

Route::resource("categories", CategoryControllerV2::class);

Route::post('categories/{category}/delete', [CategoryControllerV2::class, 'delete'])
    ->name('categories.delete');

Route::get('categories/{category}/delete', function () {
    return redirect()->route('admin.categories.index');
});

/* Roles Routes */
Route::resource('roles', RoleControllerV2::class);

/* Jokes Routes ------------------------------------------------------ */
Route::get('jokes/trash', [JokeControllerV2::class, 'trash'])
    ->name('jokes.trash');

Route::delete('jokes/trash/empty', [JokeControllerV2::class, 'removeAll'])
    ->name('jokes.trash.remove.all');

Route::post('jokes/trash/recover', [JokeControllerV2::class, 'recoverAll'])
    ->name('jokes.trash.recover.all');

Route::delete('jokes/trash/{id}/remove', [JokeControllerV2::class, 'removeOne'])
    ->name('jokes.trash.remove.one');

Route::post('jokes/trash/{id}/recover', [JokeControllerV2::class, 'recoverOne'])
    ->name('jokes.trash.recover.one');

/** Prevent GET misuse like /jokes/trash/123/delete */
Route::get('jokes/trash/{id}/{method}', [JokeControllerV2::class, 'trash']);

Route::resource("jokes", JokeControllerV2::class);

Route::post('jokes/{joke}/delete', [JokeControllerV2::class, 'delete'])
    ->name('jokes.delete');

Route::get('jokes/{joke}/delete', function () {
    return redirect()->route('jokes.index');
});

/* Vote Routes */
Route::get('/jokes', function () {
    return "";
})->name('jokes.voteplaceholder');
Route::post('/jokes/{joke}/vote', [VoteControllerV2::class, 'store'])->name('votes.store');
Route::delete('/jokes/{joke}/vote', [VoteControllerV2::class, 'destroy'])->name('votes.destroy');
