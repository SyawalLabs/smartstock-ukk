<?php
$page_title = 'Tambah Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

$error = '';
$success = '';

// Proses form jika method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan validasi input
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $stok = (int) ($_POST['stok'] ?? 0);
    $harga = (float) ($_POST['harga'] ?? 0);

    // Validasi: nama tidak boleh kosong
    if (empty($nama_barang)) {
        $error = 'Nama barang tidak boleh kosong!';
    }
    // Validasi: stok tidak boleh negatif
    elseif ($stok < 0) {
        $error = 'Stok tidak boleh negatif!';
    }
    // Validasi: harga tidak boleh negatif
    elseif ($harga < 0) {
        $error = 'Harga tidak boleh negatif!';
    } else {
        // Insert ke database dengan prepared statement
        $stmt = $pdo->prepare("
            INSERT INTO barang (nama_barang, stok, harga)
            VALUES (?, ?, ?)
        ");

        if ($stmt->execute([$nama_barang, $stok, $harga])) {
            $success = 'Barang berhasil ditambahkan!';
            // Redirect dengan SweetAlert
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barang berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php';
                });
            </script>";
        } else {
            $error = 'Gagal menyimpan data!';
        }
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>➕ Tambah Barang Baru</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="0" min="0">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            <a href="index.php" class="btn btn-secondary">← Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>