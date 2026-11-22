<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_product_management(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_user_cannot_manage_products(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_product_with_an_image(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['type' => 'food']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Strawberry Cake',
            'description' => 'Kue stroberi segar.',
            'price' => 35000,
            'stock' => 5,
            'is_available' => 1,
            'image' => UploadedFile::fake()->createWithContent(
                'strawberry-cake.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'),
            ),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::query()->where('slug', 'strawberry-cake')->firstOrFail();
        $this->assertModelExists($product);
        $this->assertSame(35000, $product->price);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_admin_can_create_a_drink_with_variant_prices(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['type' => 'drink']);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Iced Matcha',
            'description' => 'Matcha dengan susu.',
            'ice_price' => 18000,
            'hot_price' => 20000,
            'stock' => 8,
            'is_available' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Iced Matcha',
            'price' => null,
            'ice_price' => 18000,
            'hot_price' => 20000,
        ]);
    }

    public function test_drink_requires_at_least_one_variant_price(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['type' => 'drink']);

        $response = $this->from(route('admin.products.create'))
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Matcha',
                'description' => 'Matcha dengan susu.',
                'stock' => 8,
                'is_available' => 1,
            ]);

        $response->assertSessionHasErrors(['ice_price' => 'Isi harga Ice atau Hot untuk minuman.']);
        $this->assertDatabaseMissing('products', ['name' => 'Matcha']);
    }

    public function test_food_product_requires_a_price(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['type' => 'food']);

        $response = $this->from(route('admin.products.create'))
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Lemon Tart',
                'description' => 'Tart lemon.',
                'stock' => 3,
                'is_available' => 1,
            ]);

        $response->assertSessionHasErrors(['price' => 'Harga produk wajib diisi.']);
        $this->assertDatabaseMissing('products', ['name' => 'Lemon Tart']);
        Storage::disk('public')->assertEmpty();
    }

    public function test_admin_can_update_price_stock_and_availability(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create(['type' => 'food']);
        $product = Product::factory()->for($category)->create([
            'name' => 'Lemon Tart',
            'slug' => 'lemon-tart',
            'price' => 30000,
            'stock' => 3,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Lemon Tart',
            'description' => 'Tart lemon segar.',
            'price' => 32000,
            'stock' => 0,
            'is_available' => 0,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 32000,
            'stock' => 0,
            'is_available' => false,
        ]);
    }

    public function test_admin_can_delete_a_product_and_its_uploaded_image(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = Category::factory()->create();
        $imagePath = 'products/lemon-tart.jpg';
        Storage::disk('public')->put($imagePath, 'image contents');
        $product = Product::factory()->for($category)->create(['image_path' => $imagePath]);

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_public_catalog_shows_products_from_the_database_and_marks_unavailable_items(): void
    {
        $category = Category::factory()->create(['name' => 'Cake', 'slug' => 'cake', 'type' => 'food']);
        Product::factory()->for($category)->create([
            'name' => 'Lemon Tart',
            'slug' => 'lemon-tart',
            'price' => 32000,
            'is_available' => false,
        ]);

        $response = $this->get(route('catalog'));

        $response->assertOk()
            ->assertSee('Lemon Tart')
            ->assertSee('Rp 32.000')
            ->assertSee('Habis')
            ->assertSee('disabled', false);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }
}
