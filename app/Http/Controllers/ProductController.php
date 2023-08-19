<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    )
    {}

    public function create(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->only('name', 'description', 'price'), $request->input('categories'));

        return responseJson($product, Response::HTTP_CREATED);
    }

    public function update(UpdateProductRequest $request): JsonResponse
    {
        /** @var Product $product */
        $product = Product::with('categories')->find($request->input('product_id'));
        $product = $this->productService->update($product, $request->only('name', 'description', 'price', 'is_top'), $request->input('categories'));

        return responseJson($product);
    }

    public function delete(int $productId): JsonResponse
    {
        $this->productService->delete($productId);

        return responseJson(null, Response::HTTP_NO_CONTENT);
    }
}
