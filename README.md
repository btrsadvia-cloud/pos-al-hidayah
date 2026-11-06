# POS Toko Al-Hidayah

Sistem Kasir (Point of Sale) & Manajemen Inventori Web berbasis **Laravel 13** yang dirancang khusus untuk toko ritel & grosir busana muslimah, seragam sekolah, dinas, serta perlengkapan haji, umroh, dan ibadah.

---

## 1. Lingkungan & Spesifikasi Teknis

- **Operating System:** Windows (XAMPP Apache + MariaDB)
- **PHP:** 8.4.27 (64-bit) dengan ekstensi aktif: `bcmath`, `curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `zip`
- **Framework:** Laravel 13.40.0
- **Frontend Stack:** Blade + Tailwind CSS v4 + Livewire v4.4 + Alpine.js
- **Database:** MySQL / MariaDB (`pos_al_hidayah`), UTF-8 Unicode (`utf8mb4_unicode_ci`)
- **Reporting & Export:**
  - Cetak Struk: CSS khusus thermal printer 58mm / 80mm
  - Laporan PDF: `barryvdh/laravel-dompdf` v3.1
  - Laporan Excel: `maatwebsite/excel` v4.0

---

## 2. Akun Demo Pengujian

| Peran (Level) | Username | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin (Pemilik)** (`level: 1`) | `admin` | `password` | Akses penuh: Master data (kategori, satuan, produk, harga bertingkat), pemasok, pelanggan, mutasi stok, pembatalan transaksi, laporan penjualan (PDF & Excel), analitik omzet 30 hari, manajemen pengguna. |
| **Kasir** (`level: 2`) | `kasir1` | `password` | Layar kasir (POS), buka/tutup shift kasir, cetak struk, riwayat transaksi miliknya sendiri, stok masuk & keluar, data pelanggan, dashboard ringkasan harian (tanpa data laba/analitik). |

---

## 3. Fitur Utama & Aturan Bisnis

### A. Layar Kasir POS (Livewire v4 Real-time)
- **Desain Responsif:** Mobile-first (1 kolom di mobile <768px, 2 kolom tablet, 3 area desktop: Katalog, Keranjang, Panel Pembayaran).
- **Barcode Scanner Otomatis:** Input pencarian auto-focus; scanning barcode otomatis memasukkan produk ke keranjang saat menekan `Enter`.
- **Harga Bertingkat Otomatis (3-Tier):**
  - **Eceran:** Pembelian satuan standar (min 1).
  - **Grosir:** Diterapkan otomatis saat kuantitas mencapai batas grosir (misal $\ge$ 12 pcs).
  - **Super Grosir:** Diterapkan otomatis untuk pembelian borongan (misal $\ge$ 36 pcs).
  - Label tier langsung tampil pada baris keranjang belanja.
- **Pengecekan Stok & Row Lock:** Checkout diproses dalam satu transaksi database (`DB::transaction`) dengan `lockForUpdate()` pada produk untuk mencegah *race condition* dan *overselling*. Stok berkurang seketika dan tercatat di mutasi stok.
- **Aturan Diskon Kasir:** Kasir dibatasi maksimal diskon sebesar **10%** dari nilai belanja (`config/pos.php`). Diskon melebihi batas ini otomatis ditolak. Admin tidak memiliki batasan diskon.
- **Deteksi Offline:** Indikator visual otomatis muncul jika koneksi internet/jaringan terputus, dan tombol checkout diblokir sampai koneksi pulih kembali.
- **Pintasan Keyboard (Keyboard Shortcuts):**
  - `F2` : Fokus ke pencarian / barcode scanner
  - `F8` : Fokus ke input diskon faktur
  - `F9` : Selesaikan pembayaran (Checkout)
  - `Esc` : Tutup dialog / modal struk
  - `?` : Buka panduan pintasan keyboard
- **Cetak Struk Thermal:** Struk langsung diformat dengan CSS cetak thermal ukuran 58mm / 80mm.

### B. Manajemen Shift Kasir
- Kasir wajib memiliki shift terbuka sebelum dapat mengakses layar POS.
- Pencatatan kas awal (modal laci kasir).
- Penutupan shift dengan penghitungan fisik uang tunai (*counted cash*).
- Sistem otomatis menghitung kas yang diharapkan (*expected cash* = kas awal + total penjualan tunai) dan menampilkan selisih kas (pas, lebih, atau kurang).

### C. Inventori & Kartu Stok
- **Stok Masuk (Beli dari Pemasok):** Menambah stok dan mencatat Harga Pokok Penjualan (HPP).
- **Stok Keluar (Manual):** Pengurangan stok akibat barang rusak, cacat, sample, atau hilang dengan alasan yang wajib diisi.
- **Kartu Stok (Stock Ledger):** Buku pembantu mutasi stok per produk lengkap dengan kolom Debit (Masuk), Kredit (Keluar), dan Saldo Berjalan (*Running Balance*).

### D. Keamanan & Integritas Data
- Tidak ada penghapusan data transaksi penjualan (*permanent delete*).
- Pembatalan transaksi hanya dapat dilakukan oleh **Admin** dengan mengisi alasan pembatalan; status penjualan berubah menjadi `cancelled` dan seluruh barang dikembalikan ke stok fisik melalui mutasi stok masuk baru.
- Penonaktifan akun pengguna secara lunak (*soft deactivation*) tanpa menghapus riwayat transaksi.
- Setiap aktivitas penting dicatat pada tabel `audit_logs`.

### E. Integrasi Payment Gateway QRIS Dinamis (BuatQris)
- Terhubung langsung dengan Open API **[BuatQris](https://app.buatqris.site)** (`https://api.buatqris.site`).
- **QRIS Dinamis Otomatis:** Saat kasir memilih metode QRIS di POS, sistem memanggil backend untuk membuat invoice QRIS dengan kode unik nominal untuk verifikasi instan.
- **Tampilan Modal QRIS Real-time:** Menampilkan QR Code (gambar PNG / base64), total nominal tepat yang wajib dibayar, batas kedaluwarsa, dan indikator status langsung di depan kasir/pembeli.
- **Polling & Webhook Aman:**
  - Dilengkapi endpoint webhook callback (`/api/buatqris/webhook`) dengan verifikasi tanda tangan HMAC-SHA256 (`X-BuatQris-Signature`) dan proteksi idempoten.
  - Di lingkungan localhost, kasir tetap dapat memantau pembayaran via tombol **"Cek Status Sekarang"** dan polling otomatis yang aman dari batas rate limit (maks 1x per 20 detik).
  - Saat pembayaran lunas, transaksi otomatis tersimpan dan nota struk langsung tercetak.
- **Halaman Pengaturan BuatQris (Admin):** Menu **Pengaturan QRIS** untuk mengisi Account ID, Secret Token, Signing Secret, serta memantau log transaksi QRIS.

---

## 4. Cara Instalasi & Menjalankan di Localhost

### Langkah 1: Kloning & Pengaturan Lingkungan
Pastikan Apache & MySQL di XAMPP telah aktif.
```powershell
cd C:\POS_Toko_AlHidayah
copy .env.example .env
```

Pastikan konfigurasi database di file `.env`:
```ini
APP_NAME="POS Al-Hidayah"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://127.0.0.1:8000
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_al_hidayah
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 2: Migrasi & Seeder Database
```powershell
C:\xampp\php\php.exe artisan migrate --seed --force
```

### Langkah 3: Kompilasi Aset Frontend
```powershell
npm run build
```

### Langkah 4: Menjalankan Server Aplikasi
Aplikasi dapat diakses melalui dua cara:

**Opsi A (Built-in Artisan Server - Direkomendasikan):**
```powershell
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```
Buka browser di: **`http://127.0.0.1:8000`**

**Opsi B (Apache XAMPP):**
Melalui symbolic junction `C:\xampp\htdocs\pos-al-hidayah` yang mengarah ke `C:\POS_Toko_AlHidayah`:
Buka browser di: **`http://localhost/pos-al-hidayah/public`**

---

## 5. Menjalankan Rangkaian Pengujian (Testing)

Aplikasi dilengkapi dengan 11 skenario pengujian otomatis (*Feature Tests*) menggunakan PHPUnit & database SQLite `:memory:`:
```powershell
C:\xampp\php\php.exe artisan test
```

### Skenario yang Teruji:
1. `test_login_with_valid_credentials`: Autentikasi akun berhasil dan dialihkan ke dashboard.
2. `test_the_application_returns_a_successful_response`: Akses root dialihkan 302 ke `/login`.
3. `test_admin_route_forbidden_for_cashier`: Otorisasi server-side memastikan kasir menerima HTTP 403 saat mengakses modul admin.
4. `test_tiered_pricing_applied_at_correct_qty`: Pengujian kalkulasi harga otomatis pada tingkat eceran, grosir, dan super grosir.
5. `test_checkout_success`: Pengujian proses checkout lengkap (pembuatan faktur, pengurangan stok, pembuatan snapshot `sale_items`, dan mutasi stok).
6. `test_checkout_fails_insufficient_stock`: Penolakan checkout jika kuantitas melebihi sisa stok fisik.
7. `test_cashier_discount_limit_enforced`: Penolakan diskon kasir yang melebihi batas 10%.
8. `test_cancel_sale_restores_stock`: Pembatalan penjualan oleh admin mengembalikan stok fisik secara akurat.
9. `test_shift_difference_calculation`: Perhitungan selisih kas fisik vs kas yang diharapkan pada shift kasir.
10. `test_invoice_number_uniqueness`: Pembuatan nomor faktur berurutan dengan format `INV-YYYYMMDD-NNNN`.

---

## 6. Struktur Direktori Proyek

```
app/
├── Exports/
│   └── SalesExport.php             # Export laporan ke format Excel
├── Helpers/
│   └── helpers.php                 # Helper format_rupiah() & tier_label()
├── Http/
│   ├── Controllers/                # Controllers untuk seluruh modul
│   └── Middleware/
│       └── EnsureUserIsActive.php  # Cek status aktif pengguna
├── Livewire/
│   └── PosTerminal.php             # Komponen Livewire Kasir POS
├── Models/                         # Eloquent Models & relasi database
├── Providers/
│   └── AppServiceProvider.php     # Gate otorisasi & @currency directive
└── Services/
    └── PosService.php              # Logika transaksi checkout & pembatalan
resources/
├── css/app.css                     # Tailwind CSS v4
├── js/app.js                       # Javascript entry
└── views/                          # Blade templates & layouts
```
