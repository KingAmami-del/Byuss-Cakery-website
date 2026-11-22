<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackOrderRequest extends FormRequest
{
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
            'order_number' => ['required', 'string', 'max:32', 'regex:/^BYU-\d{8}-[A-Z0-9]{6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_number.required' => 'Kode pesanan wajib diisi.',
            'order_number.regex' => 'Format kode pesanan tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $orderNumber = $this->input('order_number');

        if (is_string($orderNumber)) {
            $this->merge([
                'order_number' => strtoupper(trim($orderNumber)),
            ]);
        }
    }
}
