<?php
$page_title = 'Edit Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

// Ambil ID dari URL
$id = (int) ($_GET['id'] ?? 0);

// Jika ID tidak valid, redirect
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data barang berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();

// Jika barang tidak ditemukan, redirect
if (!$barang) {
    header('Location: index.php');
    exit;
}

$error = '';

// Proses form jika method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $stok = (int) ($_POST['stok'] ?? 0);
    $harga = (float) ($_POST['harga'] ?? 0);

    if (empty($nama_barang)) {
        $error = 'Nama barang tidak boleh kosong!';
    } elseif ($stok < 0) {
        $error = 'Stok tidak boleh negatif!';
    } else {
        // Update data dengan prepared statement
        $stmt = $pdo->prepare("
            UPDATE barang
            SET nama_barang = ?, stok = ?, harga = ?
            WHERE id = ?
        ");

        if ($stmt->execute([$nama_barang, $stok, $harga, $id])) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data barang berhasil diupdate',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        }
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>✏️ Edit Barang</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control"
                                value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control"
                                value="<?= $barang['stok'] ?>" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control"
                                value="<?= $barang['harga'] ?>" min="0">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Update</button>
                            <a href="index.php" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>