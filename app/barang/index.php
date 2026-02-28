<?php

/**
 * Data Barang - List dengan Search, Pagination, dan Export
 */

$page_title = 'Data Barang';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

$stmt = $pdo->query("SELECT * FROM barang ORDER BY id DESC");
$data_barang = $stmt->fetchAll();

// Hitung total nilai
$total_nilai = 0;
foreach ($data_barang as $b) {
    $total_nilai += $b['stok'] * $b['harga'];
}
?>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Data Barang</h1>
        <p>Kelola semua data barang di gudang</p>
    </div>
    <div class="d-flex gap-2">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Tambah Barang
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-tags fs-4"></i>
            </div>
            <div class="stat-value"><?= count($data_barang) ?></div>
            <div class="stat-label">Jenis Barang</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-boxes fs-4"></i>
            </div>
            <div class="stat-value"><?= number_format(array_sum(array_column($data_barang, 'stok'))) ?></div>
            <div class="stat-label">Total Stok</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-currency-dollar fs-4"></i>
            </div>
            <div class="stat-value" style="font-size: 18px;">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
            <div class="stat-label">Nilai Inventaris</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelBarang" class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Barang</th>
                        <th width="12%">Stok</th>
                        <th width="18%">Harga</th>
                        <th width="15%">Terakhir Update</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($data_barang as $barang): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($barang['nama_barang']) ?></strong></td>
                            <td>
                                <?php
                                $stok = $barang['stok'];
                                if ($stok > 10) {
                                    $badge = 'success';
                                } elseif ($stok > 0) {
                                    $badge = 'warning';
                                } else {
                                    $badge = 'danger';
                                }
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= $stok ?></span>
                            </td>
                            <td>Rp <?= number_format($barang['harga'], 0, ',', '.') ?></td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($barang['updated_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="edit.php?id=<?= $barang['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button onclick="hapusBarang(<?= $barang['id'] ?>, '<?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES) ?>')"
                                        class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- DataTables Init -->
<script>
    $(document).ready(function() {
        $('#tabelBarang').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "<strong> < </strong>",
                    next: "<strong> > </strong>"
                }
            },

            pagingType: "simple_numbers", // hanya angka + prev/next (tapi kita kosongkan)

            order: [
                [0, 'asc']
            ],
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25],
                [5, 10, 25]
            ],
            searching: true,
            paging: true,
            info: true
        });
    });

    function hapusBarang(id, nama) {
        Swal.fire({
            title: 'Hapus Barang?',
            html: `Yakin ingin menghapus <strong>${nama}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus.php?id=' + id;
            }
        });
    }
</script>