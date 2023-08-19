<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    )
    {}

    public function index(): Collection
    {
        return $this->categoryService->get();
    }

    public function create(CreateCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->only('name'));

        return responseJson($category);
    }
}
