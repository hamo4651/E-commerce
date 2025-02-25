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
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAll();
        return CategoryResource::collection($categories);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->create($validated);
        return response()->json(
            [
                "message" => "Category Created successfully",
                "category" => new CategoryResource($category)
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = $this->categoryRepository->findById($id);
       
        return new CategoryResource($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();
        $category = $this->categoryRepository->update($validated, $id);
        return response()->json(
            [
                "message" => "Category updated successfully",
                "category" => new CategoryResource($category)
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->categoryRepository->delete($id);
        return response()->json([
            "message" => "Category deleted successfully",
        ]);

    }
}

