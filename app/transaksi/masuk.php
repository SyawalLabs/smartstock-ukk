<?php

/**
 * Transaksi Barang Masuk
 * Proses form SEBELUM include header!
 */

session_start();
date_default_timezone_set('Asia/Jakarta');

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: /bahan-ajar-ukk/app/auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

// Ambil daftar barang untuk dropdown
$stmt = $pdo->query("SELECT id, nama_barang, stok FROM barang ORDER BY nama_barang");
$daftar_barang = $stmt->fetchAll();

$error = '';

// PROSES FORM SEBELUM OUTPUT HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int) ($_POST['id_barang'] ?? 0);
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validasi input
    if ($id_barang <= 0) {
        $error = 'Pilih barang terlebih dahulu!';
    } elseif ($jumlah <= 0) {
        $error = 'Jumlah harus lebih dari 0!';
    } else {
        try {
            // Mulai transaction
            $pdo->beginTransaction();

            // 1. INSERT ke tabel transaksi
            $stmt = $pdo->prepare("INSERT INTO transaksi (id_barang, jenis_transaksi, jumlah, keterangan) VALUES (?, 'masuk', ?, ?)");
            $stmt->execute([$id_barang, $jumlah, $keterangan]);

            // 2. UPDATE stok barang (TAMBAH)
            $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
            $stmt->execute([$jumlah, $id_barang]);

            // Commit - simpan permanen
            $pdo->commit();

            $_SESSION['flash_message'] = "Barang masuk berhasil!";
            $_SESSION['flash_type'] = 'success';
            header('Location: riwayat.php');
            exit;
        } catch (Exception $e) {
            // Rollback - batalkan semua kalau error
            $pdo->rollback();
            $error = 'Terjadi kesalahan!';
        }
    }
}

// BARU include header setelah proses selesai
$page_title = 'Barang Masuk';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- HTML Form di sini -->
<div class="page-header">
    <h1>Barang Masuk</h1>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Pilih Barang *</label>
                <select name="id_barang" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($daftar_barang as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Jumlah Masuk *</label>
                <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>