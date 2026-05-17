<!-- Navbar Top -->
<nav class="navbar navbar-dark bg-navbar px-3 sticky-top shadow-sm" id="topNavbar">
    <div class="d-flex align-items-center gap-3">
        <!-- Toggle Sidebar -->
        <button class="btn btn-icon" id="sidebarToggle" title="Menú">
            <i class="bi bi-list fs-4"></i>
        </button>
        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="navbar-brand d-flex align-items-center gap-2 mb-0">
            <div class="logo-icon">
                <i class="bi bi-book-half"></i>
            </div>
            <span class="fw-700"><?= APP_NAME ?></span>
        </a>
    </div>

    <!-- Buscador global -->
    <div class="search-bar d-none d-md-flex align-items-center">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="globalSearch" class="form-control border-start-0"
                   placeholder="Buscar libros, autores..." autocomplete="off">
        </div>
    </div>

    <!-- Usuario -->
    <div class="d-flex align-items-center gap-3">
        <!-- Modo oscuro toggle -->
        <button class="btn btn-icon" id="themeToggle" title="Cambiar tema">
            <i class="bi bi-moon-stars-fill"></i>
        </button>

        <!-- Notificaciones (placeholder) -->
        <button class="btn btn-icon position-relative" title="Notificaciones">
            <i class="bi bi-bell fs-5"></i>
        </button>

        <!-- Dropdown usuario -->
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 user-btn" data-bs-toggle="dropdown">
                <img src="<?= BASE_URL ?>public/uploads/covers/<?= htmlspecialchars($_SESSION['user_photo'] ?? 'default.png') ?>"
                     alt="avatar" class="avatar-sm rounded-circle">
                <div class="text-start d-none d-lg-block">
                    <div class="fw-600 small"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                    <div class="text-muted x-small"><?= ucfirst($_SESSION['user_role'] ?? '') ?></div>
                </div>
                <i class="bi bi-chevron-down x-small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <?php $role = $_SESSION['user_role'] ?? ''; ?>
                <li>
                    <a class="dropdown-item" href="<?= BASE_URL . $role ?>/profile">
                        <i class="bi bi-person me-2"></i>Mi Perfil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
