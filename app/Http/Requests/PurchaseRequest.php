<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Staff dan Admin diizinkan membuat transaksi pembelian.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Validasi input pembelian multi-item.
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pemasok wajib dipilih.',
            'supplier_id.exists' => 'Pemasok yang dipilih tidak terdaftar di sistem.',
            'items.required' => 'Harap tambahkan minimal 1 produk untuk dibeli.',
            'items.min' => 'Harap tambahkan minimal 1 produk untuk dibeli.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_id.exists' => 'Salah satu produk yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah kuantitas wajib diisi.',
            'items.*.quantity.integer' => 'Kuantitas harus berupa bilangan bulat.',
            'items.*.quantity.min' => 'Kuantitas minimal adalah 1.',
            'items.*.unit_cost.required' => 'Harga beli satuan wajib diisi.',
            'items.*.unit_cost.numeric' => 'Harga beli satuan harus berupa angka.',
            'items.*.unit_cost.min' => 'Harga beli satuan tidak boleh bernilai negatif.',
        ];
    }
}
