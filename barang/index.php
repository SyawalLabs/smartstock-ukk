<?php
$page_title = 'Data Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

// Ambil semua data barang
$stmt = $pdo->query("SELECT * FROM barang ORDER BY id DESC");
$data_barang = $stmt->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">📦 Data Barang</h1>
        <a href="tambah.php" class="btn btn-primary">
            + Tambah Barang
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="tabelBarang" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_barang as $barang): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
                            <td>
                                <span class="badge bg-<?= $barang['stok'] > 10 ? 'success' : ($barang['stok'] > 0 ? 'warning' : 'danger') ?>">
                                    <?= $barang['stok'] ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($barang['harga'], 0, ',', '.') ?></td>
                            <td>
                                <a href="edit.php?id=<?= $barang['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <button onclick="hapusBarang(<?= $barang['id'] ?>)" class="btn btn-sm btn-danger">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Inisialisasi DataTables
    $('#tabelBarang').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });

    // Fungsi hapus dengan konfirmasi SweetAlert
    function hapusBarang(id) {
        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data barang akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus.php?id=' + id;
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>