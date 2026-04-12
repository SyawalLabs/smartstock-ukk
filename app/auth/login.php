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
    <title>Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/app/assets/bootstrap-icons/bootstrap-icons.css">
    <link href="<?= $base_url ?>/app/assets/css/style.css" rel="stylesheet">
</head>

<style>
    /* Background utama */
    .login-page {
        height: 100vh;
        background: radial-gradient(circle at top, #1e293b, #0f172a);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Inter', sans-serif;
    }

    /* Wrapper */
    .login-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 20px;
    }

    /* Card */
    .login-card {
        background: rgba(30, 41, 59, 0.9);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        padding: 35px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        color: #e2e8f0;
    }

    /* Header */
    .login-header {
        text-align: center;
        margin-bottom: 25px;
    }

    .logo-icon {
        font-size: 40px;
        color: #3b82f6;
        margin-bottom: 10px;
    }

    .login-header h2 {
        margin: 0;
        color: #fff;
    }

    .login-header p {
        color: #94a3b8;
        font-size: 14px;
    }

    /* Form */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 6px;
        display: block;
    }

    /* Input dengan icon */
    .input-icon {
        display: flex;
        align-items: center;
        background: #0f172a;
        border-radius: 10px;
        padding: 10px;
    }

    .input-icon i {
        color: #64748b;
        margin-right: 10px;
    }

    .input-icon input {
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        width: 100%;
    }

    /* Button */
    .btn-login {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: #2563eb;
        color: white;
        font-weight: 500;
        transition: 0.3s;
    }

    .btn-login:hover {
        background: #1d4ed8;
    }

    /* Demo */
    .demo {
        margin-top: 20px;
        text-align: center;
        color: #64748b;
    }
</style>

<body class="login-page">

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-header">
                <i class="bi bi-buildings-fill logo-icon"></i>
                <h2>RanInventory</h2>
                <p>Manajemen inventaris modern</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-icon">
                        <i class="bi bi-person-fill"></i>
                        <input type="text" name="username"
                            placeholder="Masukkan username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                        <input type="password" name="password"
                            placeholder="Masukkan password"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
                </button>
            </form>

            <div class="demo">
                <small>Demo: admin / admin123</small>
            </div>

        </div>
    </div>

</body>

</html>