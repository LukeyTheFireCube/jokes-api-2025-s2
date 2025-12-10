<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJokeRequest;
use App\Http\Requests\UpdateJokeRequest;
use App\Models\Joke;
use App\Models\Category;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class JokeController extends Controller
{
    /**
     * Display a listing of jokes.
     */
    public function index(): JsonResponse
    {
        $jokes = Joke::with('categories', 'user')->get();

        return ApiResponse::success($jokes, "Jokes retrieved");
    }

    /**
     * Store a newly created joke.
     */
    public function store(StoreJokeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $joke = Joke::create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'user_id' => auth()->id(),
            'published_at' => $validated['published_at'] ?? null,
        ]);

        if (array_key_exists('categories', $validated)) {
            $joke->categories()->sync($validated['categories']);
        }

        return ApiResponse::success($joke->load('categories'), "Joke created", 201);
    }

    /**
     * Display the specified joke.
     */
    public function show(string $id): JsonResponse
    {
        $joke = Joke::with('categories', 'user')->find($id);

        if (!$joke) {
            return ApiResponse::error(null, "Joke not found", 404);
        }

        return ApiResponse::success($joke, "Joke retrieved");
    }

    /**
     * Update the specified joke in storage.
     */
    public function update(UpdateJokeRequest $request, string $id): JsonResponse
    {
        $joke = Joke::find($id);

        if (!$joke) {
            return ApiResponse::error(null, "Joke not found", 404);
        }

        $validated = $request->validated();

        $joke->update([
            'title' => $validated['title'] ?? $joke->title,
            'content' => $validated['content'] ?? $joke->content,
            'user_id' => $joke->user_id,
            'published_at' => $validated['published_at'] ?? $joke->published_at,
        ]);

        // Sync categories if provided
        if (array_key_exists('categories', $validated)) {
            $joke->categories()->sync($validated['categories']);
        }

        return ApiResponse::success($joke->load('categories'), "Joke updated");
    }

    /**
     * Soft delete the specified joke.
     */
    public function destroy(string $id): JsonResponse
    {
        $joke = Joke::find($id);

        if (!$joke) {
            return ApiResponse::error(null, "Joke not found", 404);
        }

        $joke->delete();

        return ApiResponse::success(null, "Joke moved to trash");
    }

    /**
     * List all soft-deleted jokes.
     */
    public function trash(): JsonResponse
    {
        $trashed = Joke::onlyTrashed()->get();

        return ApiResponse::success($trashed, "Trashed jokes retrieved");
    }

    /**
     * Restore all soft-deleted jokes.
     */
    public function recoverAll(): JsonResponse
    {
        $count = Joke::onlyTrashed()->restore();

        return ApiResponse::success(['restored' => $count], "All trashed jokes restored");
    }

    /**
     * Permanently delete all soft-deleted jokes.
     */
    public function removeAll(): JsonResponse
    {
        $count = Joke::onlyTrashed()->forceDelete();

        return ApiResponse::success(['deleted' => $count], "All trashed jokes permanently deleted");
    }

    /**
     * Restore a single soft-deleted joke.
     */
    public function recoverOne(string $id): JsonResponse
    {
        $joke = Joke::onlyTrashed()->where('id', $id)->first();

        if (!$joke) {
            return ApiResponse::error(null, "Joke not found in trash", 404);
        }

        $joke->restore();

        return ApiResponse::success($joke, "Joke restored");
    }

    /**
     * Permanently delete a single soft-deleted joke.
     */
    public function removeOne(string $id): JsonResponse
    {
        $joke = Joke::onlyTrashed()->where('id', $id)->first();

        if (!$joke) {
            return ApiResponse::error(null, "Joke not found in trash", 404);
        }

        $joke->forceDelete();

        return ApiResponse::success(null, "Joke permanently deleted");
    }
}

