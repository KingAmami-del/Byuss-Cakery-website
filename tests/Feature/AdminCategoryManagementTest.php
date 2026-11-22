<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_category(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Pastry & Cookies',
            'type' => 'food',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Pastry & Cookies',
            'slug' => 'pastry-cookies',
            'type' => 'food',
        ]);
    }

    public function test_admin_can_rename_a_category(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name' => 'Old Name',
        ]);
    }

    public function test_category_names_must_be_unique(): void
    {
        $admin = $this->createAdmin();
        Category::factory()->create(['name' => 'Cake', 'slug' => 'cake']);

        $response = $this->from(route('admin.categories.create'))
            ->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Cake',
                'type' => 'food',
            ]);

        $response->assertSessionHasErrors(['name' => 'Kategori ini sudah terdaftar.']);
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_category_cannot_be_deleted_while_products_use_it(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHasErrors([
            'category' => 'Kategori masih memiliki produk. Pindahkan atau hapus produk terlebih dahulu.',
        ]);
        $this->assertModelExists($category);
    }

    public function test_admin_can_delete_an_empty_category(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }
}
