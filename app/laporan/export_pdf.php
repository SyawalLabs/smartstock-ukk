<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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
   TOP 5 BARANG
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
   HTML CONTENT
================================= */
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #3b82f6;
            color: white;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary-box {
            border: 1px solid #3b82f6;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <h1>LAPORAN TRANSAKSI GUDANG</h1>
    <div class="subtitle">
        Periode: <?= date('d/m/Y', strtotime($dari)) ?> - <?= date('d/m/Y', strtotime($sampai)) ?><br>
        Dicetak oleh: <?= $user ?> | <?= date('d/m/Y') ?>
    </div>

    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        Total Masuk: <?= number_format($total_masuk) ?> unit<br>
        Total Keluar: <?= number_format($total_keluar) ?> unit<br>
        Nilai Masuk: Rp <?= number_format($nilai_masuk, 0, ',', '.') ?><br>
        Nilai Keluar: Rp <?= number_format($nilai_keluar, 0, ',', '.') ?><br>
        Selisih Stok: <?= number_format($total_masuk - $total_keluar) ?> unit
    </div>

    <h3>Top 5 Barang Paling Banyak Keluar</h3>
    <table>
        <tr>
            <th width="10%">No</th>
            <th>Nama Barang</th>
            <th width="20%">Total Keluar</th>
        </tr>
        <?php if (empty($top_barang)): ?>
            <tr>
                <td colspan="3" class="text-center">Tidak ada data</td>
            </tr>
        <?php else: ?>
            <?php $no = 1;
            foreach ($top_barang as $b): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                    <td class="text-center"><?= number_format($b['total_keluar']) ?> unit</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <h3>Detail Transaksi</h3>
    <table>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Tanggal</th>
            <th>Nama Barang</th>
            <th width="12%">Jenis</th>
            <th width="10%">Jumlah</th>
            <th width="15%">Harga</th>
            <th width="15%">Total</th>
        </tr>

        <?php $no = 1;
        foreach ($data as $t):
            $total = $t['jumlah'] * $t['harga'];
        ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                <td><?= htmlspecialchars($t['nama_barang']) ?></td>
                <td class="text-center"><?= ucfirst($t['jenis_transaksi']) ?></td>
                <td class="text-center"><?= $t['jumlah'] ?></td>
                <td class="text-right">Rp <?= number_format($t['harga'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

</body>

</html>

<?php
$html = ob_get_clean();

/* ===============================
   GENERATE PDF
================================= */
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("Laporan_Transaksi_" . date('Ymd_His') . ".pdf", [
    "Attachment" => true
]);

exit;
