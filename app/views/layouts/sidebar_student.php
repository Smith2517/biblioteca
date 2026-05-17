<?php
/**
 * Sidebar unificado para Docente y Estudiante.
 * La variable $layout define qué opciones mostrar.
 */
$role     = $_SESSION['user_role'] ?? 'student';
$roleBase = $role; // 'teacher' o 'student'
$uri      = $_SERVER['REQUEST_URI'];

function sidebarIsActive(string $uri, string $path): string {
    return str_contains($uri, $path) ? 'active' : '';
}
?>
<!-- Sidebar Usuario (Docente / Estudiante) -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">

        <!-- Perfil compacto -->
        <div class="sidebar-profile">
            <img src="<?= BASE_URL ?>public/uploads/covers/<?= htmlspecialchars($_SESSION['user_photo'] ?? 'default.png') ?>"
                 alt="avatar" class="sidebar-avatar">
            <div class="sidebar-profile-info">
                <div class="fw-600"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                <span class="badge-role <?= $role === 'teacher' ? 'badge-teacher' : 'badge-student' ?>">
                    <?= $role === 'teacher' ? 'Docente' : 'Estudiante' ?>
                </span>
            </div>
        </div>

        <!-- Menú -->
        <nav class="sidebar-nav">
            <div class="sidebar-label">Principal</div>

            <a href="<?= BASE_URL . $roleBase ?>/dashboard" class="sidebar-link <?= sidebarIsActive($uri, "/{$roleBase}/dashboard") ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Inicio</span>
            </a>

            <a href="<?= BASE_URL . $roleBase ?>/catalog" class="sidebar-link <?= sidebarIsActive($uri, "/{$roleBase}/catalog") ?>">
                <i class="bi bi-collection-fill"></i>
                <span>Catálogo</span>
            </a>

            <div class="sidebar-label">Mi Biblioteca</div>

            <a href="<?= BASE_URL . $roleBase ?>/favorites" class="sidebar-link <?= sidebarIsActive($uri, "/{$roleBase}/favorites") ?>">
                <i class="bi bi-heart-fill"></i>
                <span>Favoritos</span>
            </a>

            <a href="<?= BASE_URL . $roleBase ?>/history" class="sidebar-link <?= sidebarIsActive($uri, "/{$roleBase}/history") ?>">
                <i class="bi bi-clock-history"></i>
                <span>Historial</span>
            </a>

            <div class="sidebar-label">Mi Cuenta</div>

            <a href="<?= BASE_URL . $roleBase ?>/profile" class="sidebar-link <?= sidebarIsActive($uri, "/{$roleBase}/profile") ?>">
                <i class="bi bi-person-circle"></i>
                <span>Mi Perfil</span>
            </a>

            <a href="<?= BASE_URL ?>auth/logout" class="sidebar-link text-danger-soft">
                <i class="bi bi-box-arrow-left"></i>
                <span>Cerrar Sesión</span>
            </a>
        </nav>

        <!-- Indicador de permisos -->
        <div class="sidebar-permissions">
            <div class="perm-title">Mis permisos</div>
            <div class="perm-item <?= $_SESSION['can_read']     ? 'perm-ok' : 'perm-no' ?>">
                <i class="bi bi-<?= $_SESSION['can_read']     ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
                Lectura online
            </div>
            <div class="perm-item <?= $_SESSION['can_download'] ? 'perm-ok' : 'perm-no' ?>">
                <i class="bi bi-<?= $_SESSION['can_download'] ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
                Descargas
            </div>
        </div>
    </div>
</aside>
