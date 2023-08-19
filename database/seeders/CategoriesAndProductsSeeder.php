<?php

namespace Database\Seeders;

use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesAndProductsSeeder extends Seeder
{
    protected const CATEGORIES_COUNT = 50;
    protected const PRODUCTS_COUNT = 100000000;

    public function run()
    {
        /** @var Generator $faker*/
        $faker = app()->make(Generator::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('product_categories')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();

        $chunkSize = 1000;

        $categories = [];
        for ($i = 1; $i <= self::CATEGORIES_COUNT; $i++) {
            $categories[] = ['name' => $faker->words(2, true)];
        }
        DB::table('categories')->insert($categories);

        $categoryIds = range(1, self::CATEGORIES_COUNT);
        $isDeletedOptions = [false, true];
        $isTopOptions = [false, true];

        $productChunks = [];
        $insertedProductsIds = [];
        for ($i = 1; $i <= self::PRODUCTS_COUNT; $i++) {
            $categoryId = $categoryIds[array_rand($categoryIds)];

            $productChunks[] = [
                'name' => $faker->words(2, true),
                'description' => $faker->words(10, true),
                'price' => number_format(rand(100, 5000) / 100, 2),
                'is_deleted' => $isDeletedOptions[array_rand($isDeletedOptions)],
                'is_top' => $isTopOptions[array_rand($isTopOptions)],
            ];

            $insertedProductsIds[] = $i;

            if ($i % $chunkSize === 0) {
                DB::table('products')->insert($productChunks);
                $productCategoryChunks = [];

                foreach ($insertedProductsIds as $productId) {
                    $productCategoryChunks[] = [
                        'product_id' => $productId,
                        'category_id' => $categoryId,
                    ];
                }

                DB::table('product_categories')->insert($productCategoryChunks);

                $productChunks = [];
                $insertedProductsIds = [];
            }
        }

        if (!empty($productChunks)) {
            $productCategoryChunks = [];
            foreach ($insertedProductsIds as $productId) {
                $productCategoryChunks[] = [
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                ];
            }
            DB::table('product_categories')->insert($productCategoryChunks);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('Seeding completed.');
    }
}
