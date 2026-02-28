<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user = $_SESSION['nama'] ?? 'Administrator';

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

/* ===============================
   AMBIL DATA TRANSAKSI
================================= */
$stmt = $pdo->prepare("
    SELECT t.*, b.nama_barang, b.harga 
    FROM transaksi t 
    JOIN barang b ON t.id_barang = b.id 
    WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ?
    ORDER BY t.tanggal_transaksi DESC
");
$stmt->execute([$dari, $sampai]);
$data = $stmt->fetchAll();

/* ===============================
   HITUNG STATISTIK
================================= */
$total_masuk = 0;
$total_keluar = 0;
$nilai_masuk = 0;
$nilai_keluar = 0;

foreach ($data as $t) {
    if ($t['jenis_transaksi'] === 'masuk') {
        $total_masuk += $t['jumlah'];
        $nilai_masuk += $t['jumlah'] * $t['harga'];
    } else {
        $total_keluar += $t['jumlah'];
        $nilai_keluar += $t['jumlah'] * $t['harga'];
    }
}

/* ===============================
   TOP 5 BARANG TERLARIS
================================= */
$stmt = $pdo->prepare("
    SELECT b.nama_barang, SUM(t.jumlah) as total_keluar 
    FROM transaksi t 
    JOIN barang b ON t.id_barang = b.id 
    WHERE t.jenis_transaksi = 'keluar'
    AND DATE(t.tanggal_transaksi) BETWEEN ? AND ?
    GROUP BY t.id_barang
    ORDER BY total_keluar DESC
    LIMIT 5
");
$stmt->execute([$dari, $sampai]);
$top_barang = $stmt->fetchAll();

/* ===============================
   EXPORT HEADER
================================= */
$filename = "laporan_" . date('Ymd') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table border="0">
    <tr>
        <td colspan="9" style="font-size:18px; font-weight:bold;">
            LAPORAN TRANSAKSI GUDANG
        </td>
    </tr>
    <tr>
        <td colspan="9">
            Periode: <?= date('d/m/Y', strtotime($dari)) ?> - <?= date('d/m/Y', strtotime($sampai)) ?>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            Dicetak oleh: <?= $user ?> | <?= date('d/m/Y') ?>
        </td>
    </tr>
</table>

<br>

<!-- STATISTIK -->
<table border="1">
    <tr style="background:#f2f2f2; font-weight:bold;">
        <th>Total Masuk</th>
        <th>Total Keluar</th>
        <th>Nilai Masuk</th>
        <th>Nilai Keluar</th>
        <th>Selisih Stok</th>
    </tr>
    <tr>
        <td><?= number_format($total_masuk) ?></td>
        <td><?= number_format($total_keluar) ?></td>
        <td>Rp <?= number_format($nilai_masuk,0,',','.') ?></td>
        <td>Rp <?= number_format($nilai_keluar,0,',','.') ?></td>
        <td><?= number_format($total_masuk - $total_keluar) ?></td>
    </tr>
</table>

<br>

<!-- TOP BARANG -->
<table border="1">
    <tr style="background:#e9ecef; font-weight:bold;">
        <th colspan="2">Top 5 Barang Paling Banyak Keluar</th>
    </tr>
    <?php if(empty($top_barang)): ?>
        <tr><td colspan="2">Tidak ada data</td></tr>
    <?php else: ?>
        <?php $rank=1; foreach($top_barang as $b): ?>
        <tr>
            <td><?= $rank++ ?></td>
            <td><?= $b['nama_barang'] ?> (<?= number_format($b['total_keluar']) ?> unit)</td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<br>

<!-- DETAIL TRANSAKSI -->
<table border="1">
    <thead>
        <tr style="background:#4e73df; color:white; font-weight:bold;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach($data as $t): 
            $total = $t['jumlah'] * $t['harga'];
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
            <td><?= $t['nama_barang'] ?></td>
            <td><?= ucfirst($t['jenis_transaksi']) ?></td>
            <td><?= $t['jumlah'] ?></td>
            <td>Rp <?= number_format($t['harga'],0,',','.') ?></td>
            <td>Rp <?= number_format($total,0,',','.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php exit; ?>