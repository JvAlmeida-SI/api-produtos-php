<?php

namespace App\Http\Controllers\Products;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductRequest;
use App\Http\Requests\Products\ProductFilterRequest;
use App\Services\Products\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(ProductFilterRequest $request)
    {
        $products = $this->productService->getFilteredProducts($request->validated());
        
        return response()->json([
            'data' => $products,
            'meta' => [
                'total' => $products->count(),
                'filters' => $request->validated()
            ]
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->validated(), 
            auth()->id()
        );
        return response()->json($product, 201);
    }

    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!Gate::allows('update', $product)) {
            abort(403, 'This action is unauthorized.');
        }

        $product = $this->productService->updateProduct(
            $request->validated(), 
            $id
        );
        
        return response()->json($product);
    }

    public function destroy($id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(null, 204);
    }
}