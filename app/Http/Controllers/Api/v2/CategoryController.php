<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return ApiResponse::success($categories, "Categories retrieved");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['string'],
            'description' => ['string'],
        ]);

        $category = Category::create($validated);

        return ApiResponse::success($category, 'Category created', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::whereId($id)->get();

        if (count($category)===0) {
            return ApiResponse::error($category, "Category not found", 404);
        }
        return ApiResponse::success($category, "Category retrieved");
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
