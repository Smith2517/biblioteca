<?php /* app/views/auth/login.php */ ?>
<div class="auth-page">
<div class="auth-card animate-in">

    <!-- Logo -->
    <div class="auth-logo">
        <div class="auth-logo-icon"><i class="bi bi-book-half"></i></div>
        <h1 class="auth-title"><?= APP_NAME ?></h1>
        <p class="auth-subtitle">Biblioteca Virtual Académica</p>
    </div>

    <!-- Flash -->
    <?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> mb-3">
        <?= $flash['message'] ?>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="<?= BASE_URL ?>auth/doLogin" method="POST" id="loginForm">
        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
        </div>

        <div class="mb-4">
            <label class="form-label d-flex justify-content-between">
                <span><i class="bi bi-lock me-1"></i>Contraseña</span>
                <a href="<?= BASE_URL ?>auth/forgot" class="text-accent small">¿Olvidaste tu contraseña?</a>
            </label>
            <div class="input-group">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-accent w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </button>
    </form>

    <p class="text-center text-muted small mt-4">
        © <?= date('Y') ?> <?= APP_NAME ?> — Sistema de Biblioteca Virtual
    </p>
</div>
</div>

<script>
function togglePassword() {
    const inp = document.getElementById('passwordInput');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
}
</script>
