-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Jun 2026 pada 07.04
-- Versi server: 8.0.30
-- Versi PHP: 8.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `koslife`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `focus_sessions`
--

CREATE TABLE `focus_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `task` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int NOT NULL,
  `session_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('in_progress','paused','completed','interrupted','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `focus_sessions`
--

INSERT INTO `focus_sessions` (`id`, `user_id`, `task`, `duration`, `session_type`, `status`, `started_at`, `ended_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'd', 60, 'custom', 'completed', '2026-06-22 18:35:02', '2026-06-22 18:36:02', '2026-06-22 18:35:02', '2026-06-22 18:36:02'),
(2, 1, 'd', 60, 'custom', 'completed', '2026-06-22 18:36:13', '2026-06-22 18:37:16', '2026-06-22 18:36:13', '2026-06-22 18:37:16'),
(3, 1, 'tes', 60, 'custom', 'completed', '2026-06-22 18:47:31', '2026-06-22 18:48:31', '2026-06-22 18:47:31', '2026-06-22 18:48:31'),
(4, 1, 'tes', 60, 'custom', 'completed', '2026-06-22 18:49:45', '2026-06-22 18:50:46', '2026-06-22 18:49:45', '2026-06-22 18:50:46'),
(5, 1, 'tes', 1, 'custom', 'completed', '2026-06-23 02:04:37', '2026-06-23 02:05:37', '2026-06-23 02:04:37', '2026-06-23 02:05:37'),
(6, 1, 'tes', 25, 'study', 'in_progress', '2026-06-24 02:45:25', NULL, '2026-06-24 02:45:25', '2026-06-24 02:45:25'),
(7, 1, 'tes', 25, 'study', 'completed', '2026-06-24 03:03:56', '2026-06-24 03:32:24', '2026-06-24 03:03:56', '2026-06-24 03:32:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `habits`
--

CREATE TABLE `habits` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '✅',
  `target_frequency` enum('daily','weekly','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'daily',
  `streak` int NOT NULL DEFAULT '0',
  `best_streak` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `habits`
--

INSERT INTO `habits` (`id`, `user_id`, `name`, `icon`, `target_frequency`, `streak`, `best_streak`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bangun Pagi', '🌅', 'daily', 2, 2, 1, '2026-06-21 19:33:16', '2026-06-24 04:17:30'),
(2, 1, 'Belajar', '📚', 'daily', 1, 1, 1, '2026-06-21 19:33:17', '2026-06-23 02:38:05'),
(3, 1, 'Ngoding', '💻', 'daily', 1, 1, 1, '2026-06-21 19:33:17', '2026-06-24 04:17:40'),
(4, 1, 'Membaca', '📖', 'daily', 0, 0, 1, '2026-06-21 19:33:17', '2026-06-21 19:33:17'),
(5, 1, 'Olahraga', '🏋️', 'daily', 0, 0, 1, '2026-06-21 19:33:17', '2026-06-21 19:33:17'),
(6, 1, 'Minum Air', '💧', 'daily', 1, 1, 1, '2026-06-21 19:33:17', '2026-06-23 02:12:14'),
(7, 1, 'Tidur Tepat Waktu', '😴', 'daily', 0, 0, 1, '2026-06-21 19:33:17', '2026-06-21 19:33:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `habit_logs`
--

CREATE TABLE `habit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `habit_id` bigint UNSIGNED NOT NULL,
  `log_date` date NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `habit_logs`
--

INSERT INTO `habit_logs` (`id`, `habit_id`, `log_date`, `is_completed`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-23', 1, '2026-06-23 02:11:34', '2026-06-23 02:11:34'),
(2, 6, '2026-06-23', 1, '2026-06-23 02:12:14', '2026-06-23 02:12:14'),
(3, 2, '2026-06-23', 1, '2026-06-23 02:38:05', '2026-06-23 02:38:05'),
(4, 1, '2026-06-24', 1, '2026-06-24 04:17:30', '2026-06-24 04:17:30'),
(5, 3, '2026-06-24', 1, '2026-06-24 04:17:40', '2026-06-24 04:17:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `meal_budgets`
--

CREATE TABLE `meal_budgets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `meal_date` date NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `meal_budgets`
--

INSERT INTO `meal_budgets` (`id`, `user_id`, `meal_date`, `meal_type`, `amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-22', 'breakfast', 5000.00, 'Nasi Campur', '2026-06-22 08:19:07', '2026-06-22 08:19:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_22_021308_create_tasks_table', 1),
(5, '2026_06_22_021317_create_transactions_table', 1),
(6, '2026_06_22_021321_create_meal_budgets_table', 1),
(7, '2026_06_22_021325_create_shopping_items_table', 1),
(8, '2026_06_22_021328_create_schedules_table', 1),
(9, '2026_06_22_021332_create_focus_sessions_table', 1),
(10, '2026_06_22_021336_create_habits_table', 1),
(11, '2026_06_22_021341_create_habit_logs_table', 1),
(12, '2026_06_22_021347_create_notes_table', 1),
(13, '2026_06_22_021353_create_reminders_table', 1),
(14, '2026_06_22_023524_create_sessions_table', 2),
(15, '2026_06_23_013400_fix_focus_sessions_status_enum', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notes`
--

CREATE TABLE `notes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'personal',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `title`, `content`, `category`, `is_pinned`, `color`, `created_at`, `updated_at`) VALUES
(1, 1, 'tes', 'tes', 'pkl', 0, '#FEF3C7', '2026-06-23 02:18:55', '2026-06-23 02:18:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reminders`
--

CREATE TABLE `reminders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reminder_time` datetime NOT NULL,
  `is_notified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `reminders`
--

INSERT INTO `reminders` (`id`, `user_id`, `title`, `description`, `type`, `reminder_time`, `is_notified`, `created_at`, `updated_at`) VALUES
(1, 1, 'beli makan', NULL, 'schedule', '2026-06-23 09:56:00', 0, '2026-06-23 02:26:47', '2026-06-23 02:26:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#4F46E5',
  `is_all_day` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_minutes` int NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `schedules`
--

INSERT INTO `schedules` (`id`, `user_id`, `title`, `description`, `category`, `location`, `start_time`, `end_time`, `color`, `is_all_day`, `reminder_minutes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ngembalikan Raport', NULL, 'school', 'SMK', '2026-06-22 15:49:00', '2026-06-22 16:49:00', '#4f46e5', 0, 0, '2026-06-22 08:49:59', '2026-06-22 08:49:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('yNn07mAbjgfD09WIG91tjfarfqfu0YsMYi12UZGP', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI0ZDdVY2RoaUw2UTRRMkVwVzhGbnpuQjJ2V2JMVkJNOXRIMExTR0ExIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1782275824);

-- --------------------------------------------------------

--
-- Struktur dari tabel `shopping_items`
--

CREATE TABLE `shopping_items` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '1',
  `is_checked` tinyint(1) NOT NULL DEFAULT '0',
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shopping_items`
--

INSERT INTO `shopping_items` (`id`, `user_id`, `name`, `category`, `stock_quantity`, `min_stock`, `is_checked`, `estimated_price`, `created_at`, `updated_at`) VALUES
(1, 1, 'sabun', 'hygiene', 1, 1, 0, 25000.00, '2026-06-22 08:39:41', '2026-06-22 08:39:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('not_started','in_progress','completed','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `deadline` date NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `priority`, `status`, `deadline`, `attachment`, `category`, `notes`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'tugas buat video', NULL, 'high', 'completed', '2026-06-29', NULL, 'pkl', NULL, '2026-06-22 07:59:04', '2026-06-22 07:37:13', '2026-06-22 07:59:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` date NOT NULL,
  `is_debt` tinyint(1) NOT NULL DEFAULT '0',
  `debtor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('paid','unpaid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `category`, `amount`, `description`, `transaction_date`, `is_debt`, `debtor_name`, `payment_status`, `created_at`, `updated_at`) VALUES
(1, 1, 'income', 'uang_saku', 250000.00, 'Uang Sangu', '2026-06-20', 0, NULL, 'paid', '2026-06-22 08:09:39', '2026-06-22 08:09:39'),
(2, 1, 'income', 'uang_saku', 300000.00, 'Uang Sangu', '2026-06-01', 0, NULL, 'paid', '2026-06-23 02:43:14', '2026-06-23 02:43:14'),
(4, 1, 'income', 'uang_saku', 100000.00, 'Uang Sangu', '2026-06-05', 0, NULL, 'paid', '2026-06-23 02:43:58', '2026-06-23 02:43:58'),
(5, 1, 'income', 'uang_saku', 300000.00, 'Uang Sangu', '2026-06-16', 0, NULL, 'paid', '2026-06-23 02:44:19', '2026-06-23 02:44:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `daily_meal_budget` decimal(10,2) NOT NULL DEFAULT '50000.00',
  `savings_goal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `theme` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'light',
  `pomodoro_focus` int NOT NULL DEFAULT '25',
  `pomodoro_break` int NOT NULL DEFAULT '5',
  `daily_focus_target` int NOT NULL DEFAULT '2',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `avatar`, `daily_meal_budget`, `savings_goal`, `currency`, `theme`, `pomodoro_focus`, `pomodoro_break`, `daily_focus_target`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Fh Koslife', 'fhdigital@gmail.com', NULL, '$2y$12$vo6DHPrioVAdgbGXR33LcusnbqUvn6CAX9K5.w27UFXlFhBXbsd3y', '/storage/avatars/mPLeYQRv9z0UD0EGJMxtAxX0EyR6Cen1RA5xB7SL.png', 50000.00, 1000000.00, 'IDR', 'light', 25, 5, 2, NULL, '2026-06-21 19:33:16', '2026-06-23 02:39:19');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indeks untuk tabel `focus_sessions`
--
ALTER TABLE `focus_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `focus_sessions_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `habits`
--
ALTER TABLE `habits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habits_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `habit_logs`
--
ALTER TABLE `habit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habit_logs_habit_id_foreign` (`habit_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `meal_budgets`
--
ALTER TABLE `meal_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meal_budgets_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notes_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminders_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `shopping_items`
--
ALTER TABLE `shopping_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shopping_items_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_user_id_status_deadline_index` (`user_id`,`status`,`deadline`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_type_transaction_date_index` (`user_id`,`type`,`transaction_date`);

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
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `focus_sessions`
--
ALTER TABLE `focus_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `habits`
--
ALTER TABLE `habits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `habit_logs`
--
ALTER TABLE `habit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `meal_budgets`
--
ALTER TABLE `meal_budgets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `shopping_items`
--
ALTER TABLE `shopping_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `focus_sessions`
--
ALTER TABLE `focus_sessions`
  ADD CONSTRAINT `focus_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `habits`
--
ALTER TABLE `habits`
  ADD CONSTRAINT `habits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `habit_logs`
--
ALTER TABLE `habit_logs`
  ADD CONSTRAINT `habit_logs_habit_id_foreign` FOREIGN KEY (`habit_id`) REFERENCES `habits` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `meal_budgets`
--
ALTER TABLE `meal_budgets`
  ADD CONSTRAINT `meal_budgets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `shopping_items`
--
ALTER TABLE `shopping_items`
  ADD CONSTRAINT `shopping_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
