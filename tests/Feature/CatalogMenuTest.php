<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Support\MenuItems;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_descriptions_and_coming_soon_image_placeholders(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->get(route('catalog'))
            ->assertOk()
            ->assertSee('Goguma Ppang 3 pcs')
            ->assertSee('Fudgy Brownies Bar')
            ->assertSee('Rp 26.000')
            ->assertSee('Pilihan isi keju atau cokelat, isi 3 buah.')
            ->assertSee('Americano')
            ->assertSee('images/products/croissant-tiramisu-almond.png')
            ->assertSee('images/products/croissant-fruit-danish.png')
            ->assertSee('Gambar segera hadir');
    }

    public function test_catalog_seeder_attaches_available_croissant_images_to_products(): void
    {
        $this->seed(CatalogSeeder::class);

        foreach ([
            'croissant-tiramisu-almond',
            'croissant-fruit-danish',
            'croissant-chocolate',
            'croissant-plain-butter',
            'croissant-strawberry-cream',
        ] as $slug) {
            $this->assertDatabaseHas('products', [
                'slug' => $slug,
                'image_path' => 'images/products/'.$slug.'.png',
            ]);
            $this->assertFileExists(public_path('images/products/'.$slug.'.png'));
        }

        $this->assertDatabaseHas('products', [
            'slug' => 'croissant-choco',
            'image_path' => 'images/products/croissant-chocolate.png',
        ]);
    }

    public function test_quiz_items_include_the_catalog_product_image(): void
    {
        $this->seed(CatalogSeeder::class);

        $items = collect(MenuItems::quizItems());
        $plainButter = $items->firstWhere('slug', 'croissant-plain-butter');
        $strawberryCream = $items->firstWhere('slug', 'croissant-strawberry-cream');
        $fruitDanish = $items->firstWhere('slug', 'croissant-fruit-danish');
        $creamyLatte = $items->firstWhere('slug', 'coffee-byusss-creamy-latte');
        $mangoPudding = $items->firstWhere('slug', 'silky-puding-mangga');

        $this->assertTrue(array_is_list($items->all()));

        $this->assertSame(
            'http://localhost:8000/images/products/croissant-plain-butter.png',
            $plainButter['image'],
        );
        $this->assertSame(
            'http://localhost:8000/images/products/croissant-strawberry-cream.png',
            $strawberryCream['image'],
        );
        $this->assertSame(25000, $fruitDanish['price']);
        $this->assertSame(22000, $creamyLatte['ice']);
        $this->assertSame(21000, $creamyLatte['hot']);
        $this->assertSame(10000, $mangoPudding['price']);

        $catalogSlugs = collect(MenuItems::all())
            ->filter(static fn (array $item): bool => $item['is_available']
                && ($item['price'] !== null || $item['ice'] !== null || $item['hot'] !== null))
            ->pluck('slug');

        $this->assertSame(
            $catalogSlugs->sort()->values()->all(),
            $items->pluck('slug')->sort()->values()->all(),
        );
    }

    public function test_catalog_seeder_fills_only_missing_prices_for_existing_products(): void
    {
        $category = Category::factory()->create([
            'name' => 'Cheesecake',
            'slug' => 'cheesecake',
            'type' => 'food',
        ]);
        $product = Product::factory()->for($category)->create([
            'slug' => 'cheesecake-original',
            'name' => 'Cheesecake Original',
            'price' => null,
        ]);

        $this->seed(CatalogSeeder::class);
        $this->assertSame(26000, $product->fresh()->price);

        $product->update(['price' => 17500]);
        $this->seed(CatalogSeeder::class);
        $this->assertSame(17500, $product->fresh()->price);
    }
}
