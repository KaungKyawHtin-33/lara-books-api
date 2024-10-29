<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Carbon\Carbon;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereNull('deleted_at')->get();

        return response()->json([
            'categories'    => $categories,
            'message'       => 'Show all categories'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'category'  => $category,
            'message'   => 'Category created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        abort_if(!$category || $category->deleted_at != null, 404);

        return response()->json([
            'categories'    => $category,
            'message'       => "Show detail of {$category->name}"
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        abort_if(!$category || $category->deleted_at != null, 404);

        $category->update($request->validated());

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        abort_if(!$category || $category->deleted_at != null, 404);

        $category->update([
            'deleted_at' => Carbon::now()
        ]);

        return response()->json(status: 204);
    }
}
