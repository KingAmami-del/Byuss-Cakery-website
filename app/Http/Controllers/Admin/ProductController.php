<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('category')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::query()->findOrFail($data['category_id']);

        $data = $this->normalizePrices($data, $category);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image_path'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : null;
        unset($data['image'], $data['remove_image']);

        Product::query()->create($data);

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::query()->findOrFail($data['category_id']);
        $oldImagePath = $product->image_path;

        $data = $this->normalizePrices($data, $category);
        $data['slug'] = $this->uniqueSlug($data['name'], $product);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        } else {
            unset($data['image_path']);
        }

        unset($data['image'], $data['remove_image']);
        $product->update($data);

        if ($oldImagePath !== $product->image_path) {
            $this->deleteStoredImage($oldImagePath, $product->id);
        }

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $imagePath = $product->image_path;
        $product->delete();
        $this->deleteStoredImage($imagePath, $product->id);

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePrices(array $data, Category $category): array
    {
        if ($category->type === 'food') {
            $data['ice_price'] = null;
            $data['hot_price'] = null;
        } else {
            $data['price'] = null;
        }

        return $data;
    }

    private function uniqueSlug(string $name, ?Product $except = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $suffix = 2;

        while (Product::query()
            ->where('slug', $slug)
            ->when($except, fn ($query) => $query->where('id', '!=', $except->id))
            ->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->storePublicly('products', 'public');

        if (! is_string($path)) {
            throw new RuntimeException('Gambar produk tidak berhasil disimpan.');
        }

        return $path;
    }

    private function deleteStoredImage(?string $path, int $productId): void
    {
        if (! $path || ! Str::startsWith($path, 'products/')) {
            return;
        }

        if (! Storage::disk('public')->delete($path)) {
            Log::warning('Gambar produk tidak berhasil dihapus dari penyimpanan.', [
                'product_id' => $productId,
                'image_path' => $path,
            ]);
        }
    }
}
