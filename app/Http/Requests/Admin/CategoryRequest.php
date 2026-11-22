<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $nameRule = Rule::unique('categories', 'name');

        if ($category instanceof Category) {
            $nameRule->ignore($category);
        }

        return [
            'name' => ['required', 'string', 'max:100', $nameRule],
            'type' => $this->isMethod('post') ? ['required', Rule::in(['food', 'drink'])] : ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah terdaftar.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori harus dessert atau minuman.',
        ];
    }
}
