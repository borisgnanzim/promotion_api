<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ActivityLogger;
use App\Traits\JsonResponseTrait;

class CategoryController extends Controller
{
    use JsonResponseTrait, ActivityLogger;

    /**
     * List all categories
     *
     * @group Categories
     * @authenticated
     * @response 200 {"success": true, "data": [{"id": 1, "ref": "cat_123", "name": "Electronics", "slug": "electronics"}], "message": null}
     */
    public function index()
    {
        //return $this->successResponse(CategoryResource::collection(Category::with('items','parent', 'childrens')->get()));
        $categories = Category::whereNull('parent_ref')->with('items','parent', 'childrens')->get();
        return $this->successResponse(CategoryResource::collection($categories));
    }

    /**
     * Create a new category
     *
     * @group Categories
     * @authenticated
     * @bodyParam name string required The category name. Example: Electronics
     * @bodyParam slug string required The category slug. Example: electronics
     * @bodyParam description string The category description. Example: Electronic products
     * @response 201 {"success": true, "data": {"id": 1, "ref": "cat_123", "name": "Electronics", "slug": "electronics"}, "message": "Category created."}
     * @response 422 {"success": false, "message": "Validation failed"}
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        if (isset($data['parent_ref'])) {
            $parent = Category::where('ref', $data['parent_ref'])->first();
            if (!$parent) {
                return $this->errorResponse('Parent category not found', 404);
            }
        }
        $category = Category::create($data);
        $this->logActivity([
            'action' => 'create',
            'target_type' => 'category',
            'target_ref' => $category->ref,
            'user_ref' => auth()->user()->ref,
            'description' => 'Created category '.$category->name,
            'role' => auth()->user()->roles->first()->name ?? null,
        ]);
        return $this->successResponse(CategoryResource::make($category), 'Category created successfully', 201);

    }

    /**
     * Get a specific category
     *
     * @group Categories
     * @authenticated
     * @urlParam ref string required The category reference. Example: cat_123
     * @response 200 {"success": true, "data": {"id": 1, "ref": "cat_123", "name": "Electronics", "slug": "electronics"}, "message": null}
     * @response 404 {"success": false, "message": "Category not found"}
     */
    public function show(Category $category)
    {
        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * Update a category
     *
     * @group Categories
     * @authenticated
     * @urlParam ref string required The category reference. Example: cat_123
     * @bodyParam name string The category name. Example: Electronics Updated
     * @bodyParam slug string The category slug. Example: electronics-updated
     * @response 200 {"success": true, "data": {"id": 1, "ref": "cat_123", "name": "Electronics Updated"}, "message": "Category updated."}
     * @response 404 {"success": false, "message": "Category not found"}
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse(new CategoryResource($category), 'Category updated.');
    }

    /**
     * Delete a category
     *
     * @group Categories
     * @authenticated
     * @urlParam ref string required The category reference. Example: cat_123
     * @response 200 {"success": true, "data": null, "message": "Category deleted."}
     * @response 404 {"success": false, "message": "Category not found"}
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Category deleted.');
    }
}
