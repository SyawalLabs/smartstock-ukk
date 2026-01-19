-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 19, 2026 at 03:48 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.9
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartstock_ukk`
--
-- --------------------------------------------------------
--
-- Table structure for table `barang`
--
CREATE TABLE
  `barang` (
    `id_barang` int NOT NULL,
    `nama` varchar(100) NOT NULL,
    `stok` int NOT NULL,
    `harga` decimal(10, 2) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Table structure for table `transaksi`
--
CREATE TABLE
  `transaksi` (
    `id_transaksi` int NOT NULL,
    `id_barang` int NOT NULL,
    `id_user` int NOT NULL,
    `jenis_transaksi` varchar(10) NOT NULL,
    `jumlah` int NOT NULL,
    `tanggal_waktu` datetime NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--
CREATE TABLE
  `users` (
    `id_user` int NOT NULL,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role` varchar(20) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--
--
-- Indexes for table `barang`
--
ALTER TABLE `barang` ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi` ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users` ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang` MODIFY `id_barang` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi` MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users` MODIFY `id_user` int NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Insert user admin
-- Password: admin123 (sudah di-hash dengan bcrypt)
INSERT INTO
  users (username, password)
VALUES
  (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
  );

-- Insert data barang contoh
INSERT INTO
  barang (nama_barang, stok, harga)
VALUES
  ('Laptop ASUS ROG', 10, 15000000.00),
  ('Mouse Logitech G502', 25, 850000.00),
  ('Keyboard Mechanical', 15, 1200000.00),
  ('Monitor Samsung 24"', 8, 2500000.00),
  ('Headset Gaming', 20, 450000.00);