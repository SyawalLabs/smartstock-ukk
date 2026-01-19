<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil ID dari URL
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    // Hapus data dengan prepared statement
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->execute([$id]);

    // Set flash message untuk SweetAlert
    $_SESSION['flash_message'] = 'Data barang berhasil dihapus!';
    $_SESSION['flash_type'] = 'success';
}

// Redirect kembali ke list
header('Location: index.php');
exit;
