<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+().\-\s]+$/'],
            'customer_address' => ['required', 'string', 'min:5', 'max:1000'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*' => ['required', 'array:slug,variant,quantity'],
            'items.*.slug' => ['required', 'string', 'exists:products,slug'],
            'items.*.variant' => ['nullable', 'in:ice,hot'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp wajib diisi.',
            'customer_phone.regex' => 'Format nomor WhatsApp tidak valid.',
            'customer_address.required' => 'Alamat pengiriman wajib diisi.',
            'customer_address.min' => 'Alamat pengiriman minimal lima karakter.',
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.slug.exists' => 'Salah satu produk tidak ditemukan.',
            'items.*.variant.in' => 'Pilihan suhu minuman tidak valid.',
            'items.*.quantity.min' => 'Jumlah produk minimal satu.',
        ];
    }
}
