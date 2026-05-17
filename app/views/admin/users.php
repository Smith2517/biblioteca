<?php /* app/views/admin/users.php */ ?>
<div class="page-header">
    <div>
        <h1 class="page-title">Usuarios <small>Gestión y permisos</small></h1>
    </div>
    <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalUser">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table id="tblUsers" class="table table-hover w-100">
            <thead><tr>
                <th>#</th><th>Usuario</th><th>Email</th><th>Rol</th>
                <th>Lectura</th><th>Descarga</th><th>Estado</th><th>Acciones</th>
            </tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id_user'] ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= COVERS_URL . htmlspecialchars($u['photo']) ?>"
                             class="rounded-circle" width="32" height="32" style="object-fit:cover"
                             onerror="this.src='<?= BASE_URL ?>public/img/default.png'">
                        <div>
                            <div class="fw-500"><?= htmlspecialchars($u['names'] . ' ' . $u['surnames']) ?></div>
                        </div>
                    </div>
                </td>
                <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge-role badge-<?= $u['role_slug'] ?>"><?= $u['role_name'] ?></span></td>
                <!-- Permisos con toggle visual -->
                <td>
                    <button class="perm-toggle <?= $u['can_read'] ? 'active' : '' ?>"
                            onclick="togglePerm(<?= $u['id_user'] ?>, 'read', this)"
                            title="<?= $u['can_read'] ? 'Quitar permiso de lectura' : 'Dar permiso de lectura' ?>">
                        <i class="bi bi-book"></i>
                        <?= $u['can_read'] ? 'Sí' : 'No' ?>
                    </button>
                </td>
                <td>
                    <button class="perm-toggle <?= $u['can_download'] ? 'active' : '' ?>"
                            onclick="togglePerm(<?= $u['id_user'] ?>, 'download', this)"
                            title="<?= $u['can_download'] ? 'Quitar permiso de descarga' : 'Dar permiso de descarga' ?>">
                        <i class="bi bi-download"></i>
                        <?= $u['can_download'] ? 'Sí' : 'No' ?>
                    </button>
                </td>
                <td>
                    <span class="badge <?= $u['status'] ? 'bg-success' : 'bg-danger' ?>">
                        <?= $u['status'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-secondary" onclick="editUser(<?= $u['id_user'] ?>)" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(<?= $u['id_user'] ?>)" title="Activar/Inactivar">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                        <?php if ($u['id_user'] != $_SESSION['user_id']): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $u['id_user'] ?>)" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUser" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content bg-card border-custom">
    <div class="modal-header border-custom">
        <h5 class="modal-title fw-600" id="modalUserTitle">Nuevo Usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>
    <form id="formUser" enctype="multipart/form-data">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= Session::generateCsrf() ?>">
            <input type="hidden" name="id_user" id="userId" value="0">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombres *</label>
                    <input type="text" name="names" id="uNames" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellidos *</label>
                    <input type="text" name="surnames" id="uSurnames" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" id="uEmail" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contraseña <span id="pwdHint" class="text-muted small">(obligatoria)</span></label>
                    <input type="password" name="password" id="uPassword" class="form-control" placeholder="••••••••">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rol *</label>
                    <select name="role_id" id="uRole" class="form-select">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id_role'] ?>"><?= $r['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end pb-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="uStatus" value="1" checked>
                        <label class="form-check-label" for="uStatus">Usuario activo</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Foto de perfil</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <!-- Permisos -->
                <div class="col-12">
                    <div class="card bg-secondary bg-opacity-10 border-custom p-3">
                        <div class="fw-600 mb-2"><i class="bi bi-shield-check me-2 text-accent"></i>Permisos de acceso</div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_read" id="uCanRead" value="1" checked>
                                    <label class="form-check-label" for="uCanRead">
                                        <i class="bi bi-book me-1"></i>Puede leer PDFs online
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="can_download" id="uCanDownload" value="1">
                                    <label class="form-check-label" for="uCanDownload">
                                        <i class="bi bi-download me-1"></i>Puede descargar PDFs
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-custom">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent"><i class="bi bi-save me-1"></i>Guardar</button>
        </div>
    </form>
</div>
</div>
</div>

<script src="<?= BASE_URL ?>public/js/users.js"></script>
