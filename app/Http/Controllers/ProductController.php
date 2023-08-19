<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\ReadProductRequest;
use App\Http\Requests\RestoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Jobs\AddProductToLastViewedProductsColumn;
use App\Models\Product;
use App\Models\Scopes\DeletedProduct;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    )
    {}

    public function index(ListProductsRequest $request)
    {
        $paginatorData = $this->productService->listProducts($request->input('limit'), $request->input('sort_by'), $request->input('sort_order'));

        return responseJson($paginatorData);
    }

    public function show(ReadProductRequest $request): Builder|array|Collection|Model
    {
        $product = $this->productService->showProduct($request->input('product_id'));

        if(! is_null($request->user())) {
            AddProductToLastViewedProductsColumn::dispatch($request->user()->id, $product->id, $request->user()->last_viewed_products);
        }

        return $product;
    }

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

    public function delete(DeleteProductRequest $request): JsonResponse
    {
        /** @var Product $product */
        $product = Product::with('categories')->find($request->input('product_id'));
        $this->productService->delete($product);

        return responseJson($product);
    }

    public function restore(RestoreProductRequest $request): JsonResponse
    {
        /** @var Product $product */
        $product = Product::withoutGlobalScope(DeletedProduct::class)->with('categories')->find($request->input('product_id'));
        $this->productService->restore($product);

        return responseJson($product);
    }
}
