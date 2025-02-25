<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    protected $ProductRepository;
    public function __construct(ProductRepositoryInterface $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->ProductRepository->getAll();
        return ProductResource::collection($categories);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        $Product = $this->ProductRepository->create($validatedData);
        if (isset($validatedData['category_id'])) {
            $Product->categories()->sync($validatedData['category_id']);
        }
        return response()->json(
            [
                "message" => "Product Created successfully",
                "Product" => new ProductResource($Product)
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Product = $this->ProductRepository->findById($id);
       
        return new ProductResource($Product);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $validatedData = $request->validated();
        // dd($validated);
        $Product = $this->ProductRepository->update($validatedData, $id);
        // dd($Product);
        if (isset($validatedData['category_id'])) {
            $Product->categories()->sync($validatedData['category_id']);
        }
        return response()->json(
            [
                "message" => "Product updated successfully",
                "Product" => new ProductResource($Product)
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->ProductRepository->delete($id);
        return response()->json([
            "message" => "Product deleted successfully",
        ]);

    }
}

