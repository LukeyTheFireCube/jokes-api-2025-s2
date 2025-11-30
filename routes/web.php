<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\Web\VoteController;
use App\Http\Controllers\Admin\AdminRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StaticPageController::class, 'home'])
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])
            ->name('index');

        /* Users Admin Routes ----------------------------------------------------------- */
        Route::get('users', [AdminController::class, 'users'])
            ->name('users.index');

        Route::post('/users/{user}/force-logout', [AdminController::class, 'forceLogout'])
            ->name('users.force-logout');

        /* Categories Admin Routes ------------------------------------------------------ */

        Route::get('categories/trash', [AdminCategoryController::class, 'trash'])
            ->name('categories.trash');

        Route::delete('categories/trash/empty', [AdminCategoryController::class, 'removeAll'])
            ->name('categories.trash.remove.all');

        Route::post('categories/trash/recover', [AdminCategoryController::class, 'recoverAll'])
            ->name('categories.trash.recover.all');

        Route::delete('categories/trash/{id}/remove', [AdminCategoryController::class, 'removeOne'])
            ->name('categories.trash.remove.one');

        Route::post('categories/trash/{id}/recover', [AdminCategoryController::class, 'recoverOne'])
            ->name('categories.trash.recover.one');

        /** Stop people trying to "GET" admin/categories/trash/1234/delete or similar */
        Route::get('categories/trash/{id}/{method}', [AdminCategoryController::class, 'trash']);

        Route::resource("categories", AdminCategoryController::class);

        Route::post('categories/{category}/delete', [AdminCategoryController::class, 'delete'])
            ->name('categories.delete');

        Route::get('categories/{category}/delete', function () {
            return redirect()->route('admin.categories.index');
        });

        /* Roles */
        Route::resource('roles', AdminRoleController::class);

        Route::post('roles/{role}/delete', [AdminRoleController::class, 'delete'])
            ->name('roles.delete');

        Route::get('roles/{role}/delete', function () {
            return redirect()->route('admin.roles.index');
        });

        /* Jokes Admin Routes -------------------------------------------------------- */

        Route::get('jokes/trash', [\App\Http\Controllers\Admin\AdminJokeController::class, 'trash'])
            ->name('jokes.trash');

        Route::delete('jokes/trash/empty', [\App\Http\Controllers\Admin\AdminJokeController::class, 'removeAll'])
            ->name('jokes.trash.remove.all');

        Route::post('jokes/trash/recover', [\App\Http\Controllers\Admin\AdminJokeController::class, 'recoverAll'])
            ->name('jokes.trash.recover.all');

        Route::delete('jokes/trash/{id}/remove', [\App\Http\Controllers\Admin\AdminJokeController::class, 'removeOne'])
            ->name('jokes.trash.remove.one');

        Route::post('jokes/trash/{id}/recover', [\App\Http\Controllers\Admin\AdminJokeController::class, 'recoverOne'])
            ->name('jokes.trash.recover.one');

        /** Prevent direct GET access to sensitive paths */
        Route::get('jokes/trash/{id}/{method}', [\App\Http\Controllers\Admin\AdminJokeController::class, 'trash']);

        Route::resource('jokes', \App\Http\Controllers\Admin\AdminJokeController::class);

        Route::post('jokes/{joke}/delete', [\App\Http\Controllers\Admin\AdminJokeController::class, 'delete'])
            ->name('jokes.delete');

        Route::get('jokes/{joke}/delete', function () {
            return redirect()->route('admin.jokes.index');
        });
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/jokes/{joke}/vote', [VoteController::class, 'store'])->name('votes.store');
    Route::delete('/jokes/{joke}/vote', [VoteController::class, 'destroy'])->name('votes.destroy');
});


require __DIR__ . '/auth.php';
