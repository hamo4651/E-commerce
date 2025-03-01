<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories
     * 
     * @group Category Management
     * 
     * @response 200 {
     *    "data": [
     *       {
     *          "id": 1,
     *          "name": "Electronics",
     *          "created_at": "2024-03-01T12:00:00.000000Z",
     *          "updated_at": "2024-03-01T12:00:00.000000Z"
     *       }
     *    ]
     * }
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a new category
     * 
     * @group Category Management
     * 
     * @bodyParam name string required The name of the category. Example: "Fashion"
     * 
     * @response 201 {
     *    "message": "Category created successfully",
     *    "category": {
     *       "id": 2,
     *       "name": "Fashion",
     *       "created_at": "2024-03-01T12:00:00.000000Z",
     *       "updated_at": "2024-03-01T12:00:00.000000Z"
     *    }
     * }
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->create($validated);

        return response()->json([
            "message" => "Category created successfully",
            "category" => new CategoryResource($category)
        ], 201);
    }

    /**
     * Get a specific category
     * 
     * @group Category Management
     * 
     * @urlParam id int required The ID of the category. Example: 1
     * 
     * @response 200 {
     *    "id": 1,
     *    "name": "Electronics",
     *    "created_at": "2024-03-01T12:00:00.000000Z",
     *    "updated_at": "2024-03-01T12:00:00.000000Z"
     * }
     * @response 404 {
     *    "message": "Category not found"
     * }
     */
    public function show($id)
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            return response()->json(["message" => "Category not found"], 404);
        }

        return new CategoryResource($category);
    }

    /**
     * Update a category
     * 
     * @group Category Management
     * 
     * @urlParam id int required The ID of the category. Example: 1
     * @bodyParam name string required The updated name of the category. Example: "New Fashion"
     * 
     * @response 200 {
     *    "message": "Category updated successfully",
     *    "category": {
     *       "id": 1,
     *       "name": "New Fashion",
     *       "created_at": "2024-03-01T12:00:00.000000Z",
     *       "updated_at": "2024-03-01T12:10:00.000000Z"
     *    }
     * }
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->update($validated, $id);

        return response()->json([
            "message" => "Category updated successfully",
            "category" => new CategoryResource($category)
        ]);
    }

    /**
     * Delete a category
     * 
     * @group Category Management
     * 
     * @urlParam id int required The ID of the category. Example: 1
     * 
     * @response 200 {
     *    "message": "Category deleted successfully"
     * }
     * @response 404 {
     *    "message": "Category not found"
     * }
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            return response()->json(["message" => "Category not found"], 404);
        }

        $this->categoryRepository->delete($id);
        return response()->json(["message" => "Category deleted successfully"]);
    }
}
