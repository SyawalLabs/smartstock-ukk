<?php

/**
 * ============================================
 * HALAMAN LOGIN
 * ============================================
 */

// Mulai session
session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: index.php");
    exit;
}

// Include koneksi database
require_once 'koneksi.php';

// Variabel untuk menyimpan pesan error
$error = "";

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form
    $username = htmlspecialchars($_POST['username']); // Sanitasi input
    $password = $_POST['password']; // Password tidak perlu sanitasi

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Query dengan prepared statement (aman dari SQL Injection)
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username); // "s" = string
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Cek apakah user ditemukan
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password dengan hash di database
            if (password_verify($password, $user['password'])) {
                // PASSWORD BENAR! Set session dan redirect
                $_SESSION['login'] = true;
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Gudang</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-body">
    <div class="login-card">
        <!-- Icon -->
        <div class="login-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25
                2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621
                0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375
                c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
        </div>

        <!-- Title -->
        <h2 class="text-center mb-2 fw-bold">Selamat Datang</h2>
        <p class="text-center text-secondary mb-4">Sistem Inventaris Barang Gudang</p>

        <!-- Form Login -->
        <form method="POST">
            <!-- Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z
                M4.501 20.118a7.5 7.5 0 0114.998 0" />
                        </svg>
                    </span>
                    <input type="text" class="form-control" name="username"
                        placeholder="Masukkan username" required>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5
                a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25
                H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25
                2.25z" />
                        </svg>
                    </span>
                    <input type="password" class="form-control" name="password"
                        placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-login btn-primary w-100">
                Masuk
            </button>
        </form>

        <!-- Info Login Default -->
        <div class="default-login text-center">
            <small class="text-secondary">Login default:</small><br>
            <code>admin</code> / <code>admin123</code>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="assets/js/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

    <!-- Tampilkan Error jika ada -->
    <?php if ($error): ?>
        <script>
            showError('<?= $error ?>');
        </script>
    <?php endif; ?>
</body>

</html>