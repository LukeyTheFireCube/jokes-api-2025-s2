<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the Categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::all();
        return ApiResponse::success($categories, "Categories retrieved");
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validate([
            'title' => ['string', 'required', 'min:4'],
            'description' => ['string', 'nullable', 'min:6'],
        ]);

        $category = Category::create($validated);

        return ApiResponse::success($category, 'Category created', 201);
    }

    /**
     * Display the specified Category.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $category = Category::whereId($id)->get();

        if (count($category) === 0) {
            return ApiResponse::error($category, "Category not found", 404);
        }
        return ApiResponse::success($category, "Category retrieved");
    }

    /**
     * Update the specified Category in storage.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ApiResponse::error(null, "Category not found", 404);
        }

        $validated = $request->validate([
            'title' => ['string', 'sometimes', 'min:4'],
            'description' => ['string', 'nullable', 'min:6'],
        ]);

        $category->update($validated);

        return ApiResponse::success($category, "Category updated");
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return ApiResponse::error(null, "Category not found", 404);
        }

        $category->delete();

        return ApiResponse::success(null, "Category moved to trash");
    }

    /**
     * Show all soft deleted Categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash()
    {
        $trashed = Category::onlyTrashed()->get();

        return ApiResponse::success($trashed, "Trashed categories retrieved");
    }

    /**
     * Recover all soft deleted categories from trash
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recoverAll()
    {
        $count = Category::onlyTrashed()->restore();

        return ApiResponse::success(["restored" => $count], "All trashed categories restored");
    }

    /**
     * Remove all soft deleted categories from trash
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAll()
    {
        $count = Category::onlyTrashed()->forceDelete();

        return ApiResponse::success(["deleted" => $count], "All trashed categories permanently deleted");
    }

    /**
     * Recover specified soft deleted category from trash
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function recoverOne(string $id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();

        if (!$category) {
            return ApiResponse::error(null, "Category not found in trash", 404);
        }

        $category->restore();

        return ApiResponse::success($category, "Category restored");
    }

    /**
     * Remove specified soft deleted category from trash
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeOne(string $id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();

        if (!$category) {
            return ApiResponse::error(null, "Category not found in trash", 404);
        }

        $category->forceDelete();

        return ApiResponse::success(null, "Category permanently deleted");
    }
}
