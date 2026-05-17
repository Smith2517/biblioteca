<?php /* app/views/auth/forgot-password.php */ ?>
<div class="auth-page">
<div class="auth-card animate-in">
    <div class="auth-logo">
        <div class="auth-logo-icon"><i class="bi bi-shield-lock"></i></div>
        <h1 class="auth-title">Recuperar Contraseña</h1>
        <p class="auth-subtitle">Te enviaremos un enlace de recuperación</p>
    </div>

    <?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> mb-3">
        <?= $flash['message'] ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>auth/doForgot" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
        <div class="mb-4">
            <label class="form-label"><i class="bi bi-envelope me-1"></i>Correo electrónico</label>
            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
        </div>
        <button type="submit" class="btn btn-accent w-100 py-2">
            <i class="bi bi-send me-2"></i>Enviar enlace
        </button>
    </form>
    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>auth/login" class="text-accent small">← Volver al inicio de sesión</a>
    </div>
</div>
</div>
