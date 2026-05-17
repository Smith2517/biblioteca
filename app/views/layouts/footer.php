    <!-- Scripts JS -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- App principal -->
    <script>
        // Inyectar BASE_URL para uso en JS
        const BASE_URL = '<?= BASE_URL ?>';
        const COVERS_URL = '<?= COVERS_URL ?>';
        const BOOKS_URL  = '<?= BOOKS_URL ?>';
        const USER_ROLE  = '<?= htmlspecialchars($_SESSION['user_role'] ?? '') ?>';
        const CSRF_TOKEN = '<?= Session::generateCsrf() ?>';
    </script>
    <script src="<?= BASE_URL ?>public/js/app.js"></script>

    <?php if (isset($extraJs)): ?>
        <?php foreach ((array)$extraJs as $js): ?>
            <script src="<?= BASE_URL ?>public/js/<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
