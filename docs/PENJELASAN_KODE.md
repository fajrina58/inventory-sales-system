# Panduan & Persiapan Code Interview
## Integrated Laravel Project - Inventory & Sales Management System

Dokumen ini disusun khusus sebagai bekal menghadapi sesi wawancara kode (*code interview*) dengan dosen. Penjelasan dibuat ringkas, tepat sasaran, dan menyertakan nama file serta fungsi terkait di dalam kode.

---

### 1. Di mana stok dijaga agar tidak negatif?
* **Lokasi Kode:**
  - Penjualan: [`app/Services/SaleService.php`](../app/Services/SaleService.php) di dalam method `createSale()`.
  - Pembatalan Pembelian: [`app/Services/PurchaseService.php`](../app/Services/PurchaseService.php) di dalam method `cancelPurchase()`.
* **Mekanisme Kerja:**
  Sebelum kuantitas stok dikurangkan (`$product->decrement('stock', $qty)`), sistem melakukan validasi eksplisit:
  ```php
  if ($product->stock < $qty) {
      throw ValidationException::withMessages([
          'items' => ["Penjualan ditolak: Stok untuk produk '{$product->name}' tidak mencukupi..."]
      ]);
  }
  ```
  Jika stok kurang, sistem melempar `ValidationException`. Karena berada di dalam `DB::transaction`, seluruh operasi langsung dibatalkan (*rollback*) sehingga stok di basis data tidak pernah bernilai negatif.

---

### 2. Kenapa total harga dihitung di server, bukan dari input browser?
* **Alasan Keamanan (*Security & Business Rule Integrity*):**
  Input dari browser (HTTP POST Request) sangat mudah dimanipulasi oleh pihak luar menggunakan *Inspect Element*, *DevTools*, atau aplikasi seperti *Postman*. Jika server mempercayai harga dari browser, pengguna jahat dapat mengubah harga barang Rp 500.000 menjadi Rp 1.
* **Implementasi di Kode:**
  Di [`app/Http/Controllers/SaleController.php`](../app/Http/Controllers/SaleController.php) dan [`app/Services/SaleService.php`](../app/Services/SaleService.php), form hanya mengirimkan `product_id` dan `quantity`. Nilai satuan harga resmi (`$product->selling_price`) diambil langsung dari database MySQL oleh server, kemudian server mengalikan dan menjumlahkan totalnya.

---

### 3. Apa fungsi `DB::transaction` dan `Product::lockForUpdate()`?
* **`DB::transaction(...)` (Prinsip Atomicity - ACID):**
  - **Fungsi:** Menjamin bahwa kumpulan operasi database (membuat header faktur, menyimpan detail item, dan memperbarui stok fisik) diperlakukan sebagai satu kesatuan yang utuh.
  - **Dampaknya:** Jika penyimpanan item ke-3 gagal karena stok habis, maka header faktur serta pemotongan stok pada item ke-1 dan ke-2 otomatis dibatalkan (*rollback*). Tidak akan pernah ada data setengah tersimpan.
* **`Product::lockForUpdate()` (Pessimistic Locking):**
  - **Fungsi:** Menjalankan query SQL `SELECT ... FOR UPDATE` pada baris produk yang sedang diproses.
  - **Mencegah *Race Condition* / *Overselling*:** Jika dua staf kasir memproses sisa stok 1 barang yang sama pada detik yang persis sama, `lockForUpdate()` mengunci baris produk tersebut untuk transaksi pertama. Transaksi kedua dipaksa antre/menunggu sampai transaksi pertama selesai. Ketika transaksi kedua membaca data, stok sudah bernilai 0 sehingga transaksi kedua ditolak dengan aman.

---

### 4. Bagaimana cara Staff dicegah mengakses fungsi Admin lewat URL langsung?
* **Otorisasi di Server-Side (Bukan Hanya Sembunyikan Tombol):**
  Jika pembatasan hanya dilakukan dengan menyembunyikan tombol di Blade (`@if(isAdmin)`), staf yang mengetikkan URL langsung (misal: `/categories/create` atau `/admin/users`) tetap bisa menembus sistem.
* **Lapisan Pengamanan Server:**
  1. **Middleware Admin:** [`app/Http/Middleware/AdminMiddleware.php`](../app/Http/Middleware/AdminMiddleware.php) memeriksa:
     ```php
     if (! $request->user() || ! $request->user()->isAdmin()) {
         abort(403, 'Akses Ditolak: Fitur ini hanya dapat diakses oleh Administrator.');
     }
     ```
  2. **Registrasi Rute:** Di [`routes/web.php`](../routes/web.php), semua rute kelola kategori, ubah produk, ubah supplier, dan kelola user dibungkus dalam grup `Route::middleware(['auth', 'admin'])`.
  3. **Form Request Authorization:** Pada [`app/Http/Requests/ProductRequest.php`](../app/Http/Requests/ProductRequest.php), method `authorize()` mengembalikan `$this->user()->isAdmin()`.

---

### 5. Mengapa validasi SKU unik ada di Form Request DAN constraint di tabel Database?
* **Validasi Form Request (`Rule::unique('products', 'sku')`):**
  Berfungsi memberikan pesan kesalahan yang ramah pengguna (*user-friendly validation error*) pada antarmuka web saat form dikirimkan.
* **Constraint Database (`$table->string('sku', 50)->unique()`):**
  Berfungsi sebagai pengaman lapis terakhir (*last line of defense*). Validasi di level aplikasi dapat ditembus oleh *race condition* jika dua request masuk secara paralel dalam fraksi milidetik. Database MySQL dengan index B-Tree `UNIQUE` menjamin secara fisik tidak akan pernah ada baris dengan SKU yang kembar.

---

### 6. Alur Kode Penjualan dari Route sampai Database
Berikut urutan eksekusi lengkap saat transaksi penjualan terjadi:

```
[Browser Form] 
       │
       ▼
1. routes/web.php 
   -> Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show'])
       │
       ▼
2. app/Http/Requests/SaleRequest.php
   -> Memvalidasi kuantitas minimal 1, format array items, dan keberadaan produk.
       │
       ▼
3. app/Http/Controllers/SaleController.php@store
   -> Menerima request yang sudah valid, lalu memanggil SaleService.
       │
       ▼
4. app/Services/SaleService.php@createSale
   -> Membuka DB::transaction.
   -> Menghasilkan nomor nota unik 'TRX-YYYYMMDD-XXXX'.
   -> Menyimpan header pada tabel 'sales' dengan total awal 0.
   -> Melakukan loop setiap item:
      * Product::lockForUpdate()->findOrFail($item['product_id'])
      * Cek $product->stock >= $qty (jika kurang -> lempar ValidationException).
      * Ambil harga jual dari $product->selling_price.
      * Simpan detail ke tabel 'sale_items'.
      * Potong stok fisik produk: $product->decrement('stock', $qty).
   -> Memperbarui total akhir pada tabel 'sales'.
   -> DB::transaction Commit.
       │
       ▼
5. app/Models/Sale.php & app/Models/SaleItem.php
   -> Representasi relasi Eloquent: Sale hasMany SaleItem, SaleItem belongsTo Product.
       │
       ▼
6. Redirect ke routes/web.php (sales.show)
   -> Menampilkan lembar nota faktur resmi penjualan kepada kasir/staf.
```

---

### 7. Pertanyaan Tambahan Seputar Desain Relasi
* **Kenapa `Sale` hasMany `SaleItem` dan Foreign Key ada di tabel detail?**
  Satu nota penjualan dapat berisi banyak macam produk. Sesuai prinsip normalisasi basis data (1NF–3NF), atribut *multi-value* tidak boleh disimpan berulang pada tabel header `sales`. Foreign key (`sale_id`) ditempatkan pada tabel anak (`sale_items`) untuk menunjuk ke nomor nota induknya.
* **Mengapa ada `unit_price` di tabel `sale_items` padahal sudah ada `selling_price` di tabel `products`?**
  Sebagai **Snapshot Harga Historis**. Harga master produk dapat berubah di masa depan (misal karena inflasi). Riwayat faktur penjualan masa lalu tidak boleh ikut berubah nilainya ketika harga master diubah di kemudian hari.
