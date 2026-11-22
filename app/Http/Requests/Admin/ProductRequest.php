<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['nullable', 'integer', 'min:0'],
            'ice_price' => ['nullable', 'integer', 'min:0'],
            'hot_price' => ['nullable', 'integer', 'min:0'],
            'stock' => [$this->isMethod('post') ? 'required' : 'nullable', 'integer', 'min:0'],
            'is_available' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('category_id')) {
                    return;
                }

                $type = Category::query()->whereKey($this->integer('category_id'))->value('type');

                if ($type === 'food' && ! $this->filled('price')) {
                    $validator->errors()->add('price', 'Harga produk wajib diisi.');
                }

                if ($type === 'drink' && ! $this->filled('ice_price') && ! $this->filled('hot_price')) {
                    $validator->errors()->add('ice_price', 'Isi harga Ice atau Hot untuk minuman.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'price.min' => 'Harga tidak boleh kurang dari nol.',
            'ice_price.integer' => 'Harga Ice harus berupa angka bulat.',
            'ice_price.min' => 'Harga Ice tidak boleh kurang dari nol.',
            'hot_price.integer' => 'Harga Hot harus berupa angka bulat.',
            'hot_price.min' => 'Harga Hot tidak boleh kurang dari nol.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh kurang dari nol.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.extensions' => 'Ekstensi gambar harus JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 5 MB.',
        ];
    }
}
