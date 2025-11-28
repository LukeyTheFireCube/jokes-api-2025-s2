<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Joke;
use App\Models\Vote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Submit a like or dislike from the web UI.
     *
     * @param Request $request
     * @param Joke $joke
     * @return RedirectResponse
     */
    public function store(Request $request, Joke $joke): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'in:1,-1']
        ]);

        Vote::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'joke_id' => $joke->id,
            ],
            [
                'value' => $validated['value']
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Your vote has been recorded.');
    }

    /**
     * Remove a vote from the UI ("unvote")
     *
     * @param Joke $joke
     * @return RedirectResponse
     */
    public function destroy(Joke $joke)
    {
        $vote = Vote::where('user_id', auth()->id())
            ->where('joke_id', $joke->id)
            ->first();

        if ($vote) {
            $vote->delete();
        }

        return redirect()
            ->back()
            ->with('success', 'Vote removed.');
    }
}

