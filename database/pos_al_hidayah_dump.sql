-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Nov 2026 pada 05.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_al_hidayah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `subject_type` varchar(100) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `subject_type`, `subject_id`, `old_values`, `new_values`, `created_at`) VALUES
(1, 1, 'Inisialisasi Sistem & Seeder Data Toko Al-Hidayah', 'App\\Models\\User', 1, NULL, '{\"status\":\"Data awal berhasil di-seed\"}', '2026-11-06 05:36:02'),
(2, 1, 'Inisialisasi Sistem & Seeder Data Toko Al-Hidayah', 'App\\Models\\User', 1, NULL, '{\"status\":\"Data awal berhasil di-seed\"}', '2026-11-06 07:23:48'),
(3, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 1, NULL, '{\"invoice\":\"INV-20261106-0001\",\"total\":660000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 07:48:15'),
(4, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 2, NULL, '{\"invoice\":\"INV-20261106-0002\",\"total\":115000,\"payment_method\":\"transfer\",\"items_count\":1}', '2026-11-06 08:35:45'),
(5, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 3, NULL, '{\"invoice\":\"INV-20261106-0003\",\"total\":15000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:39:13'),
(6, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 4, NULL, '{\"invoice\":\"INV-20261106-0004\",\"total\":15000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:41:21'),
(7, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 5, NULL, '{\"invoice\":\"INV-20261106-0005\",\"total\":105000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:41:50'),
(8, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 6, NULL, '{\"invoice\":\"INV-20261106-0006\",\"total\":15000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:44:22'),
(9, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 7, NULL, '{\"invoice\":\"INV-20261106-0007\",\"total\":15000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:46:43'),
(10, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 8, NULL, '{\"invoice\":\"INV-20261106-0008\",\"total\":15000,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-06 08:53:15'),
(11, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 9, NULL, '{\"invoice\":\"INV-20261106-0009\",\"total\":45,\"payment_method\":\"cash\",\"items_count\":1}', '2026-11-06 09:45:09'),
(12, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 10, NULL, '{\"invoice\":\"INV-20261106-0010\",\"total\":45,\"payment_method\":\"cash\",\"items_count\":1}', '2026-11-06 09:48:00'),
(13, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 11, NULL, '{\"invoice\":\"INV-20261106-0011\",\"total\":45,\"payment_method\":\"cash\",\"items_count\":1}', '2026-11-06 09:48:50'),
(14, 2, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 12, NULL, '{\"invoice\":\"INV-20261112-0001\",\"total\":630,\"payment_method\":\"cash\",\"items_count\":2}', '2026-11-12 05:15:47'),
(15, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 13, NULL, '{\"invoice\":\"INV-20261112-0002\",\"total\":1150,\"payment_method\":\"qris\",\"items_count\":3}', '2026-11-12 05:58:30'),
(16, 1, 'Stok Masuk', 'App\\Models\\StockMovement', 34, NULL, '{\"product\":\"Seragam SD Merah Putih Lengan Pendek\",\"qty\":10,\"cost\":90}', '2026-11-12 07:20:08'),
(17, 1, 'Stok Masuk', 'App\\Models\\StockMovement', 35, NULL, '{\"product\":\"Gamis Syar\'i Ceruty Babydoll 2 Layer\",\"qty\":\"55\",\"cost\":\"80\"}', '2026-11-12 07:30:33'),
(18, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 14, NULL, '{\"invoice\":\"INV-20261112-0003\",\"total\":1750,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-12 08:07:43'),
(19, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 15, NULL, '{\"invoice\":\"INV-20261112-0004\",\"total\":1250,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-12 08:13:43'),
(20, 1, 'Transaksi Penjualan Selesai', 'App\\Models\\Sale', 16, NULL, '{\"invoice\":\"INV-20261112-0005\",\"total\":1040,\"payment_method\":\"qris\",\"items_count\":1}', '2026-11-12 08:14:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Seragam Sekolah', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(2, 'Busana Muslimah', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(3, 'Alat Sholat', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(4, 'Haji dan Umroh', '2026-11-06 05:36:01', '2026-11-06 05:36:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` enum('L','P') DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `gender`, `phone`, `address`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ibu Hj. Aminah', 'P', '081234567890', 'Jl. Jendral Sudirman No. 45, Metro Pusat', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(2, 'Bpk. Ir. H. Ahmad Fauzi', 'L', '082198765432', 'Kedaton, Kota Bandar Lampung', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(3, 'Ibu Siti Rahmawati', 'P', '085712345678', 'Jl. AH Nasution No. 88, Kec. Metro Barat', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(4, 'Bpk. Muhammad Ridwan', 'L', '081399887766', 'Kel. Imopuro RT 02/01, Metro Pusat', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(5, 'Yayasan Pendidikan Al-Khoiriyah', 'L', '082211443322', 'Jl. Ki Hajar Dewantara, Metro Timur', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_customers_table', 1),
(5, '2026_01_01_000002_create_categories_table', 1),
(6, '2026_01_01_000003_create_units_table', 1),
(7, '2026_01_01_000004_create_suppliers_table', 1),
(8, '2026_01_01_000005_create_products_table', 1),
(9, '2026_01_01_000006_create_product_prices_table', 1),
(10, '2026_01_01_000007_create_shifts_table', 1),
(11, '2026_01_01_000008_create_stock_movements_table', 1),
(12, '2026_01_01_000009_create_sales_table', 1),
(13, '2026_01_01_000010_create_sale_items_table', 1),
(14, '2026_01_01_000011_create_audit_logs_table', 1),
(15, '2026_01_01_000012_create_qris_transactions_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `username` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `barcode`, `name`, `category_id`, `unit_id`, `image`, `stock`, `min_stock`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '8991001001', 'Seragam SD Merah Putih Lengan Pendek', 1, 5, 'products/u4XypOCVUG1YQhhKpL7pANmfK1TjVNSrF7hc8ilw.webp', 9, 5, '2026-11-06 05:36:01', '2026-11-12 08:14:48', NULL),
(2, '8991001002', 'Seragam SMP Biru Putih Putra', 1, 5, 'products/rC9s6MdpcwwDLsEtJ6FABXVKK9YUgZ38SV8HRQFk.webp', 18, 5, '2026-11-06 05:36:01', '2026-11-12 07:32:13', NULL),
(3, '8991001003', 'Seragam SMA Putih Abu-Abu Putri', 1, 5, 'products/mNQPPZh626SaN54sOkgIUN6JAqolwGZDMLEbV7ik.webp', 1, 5, '2026-11-06 05:36:01', '2026-11-12 07:32:24', NULL),
(4, '8991001004', 'Seragam Pramuka Lengkap Penggalang', 1, 5, 'products/w3wMJ4eTAqWji0re1A3RkemHzavPZ8XRiYdDqG3B.webp', 14, 5, '2026-11-06 05:36:01', '2026-11-12 07:32:36', NULL),
(5, '8991001005', 'Dasi & Topi Bordir Sekolah', 1, 1, 'products/QRgaJFvf3T4nhhR9zFbRsMMfj3NwmjGUaZbRrigX.webp', 35, 10, '2026-11-06 05:36:01', '2026-11-12 08:13:43', NULL),
(6, '8992002001', 'Gamis Rayon Viscose Polos Premium', 2, 1, 'products/dIQexl30Al1m0p5sakETyaQ93kOpsKpTBrl0infA.webp', 19, 5, '2026-11-06 05:36:01', '2026-11-12 07:57:28', NULL),
(7, '8992002002', 'Gamis Syar\'i Ceruty Babydoll 2 Layer', 2, 5, 'products/yQvUdLH5UOOPxogYeEpVeomT4pqypSukxSaavhLW.webp', 48, 5, '2026-11-06 05:36:01', '2026-11-12 08:07:43', NULL),
(8, '8992002003', 'Jilbab Bergo Maryam Diamond Crepe', 2, 1, 'products/Cby4wUrpFTsAWtUJGAB1GIWlBKnT9Ns4HyLKBWZQ.webp', 4, 10, '2026-11-06 05:36:01', '2026-11-12 07:57:52', NULL),
(9, '8992002004', 'Pashmina Ceruty Baby Doll Shawl', 2, 1, 'products/F1FCWi4pXZM7LwsVEKvCwaDOsq0QDJyWoFEgn6PO.webp', 35, 10, '2026-11-06 05:36:01', '2026-11-17 22:34:36', NULL),
(10, '8992002005', 'Tunik Muslimah Katun Bordir', 2, 1, 'products/qa9vqGzb4YCFClU6v5ozoSeLo5SgvwOKYAutFFaY.webp', 12, 5, '2026-11-06 05:36:01', '2026-11-17 22:35:45', NULL),
(11, '8993003001', 'Mukena Katun Mikro Renda Mewah', 3, 1, 'products/81OxXsQGQsSp3MuGQooJ1N2hRvSZIISYKmlM3hyh.webp', 15, 5, '2026-11-06 05:36:02', '2026-11-12 07:24:43', NULL),
(12, '8993003002', 'Sajadah Turkey Tebal Chenille Bludru', 3, 1, 'products/BtswvLw5SjBmTevyZZWWDLODDiN2aSAC1NRkJsoP.webp', 2, 5, '2026-11-06 05:36:02', '2026-11-12 07:25:10', NULL),
(13, '8993003003', 'Sarung Tenun Wadimor Padang Premium', 3, 1, 'products/mEMWKElJTDdINtYko9chN1SdrhOLAQafM93wBIkw.webp', 28, 10, '2026-11-06 05:36:02', '2026-11-12 07:31:02', NULL),
(14, '8993003004', 'Peci Songkok Hitam Nasional AC', 3, 1, 'products/WAGdIJuFuVBxOUNfRcCtVGRBApazSmxBXMOVbCUg.webp', 27, 10, '2026-11-06 05:36:02', '2026-11-12 07:31:12', NULL),
(15, '8994004001', 'Kain Ihram Pria Dewasa Katun Jacquard', 4, 5, 'products/suh8c2uK9Rc1KBWdegutH5DndlkFHuAh9Xst5MXI.webp', 12, 5, '2026-11-06 05:36:02', '2026-11-12 07:31:25', NULL),
(16, '8994004002', 'Set Gamis & Mukena Putih Umroh Paris', 4, 5, 'products/urAwddi8lm3PgeZ030vgEShPoYUFiuluVDh1QJ56.webp', 10, 5, '2026-11-06 05:36:02', '2026-11-12 07:31:37', NULL),
(17, '8994004003', 'Sabuk Haji Gesper Tawaf Hijau', 4, 1, 'products/vfRZ5ExCXmxAgn1QmSu2cc3d6vwOMlTQlSvEPWof.webp', 1, 5, '2026-11-06 05:36:02', '2026-11-12 07:31:50', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_prices`
--

CREATE TABLE `product_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `tier` enum('retail','wholesale','super_wholesale') NOT NULL,
  `min_qty` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `price` bigint(20) UNSIGNED NOT NULL COMMENT 'Price in Rupiah integer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `product_prices`
--

INSERT INTO `product_prices` (`id`, `product_id`, `tier`, `min_qty`, `price`, `created_at`, `updated_at`) VALUES
(1, 1, 'retail', 1, 90, '2026-11-06 05:36:01', '2026-11-12 07:20:34'),
(2, 1, 'wholesale', 12, 80, '2026-11-06 05:36:01', '2026-11-12 07:20:34'),
(3, 1, 'super_wholesale', 36, 70, '2026-11-06 05:36:01', '2026-11-12 07:20:34'),
(4, 2, 'retail', 1, 60, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(5, 2, 'wholesale', 12, 50, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(6, 2, 'super_wholesale', 36, 40, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(7, 3, 'retail', 1, 85, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(8, 3, 'wholesale', 12, 70, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(9, 3, 'super_wholesale', 36, 55, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(10, 4, 'retail', 1, 100, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(11, 4, 'wholesale', 12, 85, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(12, 4, 'super_wholesale', 36, 70, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(13, 5, 'retail', 1, 125, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(14, 5, 'wholesale', 12, 110, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(15, 5, 'super_wholesale', 36, 95, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(16, 6, 'retail', 1, 150, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(17, 6, 'wholesale', 12, 130, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(18, 6, 'super_wholesale', 36, 115, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(19, 7, 'retail', 1, 175, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(20, 7, 'wholesale', 12, 150, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(21, 7, 'super_wholesale', 36, 135, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(22, 8, 'retail', 1, 200, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(23, 8, 'wholesale', 12, 180, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(24, 8, 'super_wholesale', 36, 160, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(25, 9, 'retail', 1, 30, '2026-11-06 05:36:01', '2026-11-06 09:10:53'),
(26, 9, 'wholesale', 12, 25, '2026-11-06 05:36:01', '2026-11-06 09:10:54'),
(27, 9, 'super_wholesale', 36, 18, '2026-11-06 05:36:01', '2026-11-06 09:10:54'),
(28, 10, 'retail', 1, 75, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(29, 10, 'wholesale', 12, 60, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(30, 10, 'super_wholesale', 36, 48, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(31, 11, 'retail', 1, 90, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(32, 11, 'wholesale', 12, 75, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(33, 11, 'super_wholesale', 36, 60, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(34, 12, 'retail', 1, 110, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(35, 12, 'wholesale', 12, 95, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(36, 12, 'super_wholesale', 36, 80, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(37, 13, 'retail', 1, 140, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(38, 13, 'wholesale', 12, 120, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(39, 13, 'super_wholesale', 36, 100, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(40, 14, 'retail', 1, 160, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(41, 14, 'wholesale', 12, 140, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(42, 14, 'super_wholesale', 36, 120, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(43, 15, 'retail', 1, 180, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(44, 15, 'wholesale', 12, 155, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(45, 15, 'super_wholesale', 36, 135, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(46, 16, 'retail', 1, 195, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(47, 16, 'wholesale', 12, 170, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(48, 16, 'super_wholesale', 36, 150, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(49, 17, 'retail', 1, 50, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(50, 17, 'wholesale', 12, 40, '2026-11-06 05:36:02', '2026-11-06 09:10:54'),
(51, 17, 'super_wholesale', 36, 30, '2026-11-06 05:36:02', '2026-11-06 09:10:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `qris_transactions`
--

CREATE TABLE `qris_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(64) NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL,
  `total_amount` bigint(20) UNSIGNED NOT NULL,
  `amount_uniq` int(11) NOT NULL DEFAULT 0,
  `admin_fee` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `credit_amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `fee_by` varchar(20) NOT NULL DEFAULT 'user',
  `qris_method` varchar(50) DEFAULT NULL,
  `qr_url` text DEFAULT NULL,
  `qris_image` longtext DEFAULT NULL,
  `payment_url` text DEFAULT NULL,
  `status` enum('pending','success','expired','failed') NOT NULL DEFAULT 'pending',
  `is_test` tinyint(1) NOT NULL DEFAULT 0,
  `paid_at` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` bigint(20) UNSIGNED NOT NULL,
  `discount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `total` bigint(20) UNSIGNED NOT NULL,
  `paid_amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `change_amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `payment_method` enum('cash','qris','transfer') NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) DEFAULT NULL,
  `status` enum('completed','cancelled','returned') NOT NULL DEFAULT 'completed',
  `note` text DEFAULT NULL,
  `sold_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(150) NOT NULL COMMENT 'Snapshot of product name',
  `unit_name` varchar(50) NOT NULL COMMENT 'Snapshot of unit name',
  `unit_price` bigint(20) UNSIGNED NOT NULL COMMENT 'Snapshot of applied unit price',
  `quantity` int(11) NOT NULL,
  `item_discount` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Discount per item',
  `subtotal` bigint(20) UNSIGNED NOT NULL,
  `unit_cost` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Snapshot of HPP at sale time',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  `opening_cash` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `expected_cash` bigint(20) UNSIGNED DEFAULT NULL,
  `counted_cash` bigint(20) UNSIGNED DEFAULT NULL,
  `difference` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'HPP / Unit cost in Rupiah',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) NOT NULL COMMENT 'Detail or reason',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `movement_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `bank_account` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`, `address`, `bank_account`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Konveksi Berkah Busana Muslim', '081311223344 (Bp. Hendra)', 'Pusat Grosir Tanah Abang Blok A Lt. 2, Jakarta', 'BCA 8830123456 a.n Berkah Busana', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(2, 'Grosir Perlengkapan Haji Barokah', '081255667788 (Hj. Farida)', 'Kawasan Ampel, Jl. Sasak No. 12 Surabaya', 'Mandiri 142001928374 a.n Farida Barokah', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL),
(3, 'CV Pabrik Seragam Nusantara', '082199887766 (Bp. Agus)', 'Kawasan Industri Tekstil Cimahi, Bandung', 'BSI 7109283746 a.n Seragam Nusantara', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `units`
--

INSERT INTO `units` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Pcs', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(2, 'Lusin', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(3, 'Kodi', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(4, 'Meter', '2026-11-06 05:36:01', '2026-11-06 05:36:01'),
(5, 'Stel', '2026-11-06 05:36:01', '2026-11-06 05:36:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT 2 COMMENT '1: Admin, 2: Kasir',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `level`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Irfan Arif', 'admin', '$2y$12$L8WexczmSb17NtFiAUSbsOmLFeV8jOj/grCTyy/Ou/wIzG6FmGTeK', 1, 1, NULL, '2026-11-06 05:36:00', '2026-11-12 06:10:01', NULL),
(2, 'Desi Asthasari', 'kasir1', '$2y$12$E5CYxfFds/RTk9NEcSXyDOnXjv9qkYuMi0xmj2z0vt1sZq1img3iC', 2, 1, 'LBeq6nVrvzA6AvemPPyYp7tpCoydL8z5OFjKnwRt74fXbKxBZGhwIn9O8qaj', '2026-11-06 05:36:01', '2026-11-06 05:36:01', NULL);

--
-- Indexes for dumped tables
--

ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_subject_type_index` (`subject_type`),
  ADD KEY `audit_logs_subject_id_index` (`subject_id`),
  ADD KEY `audit_logs_created_at_index` (`created_at`);

ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_phone_index` (`phone`);

ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_unit_id_foreign` (`unit_id`),
  ADD KEY `products_name_index` (`name`),
  ADD KEY `products_stock_index` (`stock`);

ALTER TABLE `product_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_prices_product_id_tier_unique` (`product_id`,`tier`);

ALTER TABLE `qris_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qris_transactions_transaction_id_unique` (`transaction_id`),
  ADD KEY `qris_transactions_sale_id_foreign` (`sale_id`),
  ADD KEY `qris_transactions_created_by_foreign` (`created_by`),
  ADD KEY `qris_transactions_invoice_number_index` (`invoice_number`),
  ADD KEY `qris_transactions_status_index` (`status`);

ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoice_number_unique` (`invoice_number`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `sales_shift_id_foreign` (`shift_id`),
  ADD KEY `sales_status_index` (`status`),
  ADD KEY `sales_sold_at_index` (`sold_at`);

ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_user_id_foreign` (`user_id`),
  ADD KEY `shifts_opened_at_index` (`opened_at`),
  ADD KEY `shifts_closed_at_index` (`closed_at`);

ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`),
  ADD KEY `stock_movements_supplier_id_foreign` (`supplier_id`),
  ADD KEY `stock_movements_user_id_foreign` (`user_id`),
  ADD KEY `stock_movements_type_index` (`type`),
  ADD KEY `stock_movements_movement_date_index` (`movement_date`);

ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_name_unique` (`name`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `product_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

ALTER TABLE `qris_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

ALTER TABLE `product_prices`
  ADD CONSTRAINT `product_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `qris_transactions`
  ADD CONSTRAINT `qris_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `qris_transactions_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL;

ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

