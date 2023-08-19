<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function listProducts(int $limit = 100, string $sortBy = null, string $sortOrder = null): LengthAwarePaginator
    {
        $query = Product::query();

        if(! is_null($sortBy)) {
            $query = $query->orderBy($sortBy, $sortOrder);
        }

        return $query->orderBy('is_top', 'DESC')->paginate($limit);
    }

    public function create(array $data, array $categoriesId): Product
    {
        $product = Product::create($data);
        $mappingData = collect($categoriesId)
            ->map(function (int $categoryId) use ($product){
                return [
                    'category_id' => $categoryId,
                    'product_id' => $product->id
                ];
            })
            ->toArray();

        ProductCategory::insert($mappingData);

        return $product->load('categories');
    }

    /**
     * @throws Exception
     */
    public function update(Product $product, array $data, array $newCategories): Product
    {
        DB::beginTransaction();

        try {
            $product->fill($data);

            $oldCategories = $product->categories->pluck('id')->toArray();
            $product->save();

            $this->deleteOldCategories($product, $oldCategories, $newCategories);
            $this->addNewCategories($product, $newCategories, $oldCategories);

            DB::commit();

            return $product->refresh();
        }
        catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    protected function deleteOldCategories(Product $product, array $oldCategories, array $newCategories): void
    {
        $categoriesToDelete = array_diff($oldCategories, $newCategories);
        ProductCategory::whereProductId($product->id)->whereIn('category_id', $categoriesToDelete)->delete();
    }

    protected function addNewCategories(Product $product, array $newCategories, array $oldCategories): void
    {
        $categoriesToAdd = array_diff($newCategories, $oldCategories);
        $newMapping = collect($categoriesToAdd)
            ->map(function (int $categoryToAdd) use ($product) {
                return [
                    'product_id' => $product->id,
                    'category_id' => $categoryToAdd
                ];
            })
            ->toArray();

        ProductCategory::insert($newMapping);
    }

    public function delete(Product $product): Product
    {
        $product->is_deleted = true;
        $product->save();

        return $product;
    }

    public function restore(Product $product): Product
    {
        $product->is_deleted = false;
        $product->save();

        return $product;
    }
}
