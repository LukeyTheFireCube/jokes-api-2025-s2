<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Joke;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminJokeController extends Controller
{
    /**
     * Display a listing of jokes.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:64'],
        ]);

        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? '';

        $jokes = Joke::query()
            ->with(['user', 'categories'])
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('content', 'like', "%$search%");
            })
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $trashCount = Joke::onlyTrashed()->count();

        return view('admin.jokes.index')
            ->with('jokes', $jokes)
            ->with('trashCount', $trashCount)
            ->with('search', $search);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.jokes.create')
            ->with('categories', Category::orderBy('title')->get());
    }

    /**
     * Store new joke.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:128'],
            'content' => ['nullable', 'string', 'max:5000'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')],
        ]);

        $validated['user_id'] = auth()->id();

        $joke = Joke::create($validated);

        $joke->categories()->sync($validated['categories']);

        return redirect()->route('admin.jokes.index')
            ->with('success', "Joke {$joke->title} created successfully.");
    }

    /**
     * Show a single joke.
     */
    public function show(Joke $joke): View
    {
        $joke->load(['categories', 'user']);

        return view('admin.jokes.show')
            ->with('joke', $joke);
    }

    /**
     * Edit form.
     */
    public function edit(Joke $joke): View
    {
        return view('admin.jokes.edit')
            ->with('joke', $joke->load('categories'))
            ->with('categories', Category::orderBy('title')->get());
    }

    /**
     * Update joke.
     */
    public function update(Request $request, Joke $joke): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:4', 'max:128'],
            'content' => ['nullable', 'string', 'max:5000'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => [
                'integer',
                Rule::exists('categories', 'id')
            ],
        ]);

        $joke->update($validated);
        $joke->categories()->sync($validated['categories']);

        return redirect()->route('admin.jokes.index')
            ->with('success', "Joke {$joke->title} updated successfully.");
    }

    /**
     * Show delete confirmation.
     */
    public function delete(Joke $joke): View
    {
        return view('admin.jokes.delete')
            ->with('joke', $joke);
    }

    /**
     * Soft delete.
     */
    public function destroy(Joke $joke): RedirectResponse
    {
        $title = $joke->title;

        $joke->delete();

        return redirect()->route('admin.jokes.index')
            ->with('success', "Joke $title deleted successfully.");
    }

    /**
     * Showing trash list
     */
    public function trash(Request $request): View
    {
        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:64'],
        ]);

        $search = $validated['search'] ?? '';
        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;

        $jokes = Joke::onlyTrashed()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            })
            ->orderByDesc('deleted_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return view('admin.jokes.trash')
            ->with('jokes', $jokes)
            ->with('search', $search);
    }

    public function recoverAll(): RedirectResponse
    {
        Joke::onlyTrashed()->restore();

        return redirect()->route('admin.jokes.index')
            ->with('success', 'All jokes restored.');
    }

    public function removeAll(): RedirectResponse
    {
        Joke::onlyTrashed()->forceDelete();

        return redirect()->route('admin.jokes.trash')
            ->with('success', 'All jokes permanently deleted.');
    }

    public function recoverOne(string $id): RedirectResponse
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return redirect()->route('admin.jokes.index')
                ->with('error', 'Joke not found.');
        }

        $joke->restore();

        return redirect()->route('admin.jokes.index')
            ->with('success', 'Joke restored.');
    }

    public function removeOne(string $id): RedirectResponse
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return redirect()->route('admin.jokes.index')
                ->with('error', 'Joke not found.');
        }

        $joke->forceDelete();

        return redirect()->route('admin.jokes.index')
            ->with('success', 'Joke permanently deleted.');
    }
}

