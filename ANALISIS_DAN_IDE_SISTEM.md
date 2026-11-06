# Analisis Dokumen & Ide Pengembangan Sistem POS Toko Al-Hidayah

Dokumen ini disusun berdasarkan hasil pembacaan dan analisis menyeluruh terhadap seluruh dokumentasi visual (74 halaman dokumen skripsi) yang tersimpan di dalam folder `img` pada proyek **POS_Toko_AlHidayah**.

---

## 1. Profil & Latar Belakang Proyek

- **Judul Asli Dokumen**: *Sistem Informasi Point of Sale (POS) Pada Toko Al-Hidayah Berbasis Web*
- **Peneliti/Penyusun**: Dimas Pandu Prayogo (NPM: 20430015)
- **Instansi**: Program Studi S1 Ilmu Komputer, Fakultas Ilmu Komputer, Universitas Muhammadiyah Metro (Tahun 2024)
- **Objek Penelitian**: **Toko Al-Hidayah Diponegoro**
  - **Lokasi**: Jl. Diponegoro, Kel. Imopuro, Kec. Metro Pusat, Kota Metro, Lampung.
  - **Profil Usaha**: Usaha ritel & grosir tekstil busana muslimah, pakaian seragam sekolah, pakaian dinas/kantor, serta perlengkapan haji, umroh, dan alat sholat. (Cabang dari toko induk Pasar Shopping yang berdiri sejak 1970).
  - **Volume Transaksi**: Rata-rata 50 transaksi per hari (~1.500 produk terjual per bulan).
  - **Narasumber/Pemilik**: Bapak Irfan Arif.

---

## 2. Permasalahan Nyata di Lapangan (Pain Points)

Berdasarkan hasil wawancara dan analisis sistem berjalan di toko:
1. **Pencatatan Konvensional yang Rentan**: Transaksi kasir masih dicatat manual di buku dan nota kertas fisik.
2. **Risiko Nota Hilang & Human Error**: Sering terjadi nota terselip atau hilang (terutama transaksi kecil), perhitungan total salah, dan selisih kas fisik di akhir hari.
3. **Buku Catatan Sering Lupa Diletakkan**: Ketika pergantian kasir atau karyawan bergantian melayani pelanggan, buku catatan penjualan sering tidak terisi.
4. **Stok Tidak Terkontrol Real-Time**: Tidak ada sistem otomatis yang memotong kuantitas stok saat terjadi penjualan, sehingga sering terjadi kehabisan stok tanpa terdeteksi.
5. **Rekapitulasi Laporan Memakan Waktu**: Pemilik sulit mengetahui omset harian/bulanan secara cepat tanpa menghitung tumpukan kertas nota satu per satu.

---

## 3. Analisis Kebutuhan Sistem (Sesuai Skripsi)

### A. Pembagian Hak Akses Pengguna (Role-Based Access Control)
Sistem memiliki 2 level pengguna:
1. **Admin (Pemilik Toko)**:
   - Akses menyeluruh ke seluruh sistem.
   - Melihat Dashboard Analitik lengkap: ringkasan data (Item, Kustomer, Sale, User), produk terlaris (*best-seller*), dan pelanggan dengan pembelian terbanyak (*loyal customer*).
   - Mengelola Master Data: Kategori, Unit/Satuan, Data Barang/Item.
   - Mengelola Transaksi: Kasir POS, Stock In (Barang Masuk), Stock Out (Barang Keluar/Rusak).
   - Mengelola Laporan Penjualan (Detail Transaksi, Rekapitulasi per Periode Tanggal, Cetak Laporan PDF).
   - Mengelola User (Tambah/Edit/Hapus akun Kasir & Admin).

2. **Kasir**:
   - Login sistem.
   - Melihat Dashboard Terbatas (hanya kartu ringkasan angka, tanpa data analitik produk/keuntungan sensitif).
   - Mengelola Data Pelanggan/Kustomer.
   - Mengelola Transaksi Kasir (POS) & Cetak Struk Belanja (*Thermal Receipt*).
   - Menginput Data Barang Masuk / Keluar (Stock In / Stock Out).
   - Melihat Riwayat Transaksi Kasir.
   - *Tidak memiliki akses ke manajemen pengguna dan pengaturan sistem level admin.*

---

## 4. Struktur Database Asli (9 Tabel)

Berdasarkan *Class Diagram* dan *Relasi Tabel Database (Gambar 31)*:

1. **`user`**:
   - `user_id` (PK, int)
   - `username` (varchar 40)
   - `password` (varchar 40, hashing)
   - `name` (varchar 50)
   - `address` (varchar 100)
   - `level` (int 1: 1=Admin, 2=Kasir)

2. **`kustomer`**:
   - `kustomer_id` (PK, int)
   - `name` (varchar 50)
   - `gender` (enum: 'L', 'P')
   - `phone` (varchar 15)
   - `address` (text)

3. **`p_kategori`**:
   - `kategori_id` (PK, int)
   - `name` (varchar 50)

4. **`p_unit`**:
   - `unit_id` (PK, int)
   - `name` (varchar 50) *(Contoh: Pcs, Lusin, Kodi, Meter, Stel)*

5. **`p_item`**:
   - `item_id` (PK, int)
   - `barcode` (varchar 50, unique)
   - `name` (varchar 50)
   - `kategori_id` (FK -> `p_kategori.kategori_id`)
   - `unit_id` (FK -> `p_unit.unit_id`)
   - `harga` (int 11)
   - `stock` (int 10)
   - `image` (varchar 100)

6. **`t_stock`**:
   - `stock_id` (PK, int)
   - `item_id` (FK -> `p_item.item_id`)
   - `type` (enum: 'in', 'out')
   - `detail` (varchar 200) *(Contoh: 'Kulakan Pasar Klewer', 'Barang Rusak/Cacat', 'Peralatan Haji Masuk')*
   - `qty` (int 10)
   - `date` (date)
   - `created` (datetime)
   - `user_id` (FK -> `user.user_id`)

7. **`t_cart`** *(Temporary Cart / Session Keranjang)*:
   - `cart_id` (PK, int)
   - `item_id` (FK -> `p_item.item_id`)
   - `harga` (int 11)
   - `qty` (int 11)
   - `diskon_item` (int 11)
   - `total` (int 11)
   - `user_id` (FK -> `user.user_id`)

8. **`t_sale`** *(Header Transaksi Penjualan)*:
   - `sale_id` (PK, int)
   - `invoice` (varchar 50, unique) *(Format: misal `MP2407280001`)*
   - `kustomer_id` (FK -> `kustomer.kustomer_id`, nullable untuk kustomer umum/walk-in)
   - `total_harga` (int 11)
   - `diskon` (int 11)
   - `final_harga` (int 11)
   - `cash` (int 11)
   - `uang_kembalian` (int 11)
   - `note` (text)
   - `date` (date)
   - `user_id` (FK -> `user.user_id`)
   - `created` (datetime)

9. **`t_sale_detail`** *(Item Rinci Transaksi Penjualan)*:
   - `sale_detail_id` (PK, int)
   - `sale_id` (FK -> `t_sale.sale_id`)
   - `item_id` (FK -> `p_item.item_id`)
   - `harga` (int 11)
   - `qty` (int 11)
   - `diskon_item` (int 11)
   - `total` (int 11)

---

## 5. Ide Pengembangan & Inovasi (Modern Enhancement Ideas)

Untuk menjadikan sistem ini aplikasi POS kelas produksi modern, andal, dan siap pakai, berikut ide-ide inovasi peningkatan:

### 1. Modul Supplier & Hutang Dagang (Saran Resmi Peneliti Bab V)
- **Problem**: Pada skripsi asli, barang masuk (*Stock In*) belum mencatat siapa supplier atau distributor pengirimnya.
- **Solusi**: Tambahkan master tabel `supplier` (`supplier_id`, `nama_supplier`, `kontak`, `alamat`, `rekening`). Setiap transaksi *Stock In* langsung terhubung ke supplier terkait, lengkap dengan harga modal/beli (*cost of goods sold / HPP*).

### 2. Multi-Tier Pricing (Eceran, Grosir, Super Grosir)
- **Problem**: Pada wawancara dengan Pemilik Toko (Bapak Irfan Arif), beliau menegaskan bahwa Toko Al-Hidayah melayani pembeli **eceran, grosir, dan super grosir** dengan harga yang berbeda. Namun skripsi aslinya hanya memiliki 1 kolom `harga` standar.
- **Solusi**: Tambahkan skema harga bertingkat:
  - `harga_eceran`
  - `harga_grosir` (minimal qty tertentu, misal $\ge$ 1 lusin)
  - `harga_super_grosir` (pembelian borongan / partai besar)
  Kasir tinggal memilih tipe harga atau otomatis terdiskon berdasarkan jumlah kuantiti yang dimasukkan.

### 3. Integrasi Hardware POS Modern
- **Scan Barcode Cepat**: Dukungan barcode scanner laser USB, scanner bluetooth nirkabel, atau kamera webcam/HP langsung dari browser menggunakan pustaka JavaScript (seperti `html5-qrcode`).
- **Cetak Struk Thermal Otomatis (58mm / 80mm)**: Cetak nota struk thermal kasir menggunakan format font ESC/POS yang ringkas, rapi, dan cepat tanpa harus membuka dialog printer bawaan browser yang lambat.
- **Shortcuts Keyboard Kasir**: Fitur tombol cepat kasir seperti `F2` (Pencarian barang), `F4` (Input jumlah), `F8` (Diskon), `F9` (Bayar/Tunai), `ESC` (Batalkan transaksi).

### 4. Manajemen Stok Cerdas & Peringatan Stok Minimum (*Low Stock Alert*)
- Indikator warna pada dashboard untuk barang yang stoknya di bawah batas minimum (misal $\le 5$ pcs).
- Rekomendasi otomatis barang yang harus segera di-order ulang (*Reorder Point*).

### 5. Metode Pembayaran Non-Tunai (QRIS & Transfer Bank)
- Pembeli masa kini sering bertransaksi tanpa uang tunai.
- Tambahkan kolom `metode_pembayaran` (`tunai`, `qris`, `transfer`) dan upload bukti atau referensi pembayaran.

### 6. Pilihan Rekomendasi Arsitektur & Teknologi

| Opsi | Tech Stack | Kelebihan |
| :--- | :--- | :--- |
| **Opsi A (Sesuai Skripsi Asli)** | **PHP (CodeIgniter 4 / Laravel 11) + MySQL + AdminLTE/Bootstrap 5** | Sangat cocok jika ingin 100% konsisten dengan laporan skripsi, mudah dideploy di XAMPP lokal, stabil, dan ramah pemula. |
| **Opsi B (Modern Fullstack)** | **Next.js 15 / React + Tailwind CSS + Node.js (Prisma/Drizzle ORM) + SQLite/PostgreSQL** | Sangat responsif, antarmuka interaktif instan (SPA feel), mudah dijadikan PWA (Progressive Web App) di tablet kasir. |
| **Opsi C (Laravel 11 + Livewire 3 / Filament POS)** | **Laravel + Livewire/Filament v3** | Pengembangan super cepat, UI modern out-of-the-box, validasi data kuat, keamanan enterprise-grade. |

---

## 6. Rencana Tahapan Eksekusi (Roadmap Pengerjaan)

1. **Tahap 1: Setup Lingkungan & Database**:
   - Inisialisasi basis data dengan skema 9 tabel utama + penambahan tabel supplier.
   - Seeding data awal: akun Admin, Kasir, kategori (Seragam, Busana Muslim, Alat Sholat, Haji/Umroh), satuan unit, dan beberapa sampel produk.

2. **Tahap 2: Autentikasi & Antarmuka Utama**:
   - Halaman login dengan proteksi session dan otorisasi role (Admin vs Kasir).
   - Layout dashboard yang responsif (desktop, tablet, mobile).

3. **Tahap 3: Master Data Management (CRUD)**:
   - Modul Kategori & Satuan Unit.
   - Modul Produk Item (dilengkapi unggah foto produk & input barcode).
   - Modul Pelanggan / Kustomer & Supplier.

4. **Tahap 4: Modul Inventori (Stock Management)**:
   - Fitur Stock In (penerimaan barang dari supplier & update stok otomatis).
   - Fitur Stock Out (pengurangan stok karena barang rusak, hilang, dsb.).
   - Kartu stok / riwayat mutasi per item.

5. **Tahap 5: Modul Kasir / POS (Point of Sale)**:
   - Pencarian produk instan (berdasarkan nama atau scan barcode).
   - Keranjang belanja interaktif (ubah qty, diskon per item).
   - Perhitungan otomatis subtotal, diskon nota, total tagihan, uang tunai, dan kembalian.
   - Cetak struk kasir (*Print Preview / Thermal Layout*).

6. **Tahap 6: Laporan & Analitik**:
   - Laporan transaksi penjualan per rentang tanggal.
   - Filter laporan berdasarkan kasir, metode pembayaran, atau status.
   - Export laporan ke PDF dan Excel.
   - Grafik dashboard: tren penjualan, produk paling laris, dan pelanggan teraktif.

7. **Tahap 7: Pengujian & Dokumentasi**:
   - Pengujian fungsionalitas (Blackbox Testing sesuai format skripsi).
   - Dokumentasi cara menjalankan aplikasi dan buku panduan pengguna.


