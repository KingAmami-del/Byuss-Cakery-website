<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Support\MenuItems;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('byuss_quiz.items') as $item) {
            $category = Category::query()->firstOrCreate(
                ['slug' => Str::slug($item['group'])],
                [
                    'name' => $item['group'],
                    'type' => $item['type'],
                ],
            );

            $imagePath = MenuItems::findImagePath($item);
            $product = Product::query()->firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => $item['desc'],
                    'image_path' => $imagePath,
                    'price' => $item['price'] ?? null,
                    'ice_price' => $item['ice'] ?? null,
                    'hot_price' => $item['hot'] ?? null,
                    'stock' => null,
                    'is_available' => true,
                ],
            );

            if ($product->wasRecentlyCreated) {
                continue;
            }

            $missingAttributes = [];
            foreach ([
                'price' => $item['price'] ?? null,
                'ice_price' => $item['ice'] ?? null,
                'hot_price' => $item['hot'] ?? null,
            ] as $attribute => $price) {
                if ($price !== null && $product->{$attribute} === null) {
                    $missingAttributes[$attribute] = $price;
                }
            }

            if ($imagePath !== null && (
                $product->image_path === null
                || Str::startsWith($product->image_path, 'images/products/')
            )) {
                $missingAttributes['image_path'] = $imagePath;
            }

            if ($missingAttributes !== []) {
                $product->update($missingAttributes);
            }
        }
    }
}
