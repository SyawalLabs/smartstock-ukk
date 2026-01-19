<?php
// Mulai session untuk autentikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /bahan-ajar-ukk/app/auth/login.php');
    exit;
}

// Ambil nama halaman untuk navbar active state
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Inventaris Gudang' ?></title>

    <!-- Google Font: Exo 2 -->
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/bahan-ajar-ukk/app/assets/css/style.css" rel="stylesheet">
</head>

<body>