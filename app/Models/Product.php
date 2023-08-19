<?php

namespace App\Models;

use App\Models\Scopes\DeletedProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_deleted',
        'is_top',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new DeletedProduct());
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Category::class,
            ProductCategory::class,
            'product_id',
            'id',
            'id',
            'category_id'
        );
    }
}
