<?php /* app/views/teacher/profile.php */ ?>
<div class="page-header">
    <h1 class="page-title">Mi Perfil</h1>
</div>
<div class="row g-4 justify-content-center">
    <div class="col-md-5">
        <div class="card text-center p-4">
            <img src="<?= BASE_URL ?>public/uploads/covers/<?= htmlspecialchars($user['photo']) ?>"
                 class="rounded-circle mx-auto mb-3" width="100" height="100" style="object-fit:cover;border:3px solid var(--accent)"
                 onerror="this.src='<?= BASE_URL ?>public/img/default.png'">
            <h5 class="fw-700"><?= htmlspecialchars($user['names'] . ' ' . $user['surnames']) ?></h5>
            <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
            <span class="badge-role badge-<?= $user['role_slug'] ?> mx-auto"><?= $user['role_name'] ?></span>

            <hr class="my-3" style="border-color:var(--border)">

            <div class="d-flex justify-content-around text-center">
                <div>
                    <div class="fw-700 text-accent fs-5"><?= $_SESSION['can_read'] ? '✓' : '✗' ?></div>
                    <div class="text-muted x-small">Lectura</div>
                </div>
                <div>
                    <div class="fw-700 text-accent fs-5"><?= $_SESSION['can_download'] ? '✓' : '✗' ?></div>
                    <div class="text-muted x-small">Descarga</div>
                </div>
                <div>
                    <div class="fw-700 text-accent fs-5"><?= date('Y', strtotime($user['created_at'])) ?></div>
                    <div class="text-muted x-small">Desde</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card p-4">
            <h6 class="fw-600 mb-3"><i class="bi bi-info-circle me-2 text-accent"></i>Información de cuenta</h6>
            <dl class="row small">
                <dt class="col-sm-4 text-muted">Nombre completo</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($user['names'] . ' ' . $user['surnames']) ?></dd>
                <dt class="col-sm-4 text-muted">Correo electrónico</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($user['email']) ?></dd>
                <dt class="col-sm-4 text-muted">Rol</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($user['role_name']) ?></dd>
                <dt class="col-sm-4 text-muted">Miembro desde</dt>
                <dd class="col-sm-8"><?= date('d/m/Y', strtotime($user['created_at'])) ?></dd>
                <dt class="col-sm-4 text-muted">Permiso de lectura</dt>
                <dd class="col-sm-8"><span class="badge <?= $user['can_read'] ? 'bg-success' : 'bg-secondary' ?>"><?= $user['can_read'] ? 'Habilitado' : 'Deshabilitado' ?></span></dd>
                <dt class="col-sm-4 text-muted">Permiso de descarga</dt>
                <dd class="col-sm-8"><span class="badge <?= $user['can_download'] ? 'bg-success' : 'bg-secondary' ?>"><?= $user['can_download'] ? 'Habilitado' : 'Deshabilitado' ?></span></dd>
            </dl>
            <p class="text-muted small mt-2">Para cambiar tu información, contacta al administrador del sistema.</p>
        </div>
    </div>
</div>
