<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Str;

class MenuItems
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function quizItems(): array
    {
        $metadata = collect(config('byuss_quiz.items'))
            ->keyBy('slug');

        return collect(self::all())
            ->filter(static fn (array $item): bool => $item['is_available']
                && ($item['price'] !== null || $item['ice'] !== null || $item['hot'] !== null))
            ->map(static function (array $item) use ($metadata): array {
                $quiz = $metadata->get($item['slug'], []);

                return [
                    ...$quiz,
                    'slug' => $item['slug'],
                    'name' => $item['name'],
                    'group' => $item['group'],
                    'type' => $item['type'],
                    'desc' => $item['desc'],
                    'image' => $item['image'],
                    'price' => $item['price'],
                    'ice' => $item['ice'],
                    'hot' => $item['hot'],
                    'mood' => $quiz['mood'] ?? [],
                    'taste' => $quiz['taste'] ?? [],
                    'flavors' => $quiz['flavors'] ?? [],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return Product::query()
            ->with('category')
            ->orderBy('id')
            ->get()
            ->map(static fn (Product $product): array => [
                'slug' => $product->slug,
                'name' => $product->name,
                'group' => $product->category->name,
                'type' => $product->category->type,
                'desc' => $product->description,
                'image' => $product->imageUrl(),
                'price' => $product->price,
                'ice' => $product->ice_price,
                'hot' => $product->hot_price,
                'stock' => $product->stock,
                'is_available' => $product->is_available,
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public static function findImagePath(array $item): ?string
    {
        $names = array_unique([
            $item['name'],
            Str::slug(str_replace("'", '', $item['name'])),
            $item['slug'],
        ]);

        if ($item['slug'] === 'croissant-choco') {
            $names[] = 'croissant-chocolate';
        }

        foreach ($names as $name) {
            foreach (['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'] as $extension) {
                $path = 'images/products/'.$name.'.'.$extension;

                if (file_exists(public_path($path))) {
                    return $path;
                }
            }
        }

        return null;
    }
}
