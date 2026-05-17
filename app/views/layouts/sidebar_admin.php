<!-- Sidebar Admin -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">

        <!-- Perfil compacto -->
        <div class="sidebar-profile">
            <img src="<?= BASE_URL ?>public/uploads/covers/<?= htmlspecialchars($_SESSION['user_photo'] ?? 'default.png') ?>"
                 alt="avatar" class="sidebar-avatar">
            <div class="sidebar-profile-info">
                <div class="fw-600"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                <span class="badge-role badge-admin">Administrador</span>
            </div>
        </div>

        <!-- Menú -->
        <nav class="sidebar-nav">
            <div class="sidebar-label">Principal</div>

            <a href="<?= BASE_URL ?>admin/dashboard" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') || str_contains($_SERVER['REQUEST_URI'], '/admin') && !str_contains($_SERVER['REQUEST_URI'], '/users') && !str_contains($_SERVER['REQUEST_URI'], '/books') && !str_contains($_SERVER['REQUEST_URI'], '/categories') && !str_contains($_SERVER['REQUEST_URI'], '/authors') && !str_contains($_SERVER['REQUEST_URI'], '/editorials') && !str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-label">Contenido</div>

            <a href="<?= BASE_URL ?>admin/books" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/books') ? 'active' : '' ?>">
                <i class="bi bi-book-fill"></i>
                <span>Libros</span>
            </a>

            <a href="<?= BASE_URL ?>admin/categories" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/categories') ? 'active' : '' ?>">
                <i class="bi bi-tags-fill"></i>
                <span>Categorías</span>
            </a>

            <a href="<?= BASE_URL ?>admin/authors" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/authors') ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill"></i>
                <span>Autores</span>
            </a>

            <a href="<?= BASE_URL ?>admin/editorials" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/editorials') ? 'active' : '' ?>">
                <i class="bi bi-building-fill"></i>
                <span>Editoriales</span>
            </a>

            <div class="sidebar-label">Gestión</div>

            <a href="<?= BASE_URL ?>admin/users" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/users') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i>
                <span>Usuarios</span>
            </a>

            <a href="<?= BASE_URL ?>admin/reports" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Reportes</span>
            </a>

            <div class="sidebar-label">Sistema</div>

            <a href="<?= BASE_URL ?>auth/logout" class="sidebar-link text-danger-soft">
                <i class="bi bi-box-arrow-left"></i>
                <span>Cerrar Sesión</span>
            </a>
        </nav>
    </div>
</aside>
