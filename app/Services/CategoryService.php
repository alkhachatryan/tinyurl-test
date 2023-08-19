<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function get(): Collection
    {
        return Category::all();
    }
}
