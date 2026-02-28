<?php

/**
 * Halaman Laporan
 */

$page_title = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

$filter_dari = $_GET['dari'] ?? date('Y-m-01');
$filter_sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT t.*, b.nama_barang, b.harga FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ? ORDER BY t.tanggal_transaksi DESC");
$stmt->execute([$filter_dari, $filter_sampai]);
$data_transaksi = $stmt->fetchAll();

$total_masuk = 0;
$total_keluar = 0;
$nilai_masuk = 0;
$nilai_keluar = 0;

foreach ($data_transaksi as $t) {
    if ($t['jenis_transaksi'] === 'masuk') {
        $total_masuk += $t['jumlah'];
        $nilai_masuk += $t['jumlah'] * $t['harga'];
    } else {
        $total_keluar += $t['jumlah'];
        $nilai_keluar += $t['jumlah'] * $t['harga'];
    }
}

$stmt = $pdo->prepare("SELECT b.nama_barang, SUM(t.jumlah) as total_keluar FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE t.jenis_transaksi = 'keluar' AND DATE(t.tanggal_transaksi) BETWEEN ? AND ? GROUP BY t.id_barang ORDER BY total_keluar DESC LIMIT 5");
$stmt->execute([$filter_dari, $filter_sampai]);
$barang_terlaris = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Laporan</h1>
    <p>Ringkasan transaksi dan statistik gudang</p>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $filter_dari ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $filter_sampai ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-box-arrow-up fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($total_masuk) ?></div>
            <div class="stat-label">Total Masuk</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="bi bi-box-arrow-down fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($total_keluar) ?></div>
            <div class="stat-label">Total Keluar</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-currency-dollar fs-4"></i>
            </div>
            <div class="stat-value" style="font-size: 18px;">Rp <?= number_format($nilai_masuk, 0, ',', '.') ?></div>
            <div class="stat-label">Nilai Masuk</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-currency-dollar fs-4"></i>
            </div>
            <div class="stat-value" style="font-size: 18px;">Rp <?= number_format($nilai_keluar, 0, ',', '.') ?></div>
            <div class="stat-label">Nilai Keluar</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Export -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Export Laporan</div>
            <div class="card-body">
                <p class="text-muted">Periode:</p>
                <p class="fw-bold mb-4"><?= date('d/m/Y', strtotime($filter_dari)) ?> - <?= date('d/m/Y', strtotime($filter_sampai)) ?></p>

                <div class="d-grid gap-2">
                    <a href="export_pdf.php?dari=<?= $filter_dari ?>&sampai=<?= $filter_sampai ?>" class="btn btn-danger" target="_blank">
                        <i class="bi bi-file-pdf me-2"></i>
                        Download PDF
                    </a>
                    <a href="export_excel.php?dari=<?= $filter_dari ?>&sampai=<?= $filter_sampai ?>" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Items -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">Barang Paling Banyak Keluar</div>
            <div class="card-body">
                <?php if (empty($barang_terlaris)): ?>
                    <p class="text-muted text-center py-4">Belum ada data</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Total Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1;
                            foreach ($barang_terlaris as $b): ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                                    <td><span class="badge bg-danger"><?= number_format($b['total_keluar']) ?> unit</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>