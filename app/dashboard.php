<?php

/**
 * Dashboard
 * Clean design with Heroicons
 */

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/koneksi.php';

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) as total FROM barang");
$total_barang = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(stok) as total FROM barang");
$total_stok = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
$transaksi_hari_ini = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM barang WHERE stok < 10");
$stok_rendah = $stmt->fetch()['total'];

// Chart data
$stmt = $pdo->query("
    SELECT 
        DATE(tanggal_transaksi) as tanggal,
        SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) as masuk,
        SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) as keluar
    FROM transaksi 
    WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal_transaksi)
    ORDER BY tanggal ASC
");
$chart_data = $stmt->fetchAll();

// Transaksi Terbaru
$stmt = $pdo->query("
    SELECT t.*, b.nama_barang 
    FROM transaksi t 
    JOIN barang b ON t.id_barang = b.id 
    ORDER BY t.tanggal_transaksi DESC 
    LIMIT 5
");
$transaksi_terbaru = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-boxes fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($total_barang) ?></div>
            <div class="stat-label">Total Barang</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-stack fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($total_stok) ?></div>
            <div class="stat-label">Total Stok</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-arrow-repeat fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($transaksi_hari_ini) ?></div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-exclamation-triangle fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format($stok_rendah) ?></div>
            <div class="stat-label">Stok Rendah</div>
        </div>
    </div>
</div>

<!-- Charts & Recent -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">Grafik Transaksi 7 Hari Terakhir</div>
            <div class="card-body">
                <canvas id="chartTransaksi" height="280"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Transaksi Terbaru</span>
                <a href="<?= $base_url ?>/app/transaksi/riwayat.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($transaksi_terbaru)): ?>
                        <div class="list-group-item text-center text-muted py-4">
                            Belum ada transaksi
                        </div>
                    <?php else: ?>
                        <?php foreach ($transaksi_terbaru as $t): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge <?= $t['jenis_transaksi'] === 'masuk' ? 'bg-success' : 'bg-danger' ?> me-2">
                                            <?= $t['jenis_transaksi'] === 'masuk' ? 'Masuk' : 'Keluar' ?>
                                        </span>
                                        <strong><?= htmlspecialchars($t['nama_barang']) ?></strong>
                                    </div>
                                    <span class="<?= $t['jenis_transaksi'] === 'masuk' ? 'text-success' : 'text-danger' ?> fw-bold">
                                        <?= $t['jenis_transaksi'] === 'masuk' ? '+' : '-' ?><?= $t['jumlah'] ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Aksi Cepat</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?= $base_url ?>/app/barang/tambah.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-square fs-5"></i>
                            Tambah Barang
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= $base_url ?>/app/transaksi/masuk.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-down-circle fs-5"></i>
                            Barang Masuk
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= $base_url ?>/app/transaksi/keluar.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-up-circle fs-5"></i>
                            Barang Keluar
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= $base_url ?>/app/laporan/" class="btn btn-outline-primary w-100">
                            <i class="bi bi-file-earmark-text fs-5"></i>
                            Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Simpan data chart untuk digunakan setelah footer loaded
$chart_json = json_encode($chart_data);
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Chart Script - HARUS setelah footer karena Chart.js dimuat di footer -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = <?= $chart_json ?>;

        // Jika tidak ada data, tampilkan pesan
        if (chartData.length === 0) {
            document.getElementById('chartTransaksi').parentElement.innerHTML =
                '<div class="text-center text-muted py-5">Belum ada data transaksi 7 hari terakhir</div>';
            return;
        }

        const labels = chartData.map(d => {
            const date = new Date(d.tanggal);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short'
            });
        });
        const masukData = chartData.map(d => parseInt(d.masuk));
        const keluarData = chartData.map(d => parseInt(d.keluar));

        const ctx = document.getElementById('chartTransaksi').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Barang Masuk',
                        data: masukData,
                        backgroundColor: '#22c55e',
                        borderRadius: 4
                    },
                    {
                        label: 'Barang Keluar',
                        data: keluarData,
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e2e8f0'
                        }
                    }
                }
            }
        });
    });
</script>