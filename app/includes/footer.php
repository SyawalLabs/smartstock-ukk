        </main>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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