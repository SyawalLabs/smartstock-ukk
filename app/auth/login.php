<?php

/**
 * Login Page
 * Clean design with Heroicons
 */

session_start();
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

$base_url = BASE_URL;

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();

            header('Location: ' . BASE_URL . '/app/dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SmartStock</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/bootstrap-icons/bootstrap-icons.css">
    <link href="<?= $base_url ?>/app/assets/css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="bi bi-box-seam-fill fs-3 text-primary"></i>

            </div>
            <h1>SmartStock</h1>
            <p>Silakan login untuk melanjutkan</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    placeholder="Masukkan username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Login
            </button>
        </form>

        <div class="demo-box">
            <p><strong>Demo:</strong> admin / admin123</p>
        </div>
    </div>
</body>

</html>