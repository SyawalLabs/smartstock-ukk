<?php

/**
 * Riwayat Transaksi dengan Search dan Pagination
 */

$page_title = 'Riwayat Transaksi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

// Filter dari URL (contoh: riwayat.php?jenis=masuk&dari=2024-01-01)
$filter_jenis = $_GET['jenis'] ?? '';
$filter_dari = $_GET['dari'] ?? date('Y-m-01');
$filter_sampai = $_GET['sampai'] ?? date('Y-m-d');

// Query dengan JOIN untuk ambil nama barang
$sql = "SELECT t.*, b.nama_barang
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id
        WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ?";
$params = [$filter_dari, $filter_sampai];

// Kalau ada filter jenis, tambahkan kondisi
if ($filter_jenis) {
    $sql .= " AND t.jenis_transaksi = ?";
    $params[] = $filter_jenis;
}

$sql .= " ORDER BY t.tanggal_transaksi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data_transaksi = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Riwayat Transaksi</h1>
</div>

<!-- Form Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="masuk" <?= $filter_jenis === 'masuk' ? 'selected' : '' ?>>Masuk</option>
                    <option value="keluar" <?= $filter_jenis === 'keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $filter_dari ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $filter_sampai ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-body">
        <table id="tabelTransaksi" class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($data_transaksi as $t): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                        <td><strong><?= htmlspecialchars($t['nama_barang']) ?></strong></td>
                        <td>
                            <span class="badge <?= $t['jenis_transaksi'] === 'masuk' ? 'bg-success' : 'bg-danger' ?>">
                                <?= ucfirst($t['jenis_transaksi']) ?>
                            </span>
                        </td>
                        <td class="<?= $t['jenis_transaksi'] === 'masuk' ? 'text-success' : 'text-danger' ?> fw-bold">
                            <?= $t['jenis_transaksi'] === 'masuk' ? '+' : '-' ?><?= $t['jumlah'] ?>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($t['keterangan'] ?: '-') ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- DataTables - HARUS SETELAH footer karena jQuery dimuat di footer -->
<script>
    $(document).ready(function() {
        $('#tabelTransaksi').DataTable({
            pageLength: 10, // Tampilkan 10 data per halaman
            lengthMenu: [5, 10, 25], // Opsi jumlah data per halaman
            order: [
                [1, 'desc']
            ] // Urutkan berdasarkan kolom tanggal, descending
        });
    });
</script>