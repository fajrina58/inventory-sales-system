<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Staff dan Admin diizinkan membuat transaksi penjualan.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Validasi input penjualan multi-item.
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Harap tambahkan minimal 1 produk untuk dijual.',
            'items.min' => 'Harap tambahkan minimal 1 produk untuk dijual.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.exists' => 'Salah satu produk yang dipilih tidak valid di sistem.',
            'items.*.quantity.required' => 'Jumlah kuantitas wajib diisi.',
            'items.*.quantity.integer' => 'Kuantitas harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Kuantitas penjualan minimal adalah 1 unit.',
        ];
    }
}
