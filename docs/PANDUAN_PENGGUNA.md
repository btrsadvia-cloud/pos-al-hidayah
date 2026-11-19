# PANDUAN PENGGUNA (USER MANUAL)
## Sistem Kasir (Point of Sale) & Inventori - Toko Al-Hidayah

Dokumen ini memandu kasir dan pemilik (administrator) dalam mengoperasikan aplikasi POS Toko Al-Hidayah untuk aktivitas operasional harian.

---

## DAFTAR ISI
1. [Panduan Masuk (Login)](#1-panduan-masuk-login)
2. [Alur Kerja Kasir](#2-alur-kerja-kasir)
   - [Membuka Shift Kasir](#a-membuka-shift-kasir)
   - [Mengoperasikan Layar Kasir (POS)](#b-mengoperasikan-layar-kasir-pos)
   - [Pintasan Keyboard (Shortcuts)](#c-pintasan-keyboard-shortcuts)
   - [Mencetak Struk Belanja](#d-mencetak-struk-belanja)
   - [Melihat Riwayat Penjualan Pribadi](#e-melihat-riwayat-penjualan-pribadi)
   - [Menutup Shift Kasir](#f-menutup-shift-kasir)
3. [Alur Kerja Pemilik (Administrator)](#3-alur-kerja-pemilik-administrator)
   - [Dashboard & Analitik Omzet](#a-dashboard--analitik-omzet)
   - [Manajemen Master Data Produk & Harga Bertingkat](#b-manajemen-master-data-produk--harga-bertingkat)
   - [Manajemen Pemasok (Supplier) & Pelanggan](#c-manajemen-pemasok-supplier--pelanggan)
   - [Manajemen Mutasi Inventori & Kartu Stok](#d-manajemen-mutasi-inventori--kartu-stok)
   - [Pembatalan Transaksi Penjualan](#e-pembatalan-transaksi-penjualan)
   - [Laporan Penjualan (Ekspor PDF & Excel)](#f-laporan-penjualan-ekspor-pdf--excel)
   - [Manajemen Akun Pengguna](#g-manajemen-akun-pengguna)

---

## 1. Panduan Masuk (Login)

1. Buka browser dan kunjungi alamat aplikasi:
   - **`http://127.0.0.1:8000`** (Server Lokal) atau
   - **`http://localhost/pos-al-hidayah/public`** (Apache XAMPP).
2. Sistem akan mengarahkan Anda ke halaman login `/login`.
3. Masukkan **Username** dan **Password** Anda:
   - Akun Pemilik (Admin): `admin` / `password`
   - Akun Kasir: `kasir1` / `password`
4. Klik tombol **Masuk**.
5. Sistem secara otomatis mengarahkan Anda ke Dashboard sesuai dengan hak akses peran Anda.

---

## 2. Alur Kerja Kasir

### A. Membuka Shift Kasir
Kasir **wajib** membuka shift sebelum dapat mengakses layar POS.
1. Masuk ke menu **Manajemen Shift** (atau klik tautan peringatan pada layar POS).
2. Pada formulir **Buka Shift Baru**, masukkan nominal **Kas Awal** (uang pecahan/modal kembalian di laci kasir, bebas disesuaikan sendiri).
3. Klik tombol **Buka Shift Baru**.
4. Status shift Anda akan berubah menjadi **Shift Aktif** (indikator hijau).

### B. Mengoperasikan Layar Kasir (POS)
Buka menu **Layar Kasir (POS)**. Antarmuka terbagi menjadi 3 area:

#### 1. Area Katalog Produk (Kiri):
- **Pencarian / Scan Barcode:** Kolom pencarian otomatis fokus saat halaman dibuka. Gunakan barcode scanner fisik (atau ketik barcode lalu tekan `Enter`) untuk langsung menambahkan produk ke keranjang.
- **Filter Kategori:** Klik tombol kategori di atas katalog (Busana Muslimah, Seragam Sekolah, Alat Sholat, Haji dan Umroh) untuk menyaring produk.
- **Kartu Produk:** Menampilkan nama, harga eceran, barcode, serta lencana stok. Produk yang habis stoknya tidak dapat diklik.

#### 2. Area Keranjang Belanja (Tengah):
- **Jumlah Pembelian (+ / -):** Klik tombol `+` atau `-` (area sentuh dirancang ramah layar sentuh $\ge 44\text{px}$) atau ketik jumlah pada kotak angka.
- **Penerapan Harga Bertingkat Otomatis:**
  - Jika kuantitas $\ge$ batas grosir (misal $\ge$ 12 pcs), harga satuan otomatis berubah ke **Harga Grosir** (lencana biru).
  - Jika kuantitas $\ge$ batas super grosir (misal $\ge$ 36 pcs), harga satuan otomatis berubah ke **Harga Super Grosir** (lencana ungu).
- **Diskon Item:** Masukkan potongan harga per baris produk pada kolom *Disc* jika ada promo khusus barang.
- **Hapus Item:** Klik ikon tempat sampah untuk mengeluarkan barang dari keranjang.

#### 3. Area Pembayaran (Kanan):
- **Pilih Pelanggan:** Pilih pelanggan tetap / member dari daftar pilihan, atau biarkan kosong untuk *Pembeli Umum*.
- **Diskon Faktur:** Masukkan potongan harga total jika ada (Kasir dibatasi maksimal diskon 10% dari nilai belanja).
- **Metode Pembayaran:**
  - **Tunai:** Masukkan nominal uang yang diterima. Anda dapat mengklik tombol uang cepat (*Pas*, *Rp 50.000*, *Rp 100.000*, *Rp 200.000*, *Rp 500.000*). Kembalian akan dihitung secara otomatis.
  - **QRIS:** Pembayaran melalui scan QRIS (nominal pas otomatis), masukkan nomor referensi/RRN jika diperlukan.
  - **Transfer Bank:** Pembayaran via transfer, masukkan nomor bukti transfer pada kolom referensi.
- **Catatan Transaksi:** Tambahkan keterangan khusus jika ada (opsional).
- **Bayar (Checkout):** Tekan tombol **BAYAR** atau tekan tombol keyboard **`F9`**.

### C. Pintasan Keyboard (Shortcuts)
Untuk mempercepat transaksi kasir harian:
- **`F2`** : Langsung fokus ke kolom pencarian / scan barcode
- **`F8`** : Langsung fokus ke kolom diskon faktur
- **`F9`** : Melakukan checkout / pembayaran
- **`Esc`**: Menutup dialog struk / membatalkan dialog
- **`?`**  : Membuka panduan daftar tombol pintasan

### D. Mencetak Struk Belanja
1. Begitu pembayaran berhasil, modal konfirmasi transaksi selesai akan muncul disertai pratinjau struk belanja.
2. Klik tombol **Cetak Struk** untuk mencetak langsung ke printer thermal (ukuran 58mm atau 80mm).
3. Klik tombol **Tutup (Esc)** untuk menutup jendela dan memulai transaksi pelanggan berikutnya.
4. Jika pelanggan memerlukan cetak ulang struk di kemudian hari, buka menu **Riwayat Penjualan**, pilih transaksi terkait, lalu klik tombol **Struk**.

### E. Melihat Riwayat Penjualan Pribadi
1. Buka menu **Riwayat Penjualan**.
2. Kasir dapat melihat daftar seluruh transaksi yang dilakukan oleh akun kasir tersebut.
3. Anda dapat menyaring transaksi berdasarkan rentang tanggal atau metode pembayaran.

### F. Menutup Shift Kasir
Pada akhir jam kerja / pergantian shift kasir:
1. Buka menu **Manajemen Shift**.
2. Hitung fisik seluruh uang tunai yang ada di laci kasir (uang modal awal + total uang tunai hasil penjualan).
3. Masukkan jumlah uang tunai fisik pada kolom **Kas Hasil Hitung Fisik**.
4. Klik tombol **Tutup Shift Sekarang**.
5. Sistem akan menampilkan rekapitulasi:
   - Kas Awal
   - Total Penjualan Tunai
   - Kas yang Diharapkan
   - Kas Fisik yang Dihitung
   - **Selisih Kas:** Ditandai *Pas*, *Lebih Rp X*, atau *Kurang Rp X*.

---

## 3. Alur Kerja Pemilik (Administrator)

Pemilik toko memiliki akses ke seluruh fitur kasir ditambah fitur manajerial dan analitik.

### A. Dashboard & Analitik Omzet
Pada halaman beranda Admin:
- **Ringkasan Hari Ini:** Omzet harian bersih, jumlah transaksi berhasil, dan jumlah produk dengan stok menipis.
- **Grafik Tren Omzet 30 Hari:** Grafik visual tren omzet harian untuk memantau performa penjualan harian.
- **5 Produk Terlaris:** Daftar barang paling laku dalam 30 hari terakhir berdasarkan kuantitas terjual.
- **Pelanggan Paling Loyal:** Daftar pelanggan dengan frekuensi belanja dan total belanja tertinggi.

### B. Manajemen Master Data Produk & Harga Bertingkat
#### Menambah Produk Baru:
1. Masuk ke menu **Katalog Produk** $\rightarrow$ klik **+ Tambah Produk Baru**.
2. Isi informasi produk: Nama, Barcode (dapat discan langsung), Kategori, Satuan Ukur, Stok Awal, dan Batas Minimum Stok.
3. Unggah foto produk jika diperlukan (format PNG/JPG maks 2MB).
4. Atur **3 Tingkat Harga**:
   - **Tingkat 1 (Eceran):** Harga per pcs (minimal kuantitas: 1).
   - **Tingkat 2 (Grosir):** Harga diskon kuantitas (contoh minimal kuantitas: 12 pcs / 1 lusin).
   - **Tingkat 3 (Super Grosir):** Harga partai/borongan (contoh minimal kuantitas: 36 pcs / 1 kodi / 3 lusin).
5. Klik **Simpan Produk**.

#### Mengubah Harga Bertingkat:
Klik tautan **Harga** pada baris produk di tabel katalog untuk langsung mengubah harga ketiga tier tanpa mengganggu data produk lainnya.

### C. Manajemen Pemasok (Supplier) & Pelanggan
- **Pemasok:** Masuk ke menu **Pemasok (Supplier)** untuk menambah data mitra konveksi / pabrikan kain, lengkap dengan kontak, alamat, dan nomor rekening bank.
- **Pelanggan:** Masuk ke menu **Data Pelanggan** untuk mendaftarkan member tetap toko.

### D. Manajemen Mutasi Inventori & Kartu Stok
- **Mencatat Stok Masuk (Pembelian):**
  1. Buka menu **Stok Masuk (Beli)**.
  2. Pilih produk, pemasok, masukkan jumlah barang yang diterima, harga beli satuan (HPP), tanggal penerimaan, dan nomor surat jalan.
  3. Klik **Simpan Stok Masuk**. Stok fisik produk akan bertambah secara otomatis dan HPP terakhir diperbarui.
- **Mencatat Stok Keluar (Manual):**
  1. Buka menu **Stok Keluar (Rusak/Lain)**.
  2. Pilih produk dan jumlah pengurangan.
  3. Masukkan **Alasan Pengeluaran** secara rinci (misal: "Kain robek pada jahitan lengan", "Sample etalase display", "Hilang saat stok opname").
  4. Klik **Kurangi Stok**.
- **Memeriksa Kartu Stok (Stock Ledger):**
  Buka menu **Mutasi Stok** $\rightarrow$ klik nama produk atau tombol **Kartu**. Sistem menampilkan buku mutasi stok lengkap dengan saldo berjalan (*running balance*).

### E. Pembatalan Transaksi Penjualan
Jika terjadi kesalahan transaksi atau pembatalan belanja oleh konsumen:
1. Buka menu **Riwayat Penjualan** $\rightarrow$ klik nomor faktur yang ingin dibatalkan.
2. Klik tombol **Batalkan Penjualan**.
3. Masukkan alasan pembatalan secara jelas pada dialog konfirmasi.
4. Klik **Ya, Batalkan Transaksi**.
5. Status penjualan akan berubah menjadi **Dibatalkan** (`cancelled`), seluruh barang dikembalikan ke stok fisik melalui mutasi stok masuk baru, dan log audit tercatat secara permanen. Transaksi tidak pernah dihapus dari basis data.

### F. Laporan Penjualan (Ekspor PDF & Excel)
1. Buka menu **Laporan Penjualan**.
2. Pilih rentang tanggal (**Dari Tanggal** dan **Sampai Tanggal**).
3. Klik **Tampilkan Laporan**.
4. Halaman menampilkan:
   - Total Omzet Bersih
   - Total Transaksi & Rata-rata per Transaksi
   - Total Diskon yang Diberikan
   - Distribusi Metode Pembayaran (Tunai, QRIS, Transfer)
   - Tabel Rincian Faktur
5. Untuk mencetak / mengunduh dokumen laporan:
   - Klik **Ekspor PDF** untuk mengunduh laporan siap cetak ukuran A4.
   - Klik **Ekspor Excel (.xlsx)** untuk mengunduh berkas spreadsheet lengkap dengan format kolom rapi.

### G. Manajemen Akun Pengguna
1. Buka menu **Kelola Pengguna**.
2. Admin dapat menambahkan akun kasir baru dengan mengklik **+ Tambah Pengguna Baru**.
3. Jika seorang kasir sudah tidak bekerja lagi, klik tautan **Nonaktifkan** pada baris pengguna. Akun tersebut tidak akan dapat masuk kembali ke sistem, namun seluruh riwayat penjualan yang pernah dibuatnya tetap utuh dan tersimpan di basis data.

### H. Konfigurasi & Penggunaan Gateway QRIS (BuatQris)
1. **Pengaturan Kredensial (Admin):**
   - Masuk sebagai Admin $\rightarrow$ buka menu **Pengaturan QRIS** di sidebar.
   - Masukkan **Account ID** dan **Secret Token** (diawali `sk_live_`) yang diperoleh dari dasbor [BuatQris](https://app.buatqris.site/dasbort) (menu *Open API* $\rightarrow$ *Kredensial API*).
   - Masukkan **Signing Secret** (dari kartu *Webhook / Callback* di dasbor BuatQris).
   - Tentukan tanggungan fee: dipotong dari saldo toko (default) atau dibebankan ke pembeli.
   - Klik **Simpan Konfigurasi**.
2. **Menyambungkan Webhook (Opsional untuk domain publik/ngrok):**
   - Salin URL Webhook yang ditampilkan di halaman Pengaturan QRIS (`http://domain-anda/api/buatqris/webhook`).
   - Tempel ke kartu *Webhook / Callback* di dasbor BuatQris.
3. **Melakukan Pembayaran QRIS di Kasir:**
   - Di layar POS kasir, pilih produk dan pilih metode pembayaran **QRIS**.
   - Klik tombol **BUAT KODE QRIS (F9)**.
   - Pop-up modal QRIS dinamis akan muncul menampilkan QR Code, total tagihan persis (termasuk kode unik verifikasi), dan penghitung waktu kedaluwarsa.
   - Tunjukkan QR code kepada pembeli untuk di-scan dengan e-wallet (GoPay, OVO, DANA, ShopeePay) atau mobile banking (BCA, Mandiri, BRI, BNI).
   - Sistem akan memantau pembayaran. Begitu pembeli membayar, sistem otomatis mendeteksi pelunasan, menyelesaikan transaksi, dan langsung memunculkan struk belanja thermal.
   - Kasir juga dapat menekan tombol **🔍 Cek Status Sekarang** untuk memeriksa status seketika ke gateway.

---

*Hak Cipta &copy; 2026 Toko Al-Hidayah. Seluruh hak cipta dilindungi undang-undang.*

