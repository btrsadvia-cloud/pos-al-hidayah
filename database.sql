-- ==============================================================
-- DATABASE POINT OF SALE (POS) - TOKO AL-HIDAYAH
-- Disusun berdasarkan Skripsi Dimas Pandu Prayogo (UM Metro, 2024)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `pos_toko_alhidayah` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pos_toko_alhidayah`;

-- --------------------------------------------------------
-- 1. Tabel User (Admin & Kasir)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(40) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `address` VARCHAR(100) DEFAULT NULL,
  `level` INT(1) NOT NULL COMMENT '1: Admin/Pemilik, 2: Kasir',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data User (Password default: 'password123' atau sha1/bcrypt)
INSERT INTO `user` (`user_id`, `username`, `password`, `name`, `address`, `level`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Irfan Arif (Owner)', 'Jl. Diponegoro Metro', 1),
(2, 'kasir', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Desi Asthasari', 'Kota Metro', 2);

-- --------------------------------------------------------
-- 2. Tabel Kustomer / Pelanggan
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kustomer`;
CREATE TABLE `kustomer` (
  `kustomer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `gender` ENUM('L','P') DEFAULT 'L',
  `phone` VARCHAR(15) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kustomer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kustomer` (`kustomer_id`, `name`, `gender`, `phone`, `address`) VALUES
(1, 'Umum / Non-Member', 'L', '-', 'Metro'),
(2, 'Ibu Hj. Aminah', 'P', '081234567890', 'Metro Pusat'),
(3, 'Bpk. Ahmad Fauzi', 'L', '082198765432', 'Bandar Lampung');

-- --------------------------------------------------------
-- 3. Tabel Supplier (Fitur Rekomendasi Bab V)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
  `supplier_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`supplier_id`, `name`, `phone`, `address`, `description`) VALUES
(1, 'Konveksi Berkah Busana', '081311223344', 'Tanah Abang, Jakarta', 'Supplier busana muslim & mukena'),
(2, 'Grosir Perlengkapan Haji Barokah', '081255667788', 'Surabaya', 'Supplier sajadah, ihram, & tas haji'),
(3, 'Pabrik Seragam Nusantara', '082199887766', 'Bandung', 'Supplier seragam SD, SMP, SMA, & Pramuka');

-- --------------------------------------------------------
-- 4. Tabel Kategori Produk (`p_kategori`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `p_kategori`;
CREATE TABLE `p_kategori` (
  `kategori_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `p_kategori` (`kategori_id`, `name`) VALUES
(1, 'Baju Muslimah & Gamis'),
(2, 'Baju Seragam Sekolah'),
(3, 'Pakaian Dinas / Kantor'),
(4, 'Perlengkapan Haji & Umroh'),
(5, 'Peralatan Sholat');

-- --------------------------------------------------------
-- 5. Tabel Unit / Satuan (`p_unit`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `p_unit`;
CREATE TABLE `p_unit` (
  `unit_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `p_unit` (`unit_id`, `name`) VALUES
(1, 'Pcs'),
(2, 'Stel'),
(3, 'Lusin'),
(4, 'Kodi'),
(5, 'Meter');

-- --------------------------------------------------------
-- 6. Tabel Master Produk / Barang (`p_item`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `p_item`;
CREATE TABLE `p_item` (
  `item_id` INT(11) NOT NULL AUTO_INCREMENT,
  `barcode` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `kategori_id` INT(11) NOT NULL,
  `unit_id` INT(11) NOT NULL,
  `harga` INT(11) NOT NULL,
  `harga_grosir` INT(11) DEFAULT NULL COMMENT 'Harga untuk pembelian grosir',
  `stock` INT(10) NOT NULL DEFAULT 0,
  `image` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`),
  KEY `fk_item_kategori` (`kategori_id`),
  KEY `fk_item_unit` (`unit_id`),
  CONSTRAINT `fk_item_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `p_kategori` (`kategori_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_item_unit` FOREIGN KEY (`unit_id`) REFERENCES `p_unit` (`unit_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `p_item` (`item_id`, `barcode`, `name`, `kategori_id`, `unit_id`, `harga`, `harga_grosir`, `stock`, `image`) VALUES
(1, 'BRG001', 'Gamis Muslimah Syari Dewasa', 1, 1, 150000, 135000, 25, NULL),
(2, 'BRG002', 'Baju Koko Pria Lengan Panjang', 1, 1, 85000, 75000, 40, NULL),
(3, 'BRG003', 'Seragam SD Putih Merah Lengkap', 2, 2, 95000, 85000, 50, NULL),
(4, 'BRG004', 'Kain Ihram Pria Dewasa Katun', 4, 1, 130000, 115000, 20, NULL),
(5, 'BRG005', 'Mukena Dewasa Bordir Sutra', 5, 1, 175000, 160000, 15, NULL),
(6, 'BRG006', 'Sajadah Turki Motif Kubah', 5, 1, 45000, 38000, 60, NULL);

-- --------------------------------------------------------
-- 7. Tabel Stok Masuk & Keluar (`t_stock`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `t_stock`;
CREATE TABLE `t_stock` (
  `stock_id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `type` ENUM('in','out') NOT NULL COMMENT 'in: barang masuk, out: barang keluar/rusak/retur',
  `detail` VARCHAR(200) NOT NULL,
  `supplier_id` INT(11) DEFAULT NULL,
  `qty` INT(10) NOT NULL,
  `date` DATE NOT NULL,
  `user_id` INT(11) NOT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`),
  KEY `fk_stock_item` (`item_id`),
  KEY `fk_stock_user` (`user_id`),
  KEY `fk_stock_supplier` (`supplier_id`),
  CONSTRAINT `fk_stock_item` FOREIGN KEY (`item_id`) REFERENCES `p_item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Tabel Keranjang Belanja Sementara (`t_cart`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `t_cart`;
CREATE TABLE `t_cart` (
  `cart_id` INT(11) NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `harga` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  `diskon_item` INT(11) NOT NULL DEFAULT 0,
  `total` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `fk_cart_item` (`item_id`),
  KEY `fk_cart_user` (`user_id`),
  CONSTRAINT `fk_cart_item` FOREIGN KEY (`item_id`) REFERENCES `p_item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 9. Tabel Penjualan / Invoice (`t_sale`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `t_sale`;
CREATE TABLE `t_sale` (
  `sale_id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice` VARCHAR(50) NOT NULL UNIQUE,
  `kustomer_id` INT(11) DEFAULT NULL,
  `total_harga` INT(11) NOT NULL,
  `diskon` INT(11) NOT NULL DEFAULT 0,
  `final_harga` INT(11) NOT NULL,
  `cash` INT(11) NOT NULL,
  `uang_kembalian` INT(11) NOT NULL,
  `metode_pembayaran` ENUM('tunai','qris','transfer') DEFAULT 'tunai',
  `note` TEXT DEFAULT NULL,
  `date` DATE NOT NULL,
  `user_id` INT(11) NOT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sale_id`),
  KEY `fk_sale_kustomer` (`kustomer_id`),
  KEY `fk_sale_user` (`user_id`),
  CONSTRAINT `fk_sale_kustomer` FOREIGN KEY (`kustomer_id`) REFERENCES `kustomer` (`kustomer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 10. Tabel Detail Item Penjualan (`t_sale_detail`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `t_sale_detail`;
CREATE TABLE `t_sale_detail` (
  `sale_detail_id` INT(11) NOT NULL AUTO_INCREMENT,
  `sale_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL,
  `harga` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  `diskon_item` INT(11) NOT NULL DEFAULT 0,
  `total` INT(11) NOT NULL,
  PRIMARY KEY (`sale_detail_id`),
  KEY `fk_detail_sale` (`sale_id`),
  KEY `fk_detail_item` (`item_id`),
  CONSTRAINT `fk_detail_sale` FOREIGN KEY (`sale_id`) REFERENCES `t_sale` (`sale_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_item` FOREIGN KEY (`item_id`) REFERENCES `p_item` (`item_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Selesai

