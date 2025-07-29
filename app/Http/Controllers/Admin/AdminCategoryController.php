<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Validation
        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search'=> ['nullable', 'string', 'max:32'],
        ]);

        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? '';

        // Get the categories
        $categories = Category::where('title', 'like', '%'.$search.'%')
            ->orderBy('title')
            ->withCount('jokes')
            ->paginate($perPage, ['*'], 'page', $page);

        // TODO: get trashed category count
        $trashCount = 0;

        // return view
        return view('admin.categories.index')
            ->with('categories',$categories)
            ->with('trashCount',$trashCount)
            ->with('search',$search);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'min:4', 'max:32',
                'unique:categories,title',
            ],
            'description' => [
                'nullable',
                'string',
                'min:16', 'max:255',
            ],
        ]);

        $category = Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
