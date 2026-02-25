        </main>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        
        <!-- Bootstrap 5 JS -->
        <script src="<?= $base_url ?>/app/assets/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables JS -->
        <script src="<?= $base_url ?>/app/assets/js/datatables.js"></script>
        <script src="<?= $base_url ?>/app/assets/js/datatables.min.js"></script>

        <!-- SweetAlert2 JS -->
        <script src="<?= $base_url ?>/app/assets/js/sweetalert2.all.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Custom JS -->
        <script src="<?= $base_url ?>/app/assets/js/script.js"></script>

        <!-- Flash Message -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <script>
                Swal.fire({
                    icon: '<?= $_SESSION['flash_type'] ?? 'success' ?>',
                    title: '<?= $_SESSION['flash_type'] === 'error' ? 'Oops!' : 'Berhasil!' ?>',
                    text: '<?= $_SESSION['flash_message'] ?>',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        <?php
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        endif;
        ?>
        </body>

        </html>