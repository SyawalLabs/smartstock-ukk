<?php

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Load config untuk BASE_URL dinamis
require_once __DIR__ . '/../config/config.php';

// Cek apakah sudah login (kecuali halaman login)
$current_file = basename($_SERVER['PHP_SELF']);
if ($current_file !== 'login.php' && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/auth/login.php');
    exit;
}

// Base URL - gunakan konstanta dari config.php
$base_url = BASE_URL;

// Ambil informasi halaman aktif
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'RanInventory' ?> | RanInventory</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/bootstrap-icons/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/datatables.css">
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/datatables.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link href="<?= $base_url ?>/app/assets/css/style.css" rel="stylesheet">
</head>

<style>
    .sidebar {
        background: linear-gradient(180deg, #0f172a, #1e293b);
        color: #e2e8f0;
    }

    .sidebar-logo span {
        color: #fff;
        font-weight: 600;
    }

    .nav-item {
        color: #cbd5e1;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 10px;
        transition: 0.3s;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .nav-item.active {
        background: #2563eb;
        color: #fff;
    }

    .nav-section-title {
        font-size: 12px;
        color: #94a3b8;
        margin: 10px 0;
    }

    .sidebar-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 10px;
    }
</style>

<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="bi bi-buildings-fill fs-3 text-primary"></i>
                    <span>RanInventory</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-section-title">Menu Utama</span>

                    <a href="<?= $base_url ?>/app/dashboard.php" class="nav-item <?= ($current_page === 'dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-grid-1x2-fill fs-5"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="<?= $base_url ?>/app/barang/" class="nav-item <?= ($current_dir === 'barang') ? 'active' : '' ?>">
                        <i class="bi bi-archive-fill fs-5"></i>
                        <span>Data Barang</span>
                    </a>

                    <a href="<?= $base_url ?>/app/transaksi/masuk.php" class="nav-item <?= ($current_page === 'masuk') ? 'active' : '' ?>">
                        <i class="bi bi-arrow-down-square-fill fs-5"></i>
                        <span>Barang Masuk</span>
                    </a>

                    <a href="<?= $base_url ?>/app/transaksi/keluar.php" class="nav-item <?= ($current_page === 'keluar') ? 'active' : '' ?>">
                        <i class="bi bi-arrow-up-square-fill fs-5"></i>
                        <span>Barang Keluar (Transaksi)</span>
                    </a>

                    <a href="<?= $base_url ?>/app/transaksi/riwayat.php" class="nav-item <?= ($current_page === 'riwayat') ? 'active' : '' ?>">
                        <i class="bi bi-clock-fill fs-5"></i>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <a href="<?= $base_url ?>/app/laporan/" class="nav-item <?= ($current_dir === 'laporan' && $current_page === 'index') ? 'active' : '' ?>">
                        <i class="bi bi-clipboard-data-fill fs-5"></i>
                        <span>Lihat Laporan</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="bi bi-person fs-3 text-dark"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name text-light"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                        <span class="user-role">Admin</span>
                    </div>
                </div>
                <a href="<?= $base_url ?>/app/auth/logout.php" class="logout-btn" title="Logout">
                    <i class="bi bi-door-open-fill fs-5 text-danger"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">