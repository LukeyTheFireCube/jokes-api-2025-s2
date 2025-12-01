<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\UpdateVoteRequest;
use App\Models\Joke;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    /**
     * Submit a like or dislike via API.
     *
     * @param Request $request
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

        return response()->json([
            'success' => true,
            'message' => 'Your vote has been recorded.',
            'vote' => $vote
        ], 200);
    }

    public function update(UpdateVoteRequest $request, Joke $joke): JsonResponse
    {
        $vote = Vote::where('user_id', auth()->id())
            ->where('joke_id', $joke->id)
            ->first();

        if ($vote) {
            $vote->update([
                'value' => $request->validated()['value']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your vote has been updated.',
                'vote' => $vote
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vote not found.'
        ], 404);
    }

    /**
     * Remove a vote via API ("unvote").
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
            return response()->json([
                'success' => true,
                'message' => 'Vote removed.'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vote not found.'
        ], 404);
    }
}

