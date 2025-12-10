<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\UpdateVoteRequest;
use App\Models\Joke;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Responses\ApiResponse;

class VoteController extends Controller
{
    /**
     * Submit a like or dislike.
     *
     * @param StoreVoteRequest $request
     * @param Joke $joke
     * @return JsonResponse
     */
    public function store(StoreVoteRequest $request, Joke $joke): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'in:1,-1']
        ]);

        $vote = Vote::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'joke_id' => $joke->id,
            ],
            [
                'value' => $validated['value']
            ]
        );

        return ApiResponse::success($vote, "Your vote has been recorded.");
    }

    /**
     * Remove a vote.
     *
     * @param Joke $joke
     * @return JsonResponse
     */
    public function destroy(Joke $joke): JsonResponse
    {
        $vote = Vote::where('user_id', auth()->id())
            ->where('joke_id', $joke->id)
            ->first();

        if ($vote) {
            $vote->delete();
            return ApiResponse::success(null, "Vote removed.");
        }

        return ApiResponse::error($vote, "Vote not found.", 404);
    }
}

