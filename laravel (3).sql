-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Des 2025 pada 09.45
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_packages`
--

CREATE TABLE `custom_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_paket` varchar(255) NOT NULL,
  `harga_total` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `custom_packages`
--

INSERT INTO `custom_packages` (`id`, `nama_paket`, `harga_total`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
(8, 'PAKET 1', 12000.00, 'paket 1', 1, '2025-12-12 04:18:15', '2025-12-12 04:18:15'),
(9, 'PAKET 2', 14000.00, 'paket 2', 1, '2025-12-12 04:19:02', '2025-12-12 04:19:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_package_fnb`
--

CREATE TABLE `custom_package_fnb` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `custom_package_id` bigint(20) UNSIGNED NOT NULL,
  `fnb_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `custom_package_fnb`
--

INSERT INTO `custom_package_fnb` (`id`, `custom_package_id`, `fnb_id`, `quantity`, `created_at`, `updated_at`) VALUES
(11, 8, 7, 1, '2025-12-12 04:18:15', '2025-12-12 04:18:15'),
(12, 8, 9, 1, '2025-12-12 04:18:15', '2025-12-12 04:18:15'),
(13, 9, 9, 1, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(14, 9, 7, 1, '2025-12-12 04:19:02', '2025-12-12 04:19:02'),
(15, 9, 10, 1, '2025-12-12 04:19:02', '2025-12-12 04:19:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_package_playstation`
--

CREATE TABLE `custom_package_playstation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `custom_package_id` bigint(20) UNSIGNED NOT NULL,
  `playstation_id` bigint(20) UNSIGNED NOT NULL,
  `lama_main` int(11) NOT NULL COMMENT 'Duration in minutes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `custom_package_playstation`
--

INSERT INTO `custom_package_playstation` (`id`, `custom_package_id`, `playstation_id`, `lama_main`, `created_at`, `updated_at`) VALUES
(5, 8, 1, 60, '2025-12-12 04:18:15', '2025-12-12 04:18:15'),
(6, 9, 2, 120, '2025-12-12 04:19:02', '2025-12-12 04:19:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `devices`
--

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `playstation_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `devices`
--

INSERT INTO `devices` (`id`, `nama`, `playstation_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TV1', 1, 'Digunakan', '2025-11-30 04:53:23', '2025-12-12 14:00:09'),
(8, 'TV2', 1, 'Digunakan', '2025-11-30 04:54:58', '2025-12-12 14:13:10'),
(9, 'TV3', 1, 'Tersedia', '2025-11-30 04:55:12', '2025-12-12 13:13:03'),
(12, 'TV4', 1, 'Tersedia', '2025-12-02 15:10:42', '2025-12-12 09:55:46'),
(13, 'TV5', 1, 'Tersedia', '2025-12-06 06:43:39', '2025-12-12 12:59:17'),
(14, 'TV6', 1, 'Digunakan', '2025-12-06 12:38:22', '2025-12-12 14:26:29'),
(15, 'TV7', 1, 'Tersedia', '2025-12-06 12:38:28', '2025-12-11 15:30:18'),
(16, 'TV8', 1, 'Tersedia', '2025-12-06 12:38:36', '2025-12-12 03:55:05'),
(17, 'TV9', 1, 'Digunakan', '2025-12-06 12:39:05', '2025-12-12 14:39:08'),
(18, 'TV10', 2, 'Digunakan', '2025-12-06 12:40:30', '2025-12-12 13:50:09'),
(19, 'TV11', 2, 'Digunakan', '2025-12-06 12:40:41', '2025-12-12 14:42:17'),
(20, 'TV12', 2, 'Digunakan', '2025-12-06 12:40:51', '2025-12-12 14:44:20'),
(21, 'TV12', 2, 'Tersedia', '2025-12-06 12:41:21', '2025-12-11 07:47:37'),
(22, 'TV13', 2, 'Digunakan', '2025-12-06 12:41:33', '2025-12-12 14:00:09'),
(23, 'TV14', 2, 'Tersedia', '2025-12-06 12:41:47', '2025-12-12 15:04:00'),
(24, 'TV15', 1, 'Tersedia', '2025-12-06 12:42:09', '2025-12-11 07:47:37'),
(25, 'TV16', 1, 'Tersedia', '2025-12-06 12:42:17', '2025-12-11 15:22:00'),
(26, 'TV17', 1, 'Tersedia', '2025-12-06 12:42:31', '2025-12-10 15:19:58'),
(27, 'TV18', 1, 'Tersedia', '2025-12-06 12:42:42', '2025-12-10 14:57:51'),
(28, 'MEJA1', 3, 'Digunakan', '2025-12-06 12:44:54', '2025-12-12 12:58:15'),
(29, 'MEJA2', 3, 'Tersedia', '2025-12-06 12:45:04', '2025-12-11 07:47:37'),
(30, 'MEJA3', 3, 'Tersedia', '2025-12-06 12:45:14', '2025-12-10 14:55:16'),
(31, 'MEJA1', 3, 'Tersedia', '2025-12-06 12:45:26', '2025-12-10 14:57:07'),
(32, 'MEJA2', 4, 'Digunakan', '2025-12-06 12:45:38', '2025-12-12 14:45:37'),
(33, 'MEJA3', 4, 'Digunakan', '2025-12-06 12:45:49', '2025-12-12 14:46:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `metode_pembayaran` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expenses`
--

INSERT INTO `expenses` (`id`, `expense_category_id`, `deskripsi`, `jumlah`, `tanggal`, `metode_pembayaran`, `catatan`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 1, 'Beli ES Batu', 10000.00, '2025-12-07', 'Tunai', 'membali es batu', 1, '2025-12-07 13:41:52', '2025-12-07 13:41:52'),
(3, 2, 'untuk menggaji karyawan', 2000000.00, '2025-12-08', 'Tunai', NULL, 1, '2025-12-08 04:10:55', '2025-12-08 04:10:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Beli Bahan', 'untuk membeli bahan makanan', '2025-12-07 13:40:46', '2025-12-07 13:40:46'),
(2, 'Gaji Karyawan', 'untuk mengggaji karyawan', '2025-12-08 04:10:23', '2025-12-08 04:10:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fnbs`
--

CREATE TABLE `fnbs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga_beli` int(11) DEFAULT NULL,
  `harga_jual` int(11) NOT NULL,
  `price_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `fnbs`
--

INSERT INTO `fnbs` (`id`, `nama`, `harga_beli`, `harga_jual`, `price_group_id`, `stok`, `deskripsi`, `created_at`, `updated_at`) VALUES
(6, 'mie Rebus', 0, 10000, 2, 0, 'mie rebus mantul', '2025-12-07 09:16:55', '2025-12-08 15:52:44'),
(7, 'Nasi Goreng Spesial', NULL, 10000, 2, -1, 'nasgor + telur + ayam', '2025-12-07 09:17:45', '2025-12-12 09:01:22'),
(9, 'Es Teh', NULL, 5000, 3, -1, 'Es teh Segar', '2025-12-07 09:50:40', '2025-12-07 09:50:40'),
(10, 'Taro', NULL, 1000, 1, 12, 'snack taro', '2025-12-07 09:51:00', '2025-12-12 14:42:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_kelamin` varchar(255) NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_02_21_025346_create_members_table', 1),
(6, '2023_02_21_030516_create_playstations_table', 1),
(7, '2023_02_21_030612_create_transactions_table', 1),
(8, '2023_02_25_053515_create_devices_table', 1),
(9, '2024_10_01_000000_add_fnb_total_to_transactions_table', 1),
(10, '2025_09_26_130345_add_harga_to_playstations_table', 1),
(11, '2025_09_26_130946_drop_stok_from_playstations_table', 1),
(12, '2025_09_27_114739_create_fnbs_table', 1),
(13, '2025_09_27_114806_create_stock_mutations_table', 1),
(14, '2025_09_27_114828_create_transaction_fnbs_table', 1),
(15, '2025_09_27_140000_modify_id_transaksi_in_transactions_table', 1),
(16, '2025_09_30_205303_modify_fnbs_table_remove_required_fields', 1),
(17, '2025_09_30_220210_add_tipe_transaksi_to_transactions_table', 1),
(18, '2025_09_30_223702_modify_jam_main_nullable_in_transactions_table', 1),
(19, '2025_10_01_091438_rename_id_to_id_transaksi_in_transactions_table', 1),
(20, '2025_10_03_114248_add_payment_status_to_transactions_table', 1),
(21, '2025_11_28_210057_add_harga_beli_to_transaction_fnbs_table', 1),
(22, '2025_11_30_113711_fix_transaction_fnb_foreign_key', 1),
(23, '2025_12_07_160523_create_price_groups_table', 2),
(24, '2025_12_07_160715_add_price_group_id_to_fnbs_table', 2),
(25, '2025_12_07_161545_make_harga_beli_nullable_in_fnbs_table', 3),
(26, '2025_12_07_172224_add_payment_method_to_transactions_table', 4),
(27, '2025_12_07_172804_add_no_telepon_to_transactions_table', 5),
(28, '2025_12_07_174820_create_expenses_table', 6),
(29, '2025_12_07_194411_create_expense_categories_table', 7),
(30, '2025_12_07_194647_add_expense_category_id_to_expenses_table', 7),
(31, '2025_12_08_205036_create_custom_packages_table', 8),
(32, '2025_12_08_205127_create_custom_package_device_table', 8),
(33, '2025_12_08_205131_create_custom_package_fnb_table', 8),
(34, '2025_12_08_210811_add_custom_package_id_to_transactions_table', 9),
(35, '2025_12_08_211555_update_tipe_transaksi_enum_in_transactions_table', 10),
(36, '2025_12_08_212315_make_device_id_nullable_in_transactions_table', 11),
(38, '2025_12_09_214648_fix_tipe_transaksi_enum_values', 12),
(39, '2025_12_09_220000_fix_custom_package_enum', 13),
(40, '2025_12_09_220100_change_custom_package_device_to_playstation', 14),
(41, '2025_12_10_193018_add_diskon_to_transactions_table', 15),
(42, '2025_12_10_210503_add_lost_time_start_to_transactions_table', 16),
(43, '2025_12_11_000000_update_users_table_for_roles', 17);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `playstations`
--

CREATE TABLE `playstations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `playstations`
--

INSERT INTO `playstations` (`id`, `nama`, `image`, `created_at`, `updated_at`, `harga`) VALUES
(1, 'PS3', 'post-images/Bg3bUvMJQiM90L7yPhczj360623uvHMPkuEPnJsx.jpg', '2025-11-30 04:51:31', '2025-12-12 08:47:36', '7000'),
(2, 'PS4', 'post-images/04qoSuVv37ZrYlvdQeEkZ8duXRPD466y5rcLWJrG.png', '2025-11-30 04:52:06', '2025-12-12 08:47:55', '10000'),
(3, 'BILLIARD pagi', 'post-images/J7U20AO9UHmMaziSfo3Z8Gusf59MJJsmrvfwvuIU.png', '2025-11-30 04:52:47', '2025-12-12 08:51:10', '25000'),
(4, 'BILLIARD sore', 'post-images/IuDBsHXv28etFLtHjJbk5vXGuTEUMKAUN8BQTVaQ.png', '2025-12-06 12:44:35', '2025-12-12 08:51:21', '30000');

-- --------------------------------------------------------

--
-- Struktur dari tabel `price_groups`
--

CREATE TABLE `price_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `price_groups`
--

INSERT INTO `price_groups` (`id`, `nama`, `harga`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'seribuan', 1000, 'chiki-chikian', '2025-12-07 09:13:34', '2025-12-07 09:13:34'),
(2, 'makanan berat', 10000, 'untuk makanan berat', '2025-12-07 09:14:13', '2025-12-07 09:14:13'),
(3, 'Minuman', 5000, 'khusus untuk minuman', '2025-12-07 09:23:19', '2025-12-07 09:23:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_mutations`
--

CREATE TABLE `stock_mutations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fnb_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out') NOT NULL,
  `qty` int(11) NOT NULL,
  `date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_mutations`
--

INSERT INTO `stock_mutations` (`id`, `fnb_id`, `type`, `qty`, `date`, `note`, `created_at`, `updated_at`) VALUES
(39, 9, 'out', 1, '2025-12-07', 'Penjualan transaksi #41', '2025-12-07 09:51:46', '2025-12-07 09:51:46'),
(40, 6, 'out', 1, '2025-12-07', 'Penjualan transaksi #42', '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(41, 9, 'out', 1000, '2025-12-07', 'Penjualan transaksi #42', '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(42, 6, 'out', 1, '2025-12-07', 'Penjualan transaksi #42', '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(43, 6, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #47', '2025-12-07 14:12:13', '2025-12-07 14:12:13'),
(44, 9, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #47', '2025-12-07 14:12:13', '2025-12-07 14:12:13'),
(45, 7, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #45', '2025-12-07 14:12:45', '2025-12-07 14:12:45'),
(46, 10, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #47', '2025-12-07 14:13:19', '2025-12-07 14:13:19'),
(47, 6, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #45', '2025-12-07 14:16:23', '2025-12-07 14:16:23'),
(48, 10, 'out', 1, '2025-12-07', 'Penjualan - Transaksi #47', '2025-12-07 14:16:43', '2025-12-07 14:16:43'),
(49, 6, 'out', 1, '2025-12-08', 'Penjualan - Transaksi #76', '2025-12-08 04:20:37', '2025-12-08 04:20:37'),
(50, 7, 'out', 1, '2025-12-08', 'Penjualan - Transaksi #76', '2025-12-08 04:20:37', '2025-12-08 04:20:37'),
(51, 7, 'out', 1, '2025-12-08', 'Penjualan paket kustom #86', '2025-12-08 14:25:17', '2025-12-08 14:25:17'),
(52, 7, 'out', 1, '2025-12-08', 'Penjualan paket kustom #87', '2025-12-08 14:30:06', '2025-12-08 14:30:06'),
(53, 7, 'out', 1, '2025-12-08', 'Penjualan paket kustom #88', '2025-12-08 14:48:14', '2025-12-08 14:48:14'),
(54, 6, 'out', 95, '2025-12-08', 'Penjualan - Transaksi #90', '2025-12-08 15:52:44', '2025-12-08 15:52:44'),
(55, 7, 'out', 1, '2025-12-09', 'Penjualan - Transaksi #98', '2025-12-09 14:49:44', '2025-12-09 14:49:44'),
(56, 9, 'out', 1, '2025-12-09', 'Penjualan transaksi custom package #104', '2025-12-09 15:10:52', '2025-12-09 15:10:52'),
(57, 7, 'out', 1, '2025-12-09', 'Penjualan transaksi custom package #105', '2025-12-09 15:13:31', '2025-12-09 15:13:31'),
(58, 9, 'out', 1, '2025-12-10', 'Penjualan - Transaksi #125', '2025-12-10 06:34:24', '2025-12-10 06:34:24'),
(59, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #132', '2025-12-10 07:19:45', '2025-12-10 07:19:45'),
(60, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #133', '2025-12-10 07:24:13', '2025-12-10 07:24:13'),
(61, 7, 'out', 1, '2025-12-10', 'Penjualan - Transaksi #139', '2025-12-10 07:30:04', '2025-12-10 07:30:04'),
(62, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #142', '2025-12-10 07:31:52', '2025-12-10 07:31:52'),
(63, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #144', '2025-12-10 07:43:19', '2025-12-10 07:43:19'),
(64, 9, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #146', '2025-12-10 07:47:49', '2025-12-10 07:47:49'),
(65, 9, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #147', '2025-12-10 08:00:27', '2025-12-10 08:00:27'),
(66, 9, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #148', '2025-12-10 08:04:23', '2025-12-10 08:04:23'),
(67, 9, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #149', '2025-12-10 12:17:21', '2025-12-10 12:17:21'),
(68, 9, 'out', 1, '2025-12-10', 'Penjualan - Transaksi #150', '2025-12-10 12:18:28', '2025-12-10 12:18:28'),
(69, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #153', '2025-12-10 12:32:59', '2025-12-10 12:32:59'),
(70, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #154', '2025-12-10 12:33:34', '2025-12-10 12:33:34'),
(71, 7, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #156', '2025-12-10 12:46:35', '2025-12-10 12:46:35'),
(72, 9, 'out', 1, '2025-12-10', 'Penjualan transaksi custom package #179', '2025-12-10 16:02:14', '2025-12-10 16:02:14'),
(73, 9, 'out', 1, '2025-12-11', 'Penjualan transaksi custom package #184', '2025-12-11 08:48:04', '2025-12-11 08:48:04'),
(74, 7, 'out', 1, '2025-12-11', 'Penjualan transaksi custom package #185', '2025-12-11 08:53:35', '2025-12-11 08:53:35'),
(75, 7, 'out', 1, '2025-12-11', 'Penjualan transaksi #186', '2025-12-11 12:20:17', '2025-12-11 12:20:17'),
(76, 9, 'out', 1, '2025-12-11', 'Penjualan transaksi #186', '2025-12-11 12:20:17', '2025-12-11 12:20:17'),
(77, 9, 'out', 1, '2025-12-11', 'Penjualan transaksi custom package #188', '2025-12-11 14:22:54', '2025-12-11 14:22:54'),
(78, 9, 'out', 1, '2025-12-11', 'Penjualan transaksi custom package #192', '2025-12-11 15:16:27', '2025-12-11 15:16:27'),
(79, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi #196', '2025-12-12 03:56:48', '2025-12-12 03:56:48'),
(80, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #197', '2025-12-12 04:03:56', '2025-12-12 04:03:56'),
(81, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #198', '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(82, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #198', '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(83, 10, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #198', '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(84, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #199', '2025-12-12 04:19:55', '2025-12-12 04:19:55'),
(85, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #199', '2025-12-12 04:19:55', '2025-12-12 04:19:55'),
(86, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #200', '2025-12-12 04:20:13', '2025-12-12 04:20:13'),
(87, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #200', '2025-12-12 04:20:13', '2025-12-12 04:20:13'),
(88, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #205', '2025-12-12 09:20:09', '2025-12-12 09:20:09'),
(89, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #205', '2025-12-12 09:20:09', '2025-12-12 09:20:09'),
(90, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #204', '2025-12-12 09:20:35', '2025-12-12 09:20:35'),
(91, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #203', '2025-12-12 09:20:50', '2025-12-12 09:20:50'),
(92, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #206', '2025-12-12 12:46:49', '2025-12-12 12:46:49'),
(93, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #207', '2025-12-12 12:47:58', '2025-12-12 12:47:58'),
(94, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #208', '2025-12-12 12:55:59', '2025-12-12 12:55:59'),
(95, 9, 'out', 2, '2025-12-12', 'Penjualan - Transaksi #204', '2025-12-12 13:00:09', '2025-12-12 13:00:09'),
(96, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:19:48', '2025-12-12 13:19:48'),
(97, 7, 'in', 1, '2025-12-12', 'Hapus item dari Transaksi #201', '2025-12-12 13:30:46', '2025-12-12 13:30:46'),
(98, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:32:37', '2025-12-12 13:32:37'),
(99, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:33:50', '2025-12-12 13:33:50'),
(100, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:34:02', '2025-12-12 13:34:02'),
(101, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:34:17', '2025-12-12 13:34:17'),
(102, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:34:51', '2025-12-12 13:34:51'),
(103, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:36:33', '2025-12-12 13:36:33'),
(104, 10, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:36:48', '2025-12-12 13:36:48'),
(105, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:37:39', '2025-12-12 13:37:39'),
(106, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #201', '2025-12-12 13:38:39', '2025-12-12 13:38:39'),
(107, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #196', '2025-12-12 13:52:08', '2025-12-12 13:52:08'),
(108, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #209', '2025-12-12 13:58:19', '2025-12-12 13:58:19'),
(109, 10, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #207', '2025-12-12 13:59:37', '2025-12-12 13:59:37'),
(110, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #210', '2025-12-12 14:00:53', '2025-12-12 14:00:53'),
(111, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #210', '2025-12-12 14:00:53', '2025-12-12 14:00:53'),
(112, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #211', '2025-12-12 14:13:25', '2025-12-12 14:13:25'),
(113, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #211', '2025-12-12 14:13:25', '2025-12-12 14:13:25'),
(114, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #212', '2025-12-12 14:20:27', '2025-12-12 14:20:27'),
(115, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #212', '2025-12-12 14:20:27', '2025-12-12 14:20:27'),
(116, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #212', '2025-12-12 14:25:46', '2025-12-12 14:25:46'),
(117, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #211', '2025-12-12 14:28:31', '2025-12-12 14:28:31'),
(118, 10, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #211', '2025-12-12 14:32:03', '2025-12-12 14:32:03'),
(119, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #213', '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(120, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #213', '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(121, 10, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #213', '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(122, 9, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #214', '2025-12-12 14:42:29', '2025-12-12 14:42:29'),
(123, 7, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #214', '2025-12-12 14:42:29', '2025-12-12 14:42:29'),
(124, 10, 'out', 1, '2025-12-12', 'Penjualan transaksi custom package #214', '2025-12-12 14:42:29', '2025-12-12 14:42:29'),
(125, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #214', '2025-12-12 14:42:45', '2025-12-12 14:42:45'),
(126, 7, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #215', '2025-12-12 14:45:11', '2025-12-12 14:45:11'),
(127, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #215', '2025-12-12 14:45:11', '2025-12-12 14:45:11'),
(128, 9, 'out', 1, '2025-12-12', 'Penjualan - Transaksi #217', '2025-12-12 14:58:06', '2025-12-12 14:58:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id_transaksi` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `harga` varchar(255) NOT NULL,
  `jam_main` varchar(255) DEFAULT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_Selesai` time DEFAULT NULL,
  `lost_time_start` timestamp NULL DEFAULT NULL,
  `total` varchar(255) NOT NULL,
  `fnb_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_transaksi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipe_transaksi` enum('prepaid','postpaid','custom_package') NOT NULL DEFAULT 'prepaid',
  `payment_status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id_transaksi`, `status`, `member_id`, `nama`, `no_telepon`, `device_id`, `custom_package_id`, `diskon`, `user_id`, `harga`, `jam_main`, `waktu_mulai`, `waktu_Selesai`, `lost_time_start`, `total`, `fnb_total`, `status_transaksi`, `created_at`, `updated_at`, `tipe_transaksi`, `payment_status`, `payment_method`) VALUES
(1, 'user', NULL, 'joko', NULL, 1, NULL, 0.00, 1, '5000', '1', '12:05:00', '13:05:00', NULL, '13000', 0.00, 'selesai', '2025-11-30 05:05:27', '2025-11-30 05:22:23', 'prepaid', 'paid', NULL),
(2, 'user', NULL, 'budi', NULL, 2, NULL, 0.00, 1, '5000', '1', '12:10:00', '13:10:00', NULL, '5000', 0.00, 'selesai', '2025-11-30 05:10:53', '2025-11-30 05:13:52', 'prepaid', 'paid', NULL),
(3, 'user', NULL, 'Sarah', NULL, 4, NULL, 0.00, 1, '7000', NULL, '12:24:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-11-30 05:24:15', '2025-11-30 05:24:15', 'postpaid', 'unpaid', NULL),
(4, 'user', NULL, 'Sarah', NULL, 4, NULL, 0.00, 1, '7000', '0 jam 46 menit', '12:29:00', '13:15:00', NULL, '42000', 0.00, 'selesai', '2025-11-30 05:29:35', '2025-11-30 07:28:17', 'postpaid', 'paid', NULL),
(5, 'user', NULL, 'asep', NULL, 3, NULL, 0.00, 1, '5000', '1', '12:30:00', '13:30:00', NULL, '5000', 0.00, 'selesai', '2025-11-30 05:30:31', '2025-11-30 06:12:40', 'prepaid', 'paid', NULL),
(6, 'user', NULL, 'rizal', NULL, 5, NULL, 0.00, 1, '7000', '1', '12:39:00', '13:39:00', NULL, '18000', 0.00, 'selesai', '2025-11-30 05:39:32', '2025-11-30 06:12:26', 'prepaid', 'paid', NULL),
(7, 'user', NULL, 'irwan', NULL, 6, NULL, 0.00, 1, '7000', '0 jam 19 menit', '12:55:00', '13:14:00', NULL, '27000', 0.00, 'selesai', '2025-11-30 05:55:20', '2025-11-30 06:14:25', 'postpaid', 'paid', NULL),
(8, 'user', NULL, 'setyo', NULL, 9, NULL, 0.00, 1, '10000', '0 jam 5 menit', '13:06:00', '13:11:00', NULL, '1000', 0.00, 'selesai', '2025-11-30 06:06:13', '2025-11-30 06:11:33', 'postpaid', 'paid', NULL),
(9, 'user', NULL, 'budi', NULL, 1, NULL, 0.00, 1, '5000', '1 jam 13 menit', '13:16:00', '14:29:00', NULL, '17000', 0.00, 'selesai', '2025-11-30 06:16:14', '2025-11-30 07:29:36', 'postpaid', 'paid', NULL),
(10, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '5000', '1', '14:30:00', '15:30:00', NULL, '23000', 0.00, 'selesai', '2025-11-30 07:30:44', '2025-11-30 07:43:35', 'prepaid', 'paid', NULL),
(11, 'user', NULL, 'kevin', NULL, 2, NULL, 0.00, 1, '5000', '0 jam 2 menit', '14:40:00', '14:42:00', NULL, '31000', 0.00, 'selesai', '2025-11-30 07:40:22', '2025-11-30 07:43:12', 'postpaid', 'paid', NULL),
(12, 'user', NULL, 'affan', NULL, 2, NULL, 0.00, 1, '5000', '1', '14:44:00', '15:44:00', NULL, '5000', 0.00, 'selesai', '2025-11-30 07:44:18', '2025-11-30 07:53:27', 'prepaid', 'paid', NULL),
(13, 'user', NULL, 'jordan', NULL, 3, NULL, 0.00, 1, '5000', '2', '14:45:00', '16:45:00', NULL, '36000', 0.00, 'selesai', '2025-11-30 07:45:40', '2025-11-30 07:45:49', 'prepaid', 'paid', NULL),
(14, 'user', NULL, 'leni', NULL, 9, NULL, 0.00, 1, '10000', '0 jam 5 menit', '14:46:00', '14:51:00', NULL, '27000', 0.00, 'selesai', '2025-11-30 07:46:35', '2025-11-30 07:52:05', 'postpaid', 'paid', NULL),
(15, 'user', NULL, 'anwar', NULL, 8, NULL, 0.00, 1, '10000', '1', '14:49:00', '15:49:00', NULL, '10000', 0.00, 'selesai', '2025-11-30 07:49:06', '2025-11-30 07:49:17', 'prepaid', 'paid', NULL),
(16, 'user', NULL, 'sukma', NULL, 7, NULL, 0.00, 1, '10000', '0 jam 3 menit', '14:49:00', '14:52:00', NULL, '1000', 0.00, 'selesai', '2025-11-30 07:49:43', '2025-11-30 07:52:51', 'postpaid', 'paid', NULL),
(17, 'user', NULL, 'tyo', NULL, 9, NULL, 0.00, 1, '10000', '0 jam 25 menit', '14:54:00', '15:19:00', NULL, '5000', 0.00, 'selesai', '2025-11-30 07:54:11', '2025-11-30 08:19:58', 'postpaid', 'paid', NULL),
(18, 'user', NULL, 'Budi', NULL, 1, NULL, 0.00, 1, '5000', '6 jam 58 menit', '13:55:00', '20:53:00', NULL, '35000', 0.00, 'selesai', '2025-12-01 06:55:06', '2025-12-01 13:53:21', 'postpaid', 'paid', NULL),
(19, 'user', NULL, 'Agus', NULL, 9, NULL, 0.00, 1, '10000', '1', '13:55:00', '14:55:00', NULL, '10000', 0.00, 'selesai', '2025-12-01 06:55:27', '2025-12-01 13:55:01', 'prepaid', 'paid', NULL),
(20, 'user', NULL, 'affan', NULL, 2, NULL, 0.00, 1, '5000', '1', '20:52:00', '21:52:00', NULL, '5000', 0.00, 'selesai', '2025-12-01 13:52:52', '2025-12-01 13:52:59', 'prepaid', 'paid', NULL),
(21, 'user', NULL, 'Budi', NULL, 1, NULL, 0.00, 1, '5000', '1', '22:11:00', '23:11:00', NULL, '13000', 0.00, 'sukses', '2025-12-02 15:11:56', '2025-12-02 15:11:56', 'prepaid', 'unpaid', NULL),
(22, 'user', NULL, 'Joko', NULL, 8, NULL, 0.00, 1, '10000', NULL, '22:12:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-02 15:12:22', '2025-12-02 15:12:22', 'postpaid', 'unpaid', NULL),
(23, 'user', NULL, 'Setyo', NULL, 1, NULL, 0.00, 1, '5000', NULL, '22:02:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-03 15:02:01', '2025-12-03 15:02:01', 'postpaid', 'unpaid', NULL),
(24, 'user', NULL, 'Anwar', NULL, 6, NULL, 0.00, 1, '7000', '1', '22:02:00', '23:02:00', NULL, '17000', 0.00, 'selesai', '2025-12-03 15:02:32', '2025-12-03 15:02:44', 'prepaid', 'paid', NULL),
(25, 'user', NULL, 'Agus', NULL, 3, NULL, 0.00, 1, '5000', '0 jam 1 menit', '22:03:00', '22:04:00', NULL, '9000', 0.00, 'selesai', '2025-12-03 15:03:46', '2025-12-03 15:04:43', 'postpaid', 'unpaid', NULL),
(26, 'user', NULL, 'budi', NULL, 4, NULL, 0.00, 1, '7000', '1', '22:04:00', '23:04:00', NULL, '15000', 0.00, 'selesai', '2025-12-03 15:04:16', '2025-12-03 15:04:27', 'prepaid', 'paid', NULL),
(27, 'user', NULL, 'Asep', NULL, 7, NULL, 0.00, 1, '10000', '0 jam 0 menit', '22:06:00', '22:06:00', NULL, '10000', 0.00, 'selesai', '2025-12-03 15:06:30', '2025-12-03 15:07:04', 'postpaid', 'paid', NULL),
(28, 'user', NULL, 'Budi', NULL, 10, NULL, 0.00, 1, '10000', '1', '22:07:00', '23:07:00', NULL, '10000', 0.00, 'selesai', '2025-12-03 15:07:25', '2025-12-03 15:07:32', 'prepaid', 'paid', NULL),
(29, 'user', NULL, 'affan', NULL, 2, NULL, 0.00, 1, '5000', '1', '12:33:00', '13:33:00', NULL, '15000', 0.00, 'sukses', '2025-12-06 05:33:05', '2025-12-06 05:33:05', 'prepaid', 'unpaid', NULL),
(30, 'user', NULL, 'affan', NULL, 6, NULL, 0.00, 1, '7000', '0 jam 1 menit', '12:33:00', '12:34:00', NULL, '17000', 0.00, 'selesai', '2025-12-06 05:33:37', '2025-12-06 05:39:23', 'postpaid', 'unpaid', NULL),
(31, 'user', NULL, 'affan', NULL, 3, NULL, 0.00, 1, '5000', '1', '13:02:00', '14:02:00', NULL, '13000', 0.00, 'sukses', '2025-12-06 06:02:19', '2025-12-06 06:02:19', 'prepaid', 'unpaid', NULL),
(32, 'user', NULL, 'affan', NULL, 6, NULL, 0.00, 1, '7000', '0 jam 1 menit', '13:02:00', '13:03:00', NULL, '37000', 0.00, 'selesai', '2025-12-06 06:02:36', '2025-12-06 12:13:56', 'postpaid', 'unpaid', NULL),
(33, 'user', NULL, 'affan', NULL, 6, NULL, 0.00, 1, '7000', '5 jam 57 menit', '13:07:00', '19:04:00', NULL, '42000', 0.00, 'selesai', '2025-12-06 06:07:20', '2025-12-06 12:04:52', 'postpaid', 'paid', NULL),
(34, 'user', NULL, 'affan', NULL, 4, NULL, 0.00, 1, '7000', '1', '19:05:00', '20:05:00', NULL, '17000', 0.00, 'selesai', '2025-12-06 12:05:40', '2025-12-06 12:05:48', 'prepaid', 'paid', NULL),
(35, 'user', NULL, 'affan', NULL, 5, NULL, 0.00, 1, '7000', '1', '19:20:00', '20:20:00', NULL, '7000', 0.00, 'sukses', '2025-12-06 12:20:56', '2025-12-06 12:20:56', 'prepaid', 'unpaid', NULL),
(36, 'user', NULL, 'khairil affan', NULL, 10, NULL, 0.00, 1, '10000', '0 jam 11 menit', '19:21:00', '19:32:00', NULL, '2000', 0.00, 'selesai', '2025-12-06 12:21:09', '2025-12-06 12:32:50', 'postpaid', 'unpaid', NULL),
(37, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '1', '19:46:00', '20:46:00', NULL, '7000', 0.00, 'sukses', '2025-12-06 12:46:49', '2025-12-06 12:46:49', 'prepaid', 'unpaid', NULL),
(38, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', NULL, '19:47:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-06 12:47:22', '2025-12-06 12:47:22', 'postpaid', 'unpaid', NULL),
(39, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 1, '10000', '0 jam 4 menit', '20:15:00', '20:19:00', NULL, '1000', 0.00, 'selesai', '2025-12-06 13:15:59', '2025-12-06 13:19:13', 'postpaid', 'unpaid', NULL),
(40, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '1', '20:16:00', '21:16:00', NULL, '12000', 0.00, 'sukses', '2025-12-06 13:16:36', '2025-12-06 13:16:36', 'prepaid', 'unpaid', NULL),
(41, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', '0 jam 26 menit', '16:51:00', '17:17:00', NULL, '9000', 0.00, 'selesai', '2025-12-07 09:51:46', '2025-12-07 10:17:08', 'postpaid', 'unpaid', NULL),
(42, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '7000', '1', '17:01:00', '18:01:00', NULL, '5027000', 0.00, 'selesai', '2025-12-07 10:01:09', '2025-12-07 10:25:16', 'prepaid', 'paid', 'e-wallet'),
(43, 'user', NULL, 'affan', NULL, 20, NULL, 0.00, 1, '10000', '1', '17:30:00', '18:30:00', NULL, '10000', 0.00, 'sukses', '2025-12-07 10:30:41', '2025-12-07 10:30:41', 'prepaid', 'unpaid', NULL),
(44, 'user', NULL, 'khairil affan', '079975577', 14, NULL, 0.00, 1, '7000', '3 jam 13 menit', '17:33:00', '20:46:00', NULL, '23000', 0.00, 'selesai', '2025-12-07 10:33:41', '2025-12-07 13:46:43', 'postpaid', 'paid', 'transfer_bank'),
(45, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '2', '20:58:00', '23:21:00', NULL, '14000', 0.00, 'selesai', '2025-12-07 13:58:25', '2025-12-07 14:48:17', 'prepaid', 'paid', 'e-wallet'),
(46, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '7000', '3', '20:58:00', '23:58:00', NULL, '21000', 0.00, 'selesai', '2025-12-07 13:58:50', '2025-12-07 14:07:43', 'prepaid', 'paid', 'tunai'),
(47, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 1, '10000', '2', '21:01:00', '23:32:00', NULL, '20000', 0.00, 'selesai', '2025-12-07 14:01:22', '2025-12-07 14:48:26', 'prepaid', 'paid', 'transfer_bank'),
(48, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '1', '21:33:00', '22:45:00', NULL, '7000', 0.00, 'selesai', '2025-12-07 14:33:02', '2025-12-07 14:48:36', 'prepaid', 'paid', 'transfer_bank'),
(49, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', '2', '21:58:00', '23:58:00', NULL, '14000', 0.00, 'berjalan', '2025-12-07 14:58:55', '2025-12-07 14:59:45', 'prepaid', 'unpaid', NULL),
(50, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 1, '10000', '10', '22:00:00', '08:00:00', NULL, '100000', 0.00, 'sukses', '2025-12-07 15:00:21', '2025-12-07 15:00:21', 'prepaid', 'unpaid', NULL),
(51, 'user', NULL, 'khairil affan', NULL, 16, NULL, 0.00, 1, '7000', '3', '22:05:00', '01:05:00', NULL, '21000', 0.00, 'sukses', '2025-12-07 15:05:05', '2025-12-07 15:05:05', 'prepaid', 'unpaid', NULL),
(52, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '2', '22:12:00', '00:12:00', NULL, '14000', 0.00, 'berjalan', '2025-12-07 15:12:23', '2025-12-07 15:13:15', 'prepaid', 'unpaid', NULL),
(53, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '2', '22:14:00', '00:14:00', NULL, '14000', 0.00, 'sukses', '2025-12-07 15:14:10', '2025-12-07 15:14:10', 'prepaid', 'unpaid', NULL),
(54, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '2', '22:19:00', '00:19:00', NULL, '14000', 0.00, 'sukses', '2025-12-07 15:19:44', '2025-12-07 15:19:44', 'prepaid', 'unpaid', NULL),
(55, 'user', NULL, 'affan', NULL, 27, NULL, 0.00, 1, '7000', '5', '22:30:00', '03:30:00', NULL, '35000', 0.00, 'sukses', '2025-12-07 15:30:01', '2025-12-07 15:30:01', 'prepaid', 'unpaid', NULL),
(56, 'user', NULL, '1', NULL, 14, NULL, 0.00, 1, '7000', NULL, '22:30:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-07 15:30:34', '2025-12-07 15:30:34', 'postpaid', 'unpaid', NULL),
(57, 'user', NULL, 'affan', NULL, 27, NULL, 0.00, 1, '7000', '1', '22:30:00', '23:30:00', NULL, '7000', 0.00, 'sukses', '2025-12-07 15:30:57', '2025-12-07 15:30:57', 'prepaid', 'unpaid', NULL),
(58, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '2', '22:31:00', '00:31:00', NULL, '14000', 0.00, 'sukses', '2025-12-07 15:31:21', '2025-12-07 15:31:21', 'prepaid', 'unpaid', NULL),
(59, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '1', '22:31:00', '23:31:00', NULL, '7000', 0.00, 'sukses', '2025-12-07 15:31:50', '2025-12-07 15:31:50', 'prepaid', 'unpaid', NULL),
(60, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '3', '22:35:00', '01:35:00', NULL, '21000', 0.00, 'sukses', '2025-12-07 15:35:55', '2025-12-07 15:35:55', 'prepaid', 'unpaid', NULL),
(61, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '2', '22:36:00', '00:36:00', NULL, '14000', 0.00, 'sukses', '2025-12-07 15:36:40', '2025-12-07 15:36:40', 'prepaid', 'unpaid', NULL),
(62, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '10000', '3', '22:46:00', '01:46:00', NULL, '30000', 0.00, 'sukses', '2025-12-07 15:46:40', '2025-12-07 15:46:40', 'prepaid', 'unpaid', NULL),
(63, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '1', '22:48:00', '23:48:00', NULL, '7000', 0.00, 'sukses', '2025-12-07 15:48:44', '2025-12-07 15:48:44', 'prepaid', 'unpaid', NULL),
(64, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '2', '22:49:00', '00:49:00', NULL, '14000', 0.00, 'sukses', '2025-12-07 15:49:02', '2025-12-07 15:49:02', 'prepaid', 'unpaid', NULL),
(65, 'user', NULL, 'affan', NULL, 21, NULL, 0.00, 1, '10000', '2', '22:56:00', '00:56:00', NULL, '20000', 0.00, 'selesai', '2025-12-07 15:56:02', '2025-12-07 15:58:13', 'prepaid', 'paid', 'transfer_bank'),
(66, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '6', '23:10:00', '05:10:00', NULL, '42000', 0.00, 'sukses', '2025-12-07 16:10:37', '2025-12-07 16:10:37', 'prepaid', 'unpaid', NULL),
(67, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '1', '11:06:00', '12:06:00', NULL, '7000', 0.00, 'sukses', '2025-12-08 04:06:15', '2025-12-08 04:06:15', 'prepaid', 'unpaid', NULL),
(68, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '2', '11:06:00', '13:06:00', NULL, '14000', 0.00, 'sukses', '2025-12-08 04:06:36', '2025-12-08 04:06:36', 'prepaid', 'unpaid', NULL),
(69, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', '3', '11:06:00', '14:06:00', NULL, '21000', 0.00, 'sukses', '2025-12-08 04:06:57', '2025-12-08 04:06:57', 'prepaid', 'unpaid', NULL),
(70, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '7000', '5', '11:07:00', '16:07:00', NULL, '35000', 0.00, 'sukses', '2025-12-08 04:07:28', '2025-12-08 04:07:28', 'prepaid', 'unpaid', NULL),
(71, 'user', NULL, 'affan', '0896309862787', 15, NULL, 0.00, 1, '7000', '10', '11:08:00', '21:08:00', NULL, '70000', 0.00, 'selesai', '2025-12-08 04:08:54', '2025-12-08 04:53:22', 'prepaid', 'paid', 'tunai'),
(72, 'user', NULL, 'affan', NULL, 24, NULL, 0.00, 1, '7000', '0 jam 48 menit', '11:09:00', '11:57:00', NULL, '6000', 0.00, 'selesai', '2025-12-08 04:09:17', '2025-12-08 04:57:40', 'postpaid', 'paid', 'transfer_bank'),
(73, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '24', '11:13:00', '11:13:00', NULL, '168000', 0.00, 'selesai', '2025-12-08 04:13:43', '2025-12-08 04:49:00', 'prepaid', 'paid', 'transfer_bank'),
(74, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '20', '11:14:00', '07:14:00', NULL, '140000', 0.00, 'selesai', '2025-12-08 04:14:16', '2025-12-08 04:48:41', 'prepaid', 'paid', 'e-wallet'),
(75, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '10', '11:14:00', '21:14:00', NULL, '70000', 0.00, 'selesai', '2025-12-08 04:14:39', '2025-12-08 04:34:11', 'prepaid', 'paid', 'tunai'),
(76, 'user', NULL, 'affan', NULL, 14, NULL, 0.00, 1, '7000', '12', '11:15:00', '23:15:00', NULL, '27000', 0.00, 'selesai', '2025-12-08 04:15:03', '2025-12-08 04:20:57', 'prepaid', 'paid', 'e-wallet'),
(77, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '5', '11:21:00', '16:21:00', NULL, '35000', 0.00, 'selesai', '2025-12-08 04:21:47', '2025-12-08 04:21:53', 'prepaid', 'paid', 'transfer_bank'),
(78, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 1, '10000', '8', '12:01:00', '20:01:00', NULL, '80000', 0.00, 'selesai', '2025-12-08 05:01:04', '2025-12-08 05:01:10', 'prepaid', 'paid', 'transfer_bank'),
(79, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '10', '19:15:00', '05:15:00', NULL, '70000', 0.00, 'sukses', '2025-12-08 12:15:53', '2025-12-08 12:15:53', 'prepaid', 'unpaid', NULL),
(80, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '9', '19:16:00', '04:16:00', NULL, '63000', 0.00, 'sukses', '2025-12-08 12:16:21', '2025-12-08 12:16:21', 'prepaid', 'unpaid', NULL),
(81, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '8', '19:16:00', '03:16:00', NULL, '56000', 0.00, 'sukses', '2025-12-08 12:16:50', '2025-12-08 12:16:50', 'prepaid', 'unpaid', NULL),
(82, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '5', '19:17:00', '00:17:00', NULL, '35000', 0.00, 'sukses', '2025-12-08 12:17:16', '2025-12-08 12:17:16', 'prepaid', 'unpaid', NULL),
(83, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '4', '19:20:00', '23:20:00', NULL, '28000', 0.00, 'sukses', '2025-12-08 12:20:57', '2025-12-08 12:20:57', 'prepaid', 'unpaid', NULL),
(84, 'user', NULL, 'affan', NULL, 27, NULL, 0.00, 1, '7000', '10', '19:32:00', '05:32:00', NULL, '70000', 0.00, 'sukses', '2025-12-08 12:32:36', '2025-12-08 12:32:36', 'prepaid', 'unpaid', NULL),
(85, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '10000', '5', '20:00:00', '01:00:00', NULL, '50000', 0.00, 'selesai', '2025-12-08 13:00:57', '2025-12-08 13:11:58', 'prepaid', 'paid', 'transfer_bank'),
(86, 'user', NULL, 'affan', NULL, NULL, NULL, 0.00, 1, '0', '2', '21:25:00', '23:25:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-08 14:25:17', '2025-12-08 14:25:17', 'prepaid', 'unpaid', NULL),
(87, 'user', NULL, 'affan', NULL, NULL, NULL, 0.00, 1, '0', '2', '21:30:00', '23:30:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-08 14:30:06', '2025-12-08 14:30:06', 'prepaid', 'unpaid', NULL),
(88, 'user', NULL, 'affan', NULL, NULL, NULL, 0.00, 1, '0', '2', '21:48:00', '23:48:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-08 14:48:14', '2025-12-08 14:48:14', 'prepaid', 'unpaid', NULL),
(89, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '2', '22:09:00', '00:09:00', NULL, '14000', 0.00, 'sukses', '2025-12-08 15:09:42', '2025-12-08 15:09:42', 'prepaid', 'unpaid', NULL),
(90, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', '12', '22:15:00', '10:15:00', NULL, '957000', 0.00, 'selesai', '2025-12-08 15:15:41', '2025-12-08 15:53:04', 'prepaid', 'paid', 'tunai'),
(91, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '7000', NULL, '22:40:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-08 15:40:56', '2025-12-08 15:40:56', 'postpaid', 'unpaid', NULL),
(92, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '10', '20:29:00', '06:29:00', NULL, '70000', 0.00, 'sukses', '2025-12-09 13:29:41', '2025-12-09 13:29:41', 'prepaid', 'unpaid', NULL),
(93, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', NULL, '21:18:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 14:18:44', '2025-12-09 14:18:44', 'postpaid', 'unpaid', NULL),
(94, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '7000', '2', '21:30:00', '23:30:00', NULL, '14000', 0.00, 'sukses', '2025-12-09 14:30:17', '2025-12-09 14:30:17', 'prepaid', 'unpaid', NULL),
(95, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', NULL, '21:30:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 14:30:27', '2025-12-09 14:30:27', 'postpaid', 'unpaid', NULL),
(96, 'user', NULL, 'affan', NULL, 14, NULL, 0.00, 1, '7000', NULL, '21:38:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 14:38:18', '2025-12-09 14:38:18', 'postpaid', 'unpaid', NULL),
(97, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '1', '21:38:00', '22:38:00', NULL, '7000', 0.00, 'sukses', '2025-12-09 14:38:33', '2025-12-09 14:38:33', 'prepaid', 'unpaid', NULL),
(98, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '2', '21:48:00', '23:48:00', NULL, '17000', 0.00, 'selesai', '2025-12-09 14:48:52', '2025-12-09 14:49:48', 'prepaid', 'paid', 'e-wallet'),
(99, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 1, '10000', NULL, '21:49:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 14:49:01', '2025-12-09 14:49:01', 'postpaid', 'unpaid', NULL),
(100, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 1, '10000', '2', '22:04:00', '00:04:00', NULL, '20000', 0.00, 'sukses', '2025-12-09 15:04:22', '2025-12-09 15:04:22', 'prepaid', 'unpaid', NULL),
(101, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '15000.00', '60', '22:08:00', '10:08:00', NULL, '15000.00', 0.00, 'sukses', '2025-12-09 15:08:54', '2025-12-09 15:08:54', 'custom_package', 'unpaid', NULL),
(102, 'user', NULL, 'affan', NULL, 20, NULL, 0.00, 1, '10000', '2', '22:09:00', '00:09:00', NULL, '20000', 0.00, 'selesai', '2025-12-09 15:09:40', '2025-12-09 15:09:49', 'prepaid', 'paid', 'e-wallet'),
(103, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '15000.00', '60', '22:10:00', '10:10:00', NULL, '15000.00', 0.00, 'sukses', '2025-12-09 15:10:07', '2025-12-09 15:10:07', 'custom_package', 'unpaid', NULL),
(104, 'user', NULL, 'khairil affan', NULL, 26, NULL, 0.00, 1, '10000.00', '120', '22:10:00', '22:10:00', NULL, '10000.00', 0.00, 'sukses', '2025-12-09 15:10:52', '2025-12-09 15:10:52', 'custom_package', 'unpaid', NULL),
(105, 'user', NULL, 'affan', NULL, 24, NULL, 0.00, 1, '20000.00', '60', '22:13:00', '10:13:00', NULL, '20000.00', 0.00, 'sukses', '2025-12-09 15:13:31', '2025-12-09 15:13:31', 'custom_package', 'unpaid', NULL),
(106, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '22:30:00', '10:30:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:30:57', '2025-12-09 15:30:57', 'custom_package', 'unpaid', NULL),
(107, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '22:31:00', '10:31:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:31:25', '2025-12-09 15:31:25', 'custom_package', 'unpaid', NULL),
(108, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '22:36:00', '10:36:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:36:15', '2025-12-09 15:36:15', 'custom_package', 'unpaid', NULL),
(109, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '22:36:00', '10:36:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:36:58', '2025-12-09 15:36:58', 'custom_package', 'unpaid', NULL),
(110, 'user', NULL, 'affan', NULL, 26, NULL, 0.00, 1, '12000.00', '60', '22:38:00', '10:38:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:38:58', '2025-12-09 15:38:58', 'custom_package', 'unpaid', NULL),
(111, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '12000.00', '60', '22:40:00', '10:40:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:40:12', '2025-12-09 15:40:12', 'custom_package', 'unpaid', NULL),
(112, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '12000.00', '60', '22:44:00', '10:44:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:44:14', '2025-12-09 15:44:14', 'custom_package', 'unpaid', NULL),
(113, 'user', NULL, 'khairil affan', NULL, NULL, NULL, 0.00, 1, '0', NULL, '22:56:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 15:56:40', '2025-12-09 15:56:40', 'postpaid', 'unpaid', NULL),
(114, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '2', '22:57:00', '00:57:00', NULL, '14000', 0.00, 'sukses', '2025-12-09 15:57:00', '2025-12-09 15:57:00', 'prepaid', 'unpaid', NULL),
(115, 'user', NULL, 'affan', NULL, 24, NULL, 0.00, 1, '12000.00', '60', '22:57:00', '10:57:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 15:57:54', '2025-12-09 15:57:54', 'custom_package', 'unpaid', NULL),
(116, 'user', NULL, 'affan', NULL, 21, NULL, 0.00, 1, '10000', NULL, '23:00:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 16:00:36', '2025-12-09 16:00:36', 'postpaid', 'unpaid', NULL),
(117, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '12000.00', '60', '23:01:00', '11:01:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 16:01:20', '2025-12-09 16:01:20', 'custom_package', 'unpaid', NULL),
(118, 'user', NULL, 'khairil affan', NULL, 24, NULL, 0.00, 1, '7000', NULL, '23:04:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 16:04:54', '2025-12-09 16:04:54', 'postpaid', 'unpaid', NULL),
(119, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '12000.00', '60', '23:06:00', '11:06:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 16:06:24', '2025-12-09 16:06:24', 'custom_package', 'unpaid', NULL),
(120, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '9', '23:35:00', '08:35:00', NULL, '63000', 0.00, 'sukses', '2025-12-09 16:35:36', '2025-12-09 16:35:36', 'prepaid', 'unpaid', NULL),
(121, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '7000', NULL, '23:35:00', NULL, NULL, '0', 0.00, 'berjalan', '2025-12-09 16:35:58', '2025-12-09 16:35:58', 'postpaid', 'unpaid', NULL),
(122, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', '6', '00:10:00', '00:00:00', NULL, '42000', 0.00, 'sukses', '2025-12-09 17:10:43', '2025-12-10 13:25:31', 'prepaid', 'unpaid', NULL),
(123, 'user', NULL, 'SEPATU', NULL, 1, NULL, 0.00, 1, '7000', '14 jam 49 menit', '00:10:00', '14:59:00', NULL, '104000', 0.00, 'selesai', '2025-12-09 17:10:58', '2025-12-10 07:59:56', 'postpaid', 'paid', 'transfer_bank'),
(124, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '12000.00', '60', '00:11:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 17:11:23', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(125, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '13 jam 20 menit', '00:13:00', '13:33:00', NULL, '12000', 0.00, 'selesai', '2025-12-09 17:13:12', '2025-12-10 06:34:33', 'postpaid', 'paid', 'e-wallet'),
(126, 'user', NULL, 'affan', '0896309862787', 13, NULL, 0.00, 1, '12000.00', '60', '00:20:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 17:20:13', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(127, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '12000.00', '60', '00:20:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 17:20:43', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(128, 'user', NULL, 'affan', NULL, 14, NULL, 0.00, 1, '12000.00', '60', '00:26:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-09 17:26:58', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(129, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '12000.00', '60', '13:34:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 06:34:57', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(130, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '12000.00', '60', '13:46:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 06:46:19', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(131, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '12000.00', '60', '13:56:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 06:56:46', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(132, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 1, '10000.00', '60', '14:19:00', '00:00:00', NULL, '10000.00', 0.00, 'sukses', '2025-12-10 07:19:45', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(133, 'user', NULL, 'rahayu', '0896309862787', 19, NULL, 0.00, 1, '12000.00', '60', '14:24:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:24:13', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(134, 'user', NULL, 'affan', '0896309862787', 15, NULL, 0.00, 1, '7000', '1', '14:26:00', '00:00:00', NULL, '7000', 0.00, 'sukses', '2025-12-10 07:26:30', '2025-12-10 13:25:31', 'prepaid', 'unpaid', NULL),
(135, 'user', NULL, 'affan', '0896309862787', 13, NULL, 0.00, 1, '7000', '5 jam 40 menit', '14:26:00', '20:06:00', NULL, '40000', 0.00, 'selesai', '2025-12-10 07:26:52', '2025-12-10 13:07:33', 'postpaid', 'paid', 'transfer_bank'),
(136, 'user', NULL, 'affan', NULL, 14, NULL, 0.00, 1, '12000.00', '60', '14:27:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:27:49', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(137, 'user', NULL, 'affan', NULL, 16, NULL, 20.00, 1, '12000.00', '60', '14:28:00', '15:28:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 07:28:08', '2025-12-10 12:43:24', 'custom_package', 'paid', 'tunai'),
(138, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '5 jam 19 menit', '14:29:00', '19:48:00', NULL, '38000', 0.00, 'selesai', '2025-12-10 07:29:12', '2025-12-10 12:48:44', 'postpaid', 'unpaid', NULL),
(139, 'user', NULL, 'affan', NULL, 20, NULL, 0.00, 1, '10000', '1', '14:29:00', '15:29:00', NULL, '20000', 0.00, 'selesai', '2025-12-10 07:29:49', '2025-12-10 07:30:16', 'prepaid', 'paid', 'e-wallet'),
(140, 'user', NULL, 'affan', NULL, 24, NULL, 0.00, 1, '12000.00', '60', '14:30:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:30:46', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(141, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '12000.00', '60', '14:31:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:31:21', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(142, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '12000.00', '60', '14:31:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:31:52', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(143, 'user', NULL, 'affan', NULL, 21, NULL, 0.00, 1, '10000', '1', '14:40:00', '00:00:00', NULL, '10000', 0.00, 'sukses', '2025-12-10 07:40:00', '2025-12-10 13:25:32', 'prepaid', 'unpaid', NULL),
(144, 'user', NULL, 'affan', NULL, 22, NULL, 0.00, 1, '12000.00', '60', '14:43:00', '00:00:00', NULL, '12000.00', 0.00, 'sukses', '2025-12-10 07:43:19', '2025-12-10 13:25:32', 'custom_package', 'unpaid', NULL),
(145, 'user', NULL, 'affan', NULL, 26, NULL, 0.00, 1, '12000.00', '60', '14:45:00', '15:45:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 07:45:37', '2025-12-10 12:27:08', 'custom_package', 'paid', 'e-wallet'),
(146, 'user', NULL, 'affan', NULL, 27, NULL, 0.00, 1, '12000.00', '60', '14:47:00', '15:47:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 07:47:49', '2025-12-10 12:25:10', 'custom_package', 'paid', 'transfer_bank'),
(147, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '12000.00', '60', '15:00:00', '16:00:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 08:00:27', '2025-12-10 08:00:57', 'custom_package', 'paid', 'tunai'),
(148, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '15:04:00', '16:04:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 08:04:23', '2025-12-10 12:24:40', 'custom_package', 'paid', 'tunai'),
(149, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '12000.00', '60', '19:17:00', '20:17:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 12:17:21', '2025-12-10 12:19:06', 'custom_package', 'paid', 'tunai'),
(150, 'user', NULL, 'affan', NULL, 23, NULL, 0.00, 1, '10000', '9', '19:17:00', '04:17:00', NULL, '15000', 0.00, 'selesai', '2025-12-10 12:17:54', '2025-12-10 12:19:36', 'prepaid', 'paid', 'tunai'),
(151, 'user', NULL, 'affan', NULL, 28, NULL, 0.00, 1, '25000', '0 jam 10 menit', '19:18:00', '19:28:00', NULL, '5000', 0.00, 'selesai', '2025-12-10 12:18:08', '2025-12-10 12:28:50', 'postpaid', 'paid', 'tunai'),
(152, 'user', NULL, 'affan', NULL, 12, NULL, 10.00, 1, '7000', '2', '19:32:00', '21:32:00', NULL, '14000', 0.00, 'selesai', '2025-12-10 12:32:14', '2025-12-10 12:32:25', 'prepaid', 'paid', 'tunai'),
(153, 'user', NULL, 'affan', NULL, 18, NULL, 50.00, 1, '12000.00', '60', '19:32:00', '20:32:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 12:32:59', '2025-12-10 12:33:19', 'custom_package', 'paid', 'tunai'),
(154, 'user', NULL, 'affan', NULL, 22, NULL, 0.00, 1, '12000.00', '60', '19:33:00', '20:33:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 12:33:34', '2025-12-10 12:38:46', 'custom_package', 'paid', 'tunai'),
(155, 'user', NULL, 'affan', NULL, 30, NULL, 10.00, 1, '25000', '2', '19:34:00', '21:34:00', NULL, '50000', 0.00, 'selesai', '2025-12-10 12:34:05', '2025-12-10 12:36:02', 'prepaid', 'paid', 'tunai'),
(156, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 1, '10000.00', '60', '19:46:00', '20:46:00', NULL, '10000.00', 0.00, 'selesai', '2025-12-10 12:46:35', '2025-12-10 12:46:43', 'custom_package', 'paid', 'transfer_bank'),
(157, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '0 jam 0 menit', '20:08:00', '20:08:00', NULL, '0', 0.00, 'selesai', '2025-12-10 13:08:09', '2025-12-10 13:08:24', 'postpaid', 'unpaid', NULL),
(158, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 1, '7000', '1 jam 50 menit', '20:09:00', '21:59:00', NULL, '13000', 0.00, 'selesai', '2025-12-10 13:09:25', '2025-12-10 14:59:57', 'postpaid', 'paid', 'e-wallet'),
(159, 'user', NULL, 'affan', NULL, 14, NULL, 0.00, 1, '7000', '1 jam 47 menit', '20:12:00', '21:59:00', NULL, '12500', 0.00, 'selesai', '2025-12-10 13:12:03', '2025-12-10 14:59:47', 'postpaid', 'paid', 'transfer_bank'),
(160, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '0 jam 26 menit', '20:12:00', '20:38:00', NULL, '3500', 0.00, 'selesai', '2025-12-10 13:12:25', '2025-12-10 13:39:21', 'postpaid', 'paid', 'tunai'),
(161, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '1 jam 44 menit', '20:15:00', '21:59:00', NULL, '12500', 0.00, 'selesai', '2025-12-10 13:15:05', '2025-12-10 14:59:36', 'postpaid', 'paid', 'transfer_bank'),
(162, 'user', NULL, 'affan', NULL, 17, NULL, 0.00, 1, '7000', '1 jam 44 menit', '20:15:00', '21:59:00', NULL, '12500', 0.00, 'selesai', '2025-12-10 13:15:46', '2025-12-10 14:59:24', 'postpaid', 'paid', 'e-wallet'),
(163, 'user', NULL, 'affan', NULL, 20, NULL, 0.00, 1, '10000', '1 jam 43 menit', '20:16:00', '21:59:00', NULL, '17500', 0.00, 'selesai', '2025-12-10 13:16:12', '2025-12-10 14:59:09', 'postpaid', 'paid', 'e-wallet'),
(164, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '1 jam 39 menit', '20:19:00', '21:58:00', NULL, '12000', 0.00, 'selesai', '2025-12-10 13:19:19', '2025-12-10 14:58:57', 'postpaid', 'paid', 'e-wallet'),
(165, 'user', NULL, 'affan', NULL, 21, NULL, 0.00, 1, '10000', '1 jam 38 menit', '20:20:00', '21:58:00', NULL, '16500', 0.00, 'selesai', '2025-12-10 13:20:03', '2025-12-10 14:58:44', 'postpaid', 'paid', 'e-wallet'),
(166, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '7000', '1 jam 33 menit', '20:25:00', '21:58:00', NULL, '11000', 0.00, 'selesai', '2025-12-10 13:25:31', '2025-12-10 14:58:28', 'postpaid', 'paid', 'transfer_bank'),
(167, 'user', NULL, 'affan', NULL, 26, NULL, 0.00, 1, '7000', '1 jam 31 menit', '20:27:00', '21:58:00', NULL, '11000', 0.00, 'selesai', '2025-12-10 13:27:43', '2025-12-10 14:58:13', 'postpaid', 'paid', 'transfer_bank'),
(168, 'user', NULL, 'affan', NULL, 27, NULL, 0.00, 1, '7000', '1 jam 29 menit', '20:28:00', '21:57:00', NULL, '10500', 0.00, 'selesai', '2025-12-10 13:28:27', '2025-12-10 14:57:55', 'postpaid', 'paid', 'transfer_bank'),
(169, 'user', NULL, 'affan', NULL, 28, NULL, 0.00, NULL, '25000', '20 jam 49 menit', '00:00:00', '20:49:00', NULL, '520500', 0.00, 'selesai', '2025-12-10 13:45:16', '2025-12-10 13:49:59', 'postpaid', 'unpaid', NULL),
(170, 'user', NULL, 'affan', NULL, 33, NULL, 0.00, 1, '30000', '1 jam 5 menit', '20:52:00', '21:57:00', NULL, '32500', 0.00, 'selesai', '2025-12-10 13:52:25', '2025-12-10 14:57:44', 'postpaid', 'paid', 'transfer_bank'),
(171, 'user', NULL, 'affan', NULL, 31, NULL, 0.00, 1, '25000', '1 jam 5 menit', '20:52:00', '21:57:00', NULL, '27500', 0.00, 'selesai', '2025-12-10 13:52:56', '2025-12-10 14:57:12', 'postpaid', 'paid', 'transfer_bank'),
(172, 'user', NULL, 'affan', NULL, 32, NULL, 0.00, 1, '30000', '1 jam 5 menit', '20:55:00', '22:00:00', NULL, '32500', 0.00, 'selesai', '2025-12-10 13:55:26', '2025-12-10 15:00:17', 'postpaid', 'paid', 'transfer_bank'),
(173, 'user', NULL, 'affan', NULL, 28, NULL, 0.00, 1, '25000', '0 jam 57 menit', '20:59:00', '21:56:00', NULL, '24000', 0.00, 'selesai', '2025-12-10 13:59:42', '2025-12-10 14:56:49', 'postpaid', 'paid', 'transfer_bank'),
(174, 'user', NULL, 'affan', NULL, 22, NULL, 0.00, 1, '10000', NULL, '21:08:00', NULL, '2025-12-10 14:08:33', '0', 0.00, 'berjalan', '2025-12-10 14:08:33', '2025-12-10 14:08:33', 'postpaid', 'unpaid', NULL),
(175, 'user', NULL, 'affan', NULL, 29, NULL, 0.00, 1, '25000', NULL, '21:08:00', NULL, '2025-12-10 14:08:59', '0', 0.00, 'berjalan', '2025-12-10 14:08:59', '2025-12-10 14:08:59', 'postpaid', 'unpaid', NULL),
(176, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 1, '10000', '0 jam 46 menit', '21:09:00', '21:55:00', '2025-12-10 14:09:15', '8000', 0.00, 'selesai', '2025-12-10 14:09:15', '2025-12-10 14:56:05', 'postpaid', 'paid', 'transfer_bank'),
(177, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '7000', NULL, '22:00:00', NULL, '2025-12-10 15:00:41', '0', 0.00, 'berjalan', '2025-12-10 15:00:41', '2025-12-10 16:24:45', 'postpaid', 'unpaid', NULL),
(178, 'user', NULL, 'affan', NULL, 21, NULL, 10.00, 1, '10000', '3', '22:10:00', '01:10:00', '2025-12-10 15:21:28', '30000', 0.00, 'selesai', '2025-12-10 15:10:27', '2025-12-11 12:42:08', 'prepaid', 'paid', 'transfer_bank'),
(179, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 1, '12000.00', '60', '23:02:00', '00:02:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-10 16:02:14', '2025-12-10 16:02:23', 'custom_package', 'paid', 'transfer_bank'),
(180, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '2', '15:14:00', '17:14:00', NULL, '14000', 0.00, 'selesai', '2025-12-11 08:14:35', '2025-12-11 12:10:33', 'prepaid', 'paid', 'transfer_bank'),
(181, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', NULL, '15:14:00', NULL, '2025-12-11 08:14:54', '0', 0.00, 'selesai', '2025-12-11 08:14:54', '2025-12-11 12:09:28', 'postpaid', 'paid', 'transfer_bank'),
(182, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 7, '7000', '3', '15:47:00', '18:47:00', NULL, '21000', 0.00, 'sukses', '2025-12-11 08:47:16', '2025-12-11 08:47:16', 'prepaid', 'unpaid', NULL),
(183, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 7, '7000', NULL, '15:47:00', NULL, '2025-12-11 08:47:41', '0', 0.00, 'berjalan', '2025-12-11 08:47:41', '2025-12-11 08:47:41', 'postpaid', 'unpaid', NULL),
(184, 'user', NULL, 'affan', NULL, 13, NULL, 0.00, 7, '12000.00', '60', '15:48:00', '16:48:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-11 08:48:04', '2025-12-11 08:48:20', 'custom_package', 'paid', 'e-wallet'),
(185, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 8, '12000.00', '60', '15:53:00', '16:53:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-11 08:53:35', '2025-12-11 08:56:38', 'custom_package', 'paid', 'tunai'),
(186, 'user', NULL, 'affan', NULL, 28, NULL, 5.00, 1, '25000', '1', '19:20:00', '20:20:00', NULL, '40000', 0.00, 'selesai', '2025-12-11 12:20:17', '2025-12-11 12:20:30', 'prepaid', 'paid', 'transfer_bank'),
(187, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 1, '7000', '9', '19:27:00', '04:27:00', NULL, '63000', 0.00, 'selesai', '2025-12-11 12:27:00', '2025-12-11 12:42:44', 'prepaid', 'paid', 'transfer_bank'),
(188, 'user', NULL, 'affan', NULL, 25, NULL, 0.00, 1, '12000.00', '60', '21:22:00', '22:22:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-11 14:22:54', '2025-12-11 14:23:02', 'custom_package', 'paid', 'transfer_bank'),
(189, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', NULL, '21:50:00', NULL, '2025-12-11 14:50:36', '0', 0.00, 'berjalan', '2025-12-11 14:50:36', '2025-12-11 14:50:36', 'postpaid', 'unpaid', NULL),
(190, 'user', NULL, 'affan', NULL, 9, NULL, 0.00, 1, '7000', NULL, '21:51:00', NULL, '2025-12-11 14:51:14', '0', 0.00, 'berjalan', '2025-12-11 14:51:14', '2025-12-11 14:51:14', 'postpaid', 'unpaid', NULL),
(191, 'user', NULL, 'affan', NULL, 13, NULL, 5.00, 1, '7000', '0 jam 40 menit', '21:51:00', '22:31:00', '2025-12-11 14:51:46', '5000', 0.00, 'selesai', '2025-12-11 14:51:46', '2025-12-11 15:31:53', 'postpaid', 'paid', 'e-wallet'),
(192, 'user', NULL, 'affan', NULL, 14, NULL, 10.00, 1, '12000.00', '60', '22:16:00', '23:16:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-11 15:16:27', '2025-12-11 15:20:30', 'custom_package', 'paid', 'tunai'),
(193, 'user', NULL, 'affan', NULL, 15, NULL, 0.00, 1, '7000', '0 jam 13 menit', '22:17:00', '22:30:00', '2025-12-11 15:17:01', '2000', 0.00, 'selesai', '2025-12-11 15:17:01', '2025-12-11 15:30:18', 'postpaid', 'unpaid', NULL),
(194, 'user', NULL, 'affan', NULL, 16, NULL, 0.00, 1, '7000', '4', '22:17:00', '02:17:00', NULL, '28000', 0.00, 'sukses', '2025-12-11 15:17:24', '2025-12-11 15:17:24', 'prepaid', 'unpaid', NULL),
(195, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 7, '7000', '5', '10:55:00', '15:55:00', NULL, '35000', 0.00, 'sukses', '2025-12-12 03:55:47', '2025-12-12 03:55:47', 'prepaid', 'unpaid', NULL),
(196, 'user', NULL, 'affan', NULL, 8, NULL, 0.00, 7, '7000', '9 jam 55 menit', '10:56:00', '20:51:00', '2025-12-12 03:56:48', '79500', 0.00, 'selesai', '2025-12-12 03:56:48', '2025-12-12 13:54:12', 'postpaid', 'paid', 'tunai'),
(197, 'user', NULL, 'affan', NULL, 19, NULL, 0.00, 7, '10000.00', '60', '11:03:00', '12:03:00', NULL, '10000.00', 0.00, 'sukses', '2025-12-12 04:03:56', '2025-12-12 04:03:56', 'custom_package', 'unpaid', NULL),
(198, 'user', NULL, 'affan', NULL, 18, 9, 0.00, 1, '14000.00', '120', '11:19:00', '13:19:00', NULL, '14000.00', 0.00, 'sukses', '2025-12-12 04:19:40', '2025-12-12 04:19:40', 'custom_package', 'unpaid', NULL),
(199, 'user', NULL, 'affan', NULL, 9, 8, 0.00, 1, '12000.00', '60', '11:19:00', '12:19:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-12 04:19:55', '2025-12-12 05:38:34', 'custom_package', 'paid', 'tunai'),
(200, 'user', NULL, 'affan', NULL, 12, 8, 0.00, 1, '12000.00', '60', '11:20:00', '12:20:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-12 04:20:13', '2025-12-12 05:38:09', 'custom_package', 'paid', 'transfer_bank'),
(201, 'user', NULL, 'affan', NULL, 9, NULL, 5.00, 8, '7000', '7 jam 33 menit', '12:40:00', '20:13:00', '2025-12-12 05:40:36', '109000', 0.00, 'selesai', '2025-12-12 05:40:36', '2025-12-12 13:41:06', 'postpaid', 'paid', 'tunai'),
(202, 'user', NULL, 'affan', NULL, 12, NULL, 0.00, 8, '7000', '4', '12:43:00', '16:43:00', NULL, '28000', 0.00, 'sukses', '2025-12-12 05:43:45', '2025-12-12 05:43:45', 'prepaid', 'unpaid', NULL),
(203, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '4', '16:19:00', '20:19:00', NULL, '12000', 0.00, 'selesai', '2025-12-12 09:19:37', '2025-12-12 11:12:12', 'prepaid', 'paid', 'transfer_bank'),
(204, 'user', NULL, 'BUdi', NULL, 13, NULL, 0.00, 1, '7000', '3 jam 40 menit', '16:19:00', '19:59:00', '2025-12-12 09:19:49', '27000', 0.00, 'selesai', '2025-12-12 09:19:49', '2025-12-12 13:00:09', 'postpaid', 'unpaid', NULL),
(205, 'user', NULL, 'Jaka', NULL, 14, 8, 10.00, 1, '12000.00', '60', '16:20:00', '17:20:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-12 09:20:09', '2025-12-12 11:08:33', 'custom_package', 'paid', 'e-wallet'),
(206, 'user', NULL, 'affan', NULL, 18, NULL, 0.00, 1, '10000', '3', '19:22:00', '22:22:00', NULL, '20000', 0.00, 'selesai', '2025-12-12 12:22:44', '2025-12-12 13:42:42', 'prepaid', 'paid', 'e-wallet'),
(207, 'user', NULL, 'affan', NULL, 22, NULL, 0.00, 1, '10000', '3', '19:47:00', '22:47:00', '2025-12-12 12:47:00', '36000', 0.00, 'selesai', '2025-12-12 12:47:48', '2025-12-12 13:59:44', 'prepaid', 'paid', 'e-wallet'),
(208, 'user', NULL, 'affan', NULL, 28, NULL, 10.00, 1, '25000', '3', '19:55:00', '22:55:00', NULL, '85000', 0.00, 'selesai', '2025-12-12 12:55:47', '2025-12-12 12:56:32', 'prepaid', 'paid', 'e-wallet'),
(209, 'user', NULL, 'affan', NULL, 1, NULL, 0.00, 1, '7000', '4', '20:58:00', '00:58:00', NULL, '38000', 0.00, 'selesai', '2025-12-12 13:58:04', '2025-12-12 13:58:53', 'prepaid', 'paid', 'e-wallet'),
(210, 'user', NULL, 'affan', NULL, 8, 8, 0.00, 1, '12000.00', '60', '21:00:00', '22:00:00', NULL, '12000.00', 0.00, 'selesai', '2025-12-12 14:00:53', '2025-12-12 14:12:22', 'custom_package', 'paid', 'e-wallet'),
(211, 'user', NULL, 'affan', NULL, 17, 8, 0.00, 1, '12000.00', '60', '21:13:00', '22:13:00', NULL, '18000', 0.00, 'selesai', '2025-12-12 14:13:25', '2025-12-12 14:32:20', 'custom_package', 'paid', 'tunai'),
(212, 'user', NULL, 'affan', NULL, 14, 8, 0.00, 1, '12000.00', '60', '21:20:00', '22:20:00', NULL, '22000', 0.00, 'selesai', '2025-12-12 14:20:27', '2025-12-12 14:26:12', 'custom_package', 'paid', 'e-wallet'),
(213, 'user', NULL, 'affan', NULL, 19, 9, 0.00, 1, '14000.00', '120', '21:39:00', '23:39:00', NULL, '14000.00', 0.00, 'selesai', '2025-12-12 14:39:49', '2025-12-12 14:40:26', 'custom_package', 'paid', 'transfer_bank'),
(214, 'user', NULL, 'affan', NULL, 20, 9, 0.00, 1, '14000.00', '120', '21:42:00', '23:42:00', NULL, '19000', 0.00, 'selesai', '2025-12-12 14:42:29', '2025-12-12 14:43:02', 'custom_package', 'paid', 'tunai'),
(215, 'user', NULL, 'affan', NULL, 32, NULL, 0.00, 1, '30000', '5', '21:44:00', '02:44:00', NULL, '165000', 0.00, 'selesai', '2025-12-12 14:44:43', '2025-12-12 14:45:27', 'prepaid', 'paid', 'e-wallet'),
(216, 'user', NULL, 'affan', NULL, 33, NULL, 0.00, 1, '30000', NULL, '21:46:00', NULL, '2025-12-12 14:46:21', '0', 0.00, 'berjalan', '2025-12-12 14:46:21', '2025-12-12 14:46:21', 'postpaid', 'unpaid', NULL),
(217, 'user', NULL, 'Rian', '089630986', 23, NULL, 9.10, 1, '10000', '4', '21:57:00', '01:57:00', NULL, '45000', 0.00, 'selesai', '2025-12-12 14:57:56', '2025-12-12 15:04:00', 'prepaid', 'paid', 'e-wallet');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_fnbs`
--

CREATE TABLE `transaction_fnbs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `fnb_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transaction_fnbs`
--

INSERT INTO `transaction_fnbs` (`id`, `transaction_id`, `fnb_id`, `qty`, `harga_jual`, `harga_beli`, `created_at`, `updated_at`) VALUES
(31, 41, 9, 1, 5000, 0, '2025-12-07 09:51:46', '2025-12-07 09:51:46'),
(32, 42, 6, 1, 10000, 0, '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(33, 42, 9, 1000, 5000, 0, '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(34, 42, 6, 1, 10000, 0, '2025-12-07 10:01:09', '2025-12-07 10:01:09'),
(35, 47, 6, 1, 10000, 0, '2025-12-07 14:12:13', '2025-12-07 14:12:13'),
(36, 47, 9, 1, 5000, 0, '2025-12-07 14:12:13', '2025-12-07 14:12:13'),
(37, 45, 7, 1, 10000, 0, '2025-12-07 14:12:45', '2025-12-07 14:12:45'),
(38, 47, 10, 2, 1000, 0, '2025-12-07 14:13:19', '2025-12-07 14:16:43'),
(39, 45, 6, 1, 10000, 0, '2025-12-07 14:16:23', '2025-12-07 14:16:23'),
(40, 76, 6, 1, 10000, 0, '2025-12-08 04:20:37', '2025-12-08 04:20:37'),
(41, 76, 7, 1, 10000, 0, '2025-12-08 04:20:37', '2025-12-08 04:20:37'),
(42, 86, 7, 1, 10000, 0, '2025-12-08 14:25:17', '2025-12-08 14:25:17'),
(43, 87, 7, 1, 10000, 0, '2025-12-08 14:30:06', '2025-12-08 14:30:06'),
(44, 88, 7, 1, 10000, 0, '2025-12-08 14:48:14', '2025-12-08 14:48:14'),
(45, 90, 6, 95, 10000, 0, '2025-12-08 15:52:44', '2025-12-08 15:52:44'),
(46, 98, 7, 1, 10000, 0, '2025-12-09 14:49:44', '2025-12-09 14:49:44'),
(47, 104, 9, 1, 5000, 0, '2025-12-09 15:10:52', '2025-12-09 15:10:52'),
(48, 105, 7, 1, 10000, 0, '2025-12-09 15:13:31', '2025-12-09 15:13:31'),
(49, 125, 9, 1, 5000, 0, '2025-12-10 06:34:24', '2025-12-10 06:34:24'),
(50, 132, 7, 1, 10000, 0, '2025-12-10 07:19:45', '2025-12-10 07:19:45'),
(51, 133, 7, 1, 10000, 0, '2025-12-10 07:24:13', '2025-12-10 07:24:13'),
(52, 139, 7, 1, 10000, 0, '2025-12-10 07:30:04', '2025-12-10 07:30:04'),
(53, 142, 7, 1, 10000, 0, '2025-12-10 07:31:52', '2025-12-10 07:31:52'),
(54, 144, 7, 1, 10000, 0, '2025-12-10 07:43:19', '2025-12-10 07:43:19'),
(55, 146, 9, 1, 5000, 0, '2025-12-10 07:47:49', '2025-12-10 07:47:49'),
(56, 147, 9, 1, 5000, 0, '2025-12-10 08:00:27', '2025-12-10 08:00:27'),
(57, 148, 9, 1, 5000, 0, '2025-12-10 08:04:23', '2025-12-10 08:04:23'),
(58, 149, 9, 1, 5000, 0, '2025-12-10 12:17:21', '2025-12-10 12:17:21'),
(59, 150, 9, 1, 5000, 0, '2025-12-10 12:18:28', '2025-12-10 12:18:28'),
(60, 153, 7, 1, 10000, 0, '2025-12-10 12:32:59', '2025-12-10 12:32:59'),
(61, 154, 7, 1, 10000, 0, '2025-12-10 12:33:34', '2025-12-10 12:33:34'),
(62, 156, 7, 1, 10000, 0, '2025-12-10 12:46:35', '2025-12-10 12:46:35'),
(63, 179, 9, 1, 5000, 0, '2025-12-10 16:02:14', '2025-12-10 16:02:14'),
(64, 184, 9, 1, 5000, 0, '2025-12-11 08:48:04', '2025-12-11 08:48:04'),
(65, 185, 7, 1, 10000, 0, '2025-12-11 08:53:35', '2025-12-11 08:53:35'),
(66, 186, 7, 1, 10000, 0, '2025-12-11 12:20:17', '2025-12-11 12:20:17'),
(67, 186, 9, 1, 5000, 0, '2025-12-11 12:20:17', '2025-12-11 12:20:17'),
(68, 188, 9, 1, 5000, 0, '2025-12-11 14:22:54', '2025-12-11 14:22:54'),
(69, 192, 9, 1, 5000, 0, '2025-12-11 15:16:27', '2025-12-11 15:16:27'),
(70, 196, 9, 2, 5000, 0, '2025-12-12 03:56:48', '2025-12-12 13:52:08'),
(71, 197, 7, 1, 10000, 0, '2025-12-12 04:03:56', '2025-12-12 04:03:56'),
(72, 198, 9, 1, 5000, 0, '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(73, 198, 7, 1, 10000, 0, '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(74, 198, 10, 1, 1000, 0, '2025-12-12 04:19:40', '2025-12-12 04:19:40'),
(75, 199, 7, 1, 10000, 0, '2025-12-12 04:19:55', '2025-12-12 04:19:55'),
(76, 199, 9, 1, 5000, 0, '2025-12-12 04:19:55', '2025-12-12 04:19:55'),
(77, 200, 7, 1, 10000, 0, '2025-12-12 04:20:13', '2025-12-12 04:20:13'),
(78, 200, 9, 1, 5000, 0, '2025-12-12 04:20:13', '2025-12-12 04:20:13'),
(79, 205, 7, 1, 10000, 0, '2025-12-12 09:20:09', '2025-12-12 09:20:09'),
(80, 205, 9, 1, 5000, 0, '2025-12-12 09:20:09', '2025-12-12 09:20:09'),
(81, 204, 7, 1, 10000, 0, '2025-12-12 09:20:35', '2025-12-12 09:20:35'),
(82, 203, 9, 1, 5000, 0, '2025-12-12 09:20:50', '2025-12-12 09:20:50'),
(83, 206, 7, 1, 10000, 0, '2025-12-12 12:46:49', '2025-12-12 12:46:49'),
(84, 207, 9, 1, 5000, 0, '2025-12-12 12:47:58', '2025-12-12 12:47:58'),
(85, 208, 7, 1, 10000, 0, '2025-12-12 12:55:59', '2025-12-12 12:55:59'),
(86, 204, 9, 2, 5000, 0, '2025-12-12 13:00:09', '2025-12-12 13:00:09'),
(88, 201, 7, 3, 10000, 0, '2025-12-12 13:32:37', '2025-12-12 13:34:02'),
(89, 201, 9, 5, 5000, 0, '2025-12-12 13:34:17', '2025-12-12 13:38:39'),
(90, 201, 10, 1, 1000, 0, '2025-12-12 13:36:48', '2025-12-12 13:36:48'),
(91, 209, 7, 1, 10000, 0, '2025-12-12 13:58:19', '2025-12-12 13:58:19'),
(92, 207, 10, 1, 1000, 0, '2025-12-12 13:59:37', '2025-12-12 13:59:37'),
(93, 210, 7, 1, 10000, 0, '2025-12-12 14:00:53', '2025-12-12 14:00:53'),
(94, 210, 9, 1, 5000, 0, '2025-12-12 14:00:53', '2025-12-12 14:00:53'),
(95, 211, 7, 1, 10000, 0, '2025-12-12 14:13:25', '2025-12-12 14:13:25'),
(96, 211, 9, 2, 5000, 0, '2025-12-12 14:13:25', '2025-12-12 14:28:31'),
(97, 212, 7, 2, 10000, 0, '2025-12-12 14:20:27', '2025-12-12 14:25:46'),
(98, 212, 9, 1, 5000, 0, '2025-12-12 14:20:27', '2025-12-12 14:20:27'),
(99, 211, 10, 1, 1000, 0, '2025-12-12 14:32:03', '2025-12-12 14:32:03'),
(100, 213, 9, 1, 5000, 0, '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(101, 213, 7, 1, 10000, 0, '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(102, 213, 10, 1, 1000, 0, '2025-12-12 14:39:49', '2025-12-12 14:39:49'),
(103, 214, 9, 2, 5000, 0, '2025-12-12 14:42:29', '2025-12-12 14:42:45'),
(104, 214, 7, 1, 10000, 0, '2025-12-12 14:42:29', '2025-12-12 14:42:29'),
(105, 214, 10, 1, 1000, 0, '2025-12-12 14:42:29', '2025-12-12 14:42:29'),
(106, 215, 7, 1, 10000, 0, '2025-12-12 14:45:11', '2025-12-12 14:45:11'),
(107, 215, 9, 1, 5000, 0, '2025-12-12 14:45:11', '2025-12-12 14:45:11'),
(108, 217, 9, 1, 5000, 0, '2025-12-12 14:58:06', '2025-12-12 14:58:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','owner','kasir') NOT NULL DEFAULT 'kasir',
  `shift` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `shift`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '2025-11-30 04:39:14', '$2y$10$tOyA9MTVTB3BfptsKQavHeUKP1DvfjfRgARElaaAAXoK7TiweGy7q', 'admin', NULL, '5TK5UBLRF3Lhl8j5WmiKSj1G1QTqzLO9iNxLm4pjiwBbuujKHVW0PqLqyD3U', '2025-11-30 04:39:14', '2025-12-09 14:07:12'),
(6, 'khairil affan', 'owner@gmail.com', NULL, '$2y$10$.i9WfuY7MLRP/I2XGJREE.8UQBNHZFcxpsR8IemUPrShFiW2TQRPS', 'owner', NULL, NULL, '2025-12-11 08:40:12', '2025-12-11 08:40:12'),
(7, 'shift1', 'shift1@gmail.com', NULL, '$2y$10$dP6IqvODD0u7xGGX2nIwxeMbJarWEOq0jhtFs1B7bb7aWWqn1WxCe', 'kasir', 'pagi', NULL, '2025-12-11 08:42:59', '2025-12-11 14:46:37'),
(8, 'shift2', 'shift2@gmail.com', NULL, '$2y$10$c1e2I6aEvcKaQXYFcvZsCu7McCYuY4Gttcu0KPVBmObTSCb/3/zkS', 'kasir', 'malam', NULL, '2025-12-11 08:50:34', '2025-12-11 08:50:34');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `custom_packages`
--
ALTER TABLE `custom_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `custom_package_fnb`
--
ALTER TABLE `custom_package_fnb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_package_fnb_custom_package_id_foreign` (`custom_package_id`),
  ADD KEY `custom_package_fnb_fnb_id_foreign` (`fnb_id`);

--
-- Indeks untuk tabel `custom_package_playstation`
--
ALTER TABLE `custom_package_playstation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_package_playstation_custom_package_id_foreign` (`custom_package_id`),
  ADD KEY `custom_package_playstation_playstation_id_foreign` (`playstation_id`);

--
-- Indeks untuk tabel `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`);

--
-- Indeks untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `fnbs`
--
ALTER TABLE `fnbs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fnbs_price_group_id_foreign` (`price_group_id`);

--
-- Indeks untuk tabel `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_user_id_unique` (`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `playstations`
--
ALTER TABLE `playstations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `price_groups`
--
ALTER TABLE `price_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_mutations_fnb_id_foreign` (`fnb_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `transactions_custom_package_id_foreign` (`custom_package_id`);

--
-- Indeks untuk tabel `transaction_fnbs`
--
ALTER TABLE `transaction_fnbs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_fnbs_fnb_id_foreign` (`fnb_id`),
  ADD KEY `transaction_fnbs_transaction_id_foreign` (`transaction_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `custom_packages`
--
ALTER TABLE `custom_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `custom_package_fnb`
--
ALTER TABLE `custom_package_fnb`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `custom_package_playstation`
--
ALTER TABLE `custom_package_playstation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `devices`
--
ALTER TABLE `devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fnbs`
--
ALTER TABLE `fnbs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `playstations`
--
ALTER TABLE `playstations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `price_groups`
--
ALTER TABLE `price_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `stock_mutations`
--
ALTER TABLE `stock_mutations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id_transaksi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT untuk tabel `transaction_fnbs`
--
ALTER TABLE `transaction_fnbs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `custom_package_fnb`
--
ALTER TABLE `custom_package_fnb`
  ADD CONSTRAINT `custom_package_fnb_custom_package_id_foreign` FOREIGN KEY (`custom_package_id`) REFERENCES `custom_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_package_fnb_fnb_id_foreign` FOREIGN KEY (`fnb_id`) REFERENCES `fnbs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `custom_package_playstation`
--
ALTER TABLE `custom_package_playstation`
  ADD CONSTRAINT `custom_package_playstation_custom_package_id_foreign` FOREIGN KEY (`custom_package_id`) REFERENCES `custom_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `custom_package_playstation_playstation_id_foreign` FOREIGN KEY (`playstation_id`) REFERENCES `playstations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `fnbs`
--
ALTER TABLE `fnbs`
  ADD CONSTRAINT `fnbs_price_group_id_foreign` FOREIGN KEY (`price_group_id`) REFERENCES `price_groups` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD CONSTRAINT `stock_mutations_fnb_id_foreign` FOREIGN KEY (`fnb_id`) REFERENCES `fnbs` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_custom_package_id_foreign` FOREIGN KEY (`custom_package_id`) REFERENCES `custom_packages` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `transaction_fnbs`
--
ALTER TABLE `transaction_fnbs`
  ADD CONSTRAINT `transaction_fnbs_fnb_id_foreign` FOREIGN KEY (`fnb_id`) REFERENCES `fnbs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_fnbs_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id_transaksi`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
