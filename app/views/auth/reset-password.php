<?php /* app/views/auth/reset-password.php */ ?>
<div class="auth-page">
<div class="auth-card animate-in">
    <div class="auth-logo">
        <div class="auth-logo-icon"><i class="bi bi-key"></i></div>
        <h1 class="auth-title">Nueva Contraseña</h1>
        <p class="auth-subtitle">Elige una contraseña segura de al menos 8 caracteres</p>
    </div>

    <?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> mb-3">
        <?= $flash['message'] ?>
    </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>auth/doReset" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required minlength="8">
        </div>
        <div class="mb-4">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="confirm" class="form-control" placeholder="Repite la contraseña" required minlength="8">
        </div>
        <button type="submit" class="btn btn-accent w-100 py-2">
            <i class="bi bi-check-circle me-2"></i>Guardar nueva contraseña
        </button>
    </form>
</div>
</div>
