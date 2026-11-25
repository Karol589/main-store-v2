<?php
/**
 * Usuarios: se migra de la v1.0 y se le agrega la columna Perfil.
 */
require_once __DIR__ . '/../../includes/auth.php';
require_access('users');

$page_title  = 'Usuarios';
$active_menu = 'users';

$users = db_all(
    "SELECT u.id, u.name, u.email, p.code AS profile_code, p.name AS profile
       FROM users u
       JOIN profiles p ON p.id = u.profile_id
      WHERE u.deleted_at IS NULL
      ORDER BY u.name"
);

require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Listado de usuarios</h6>
        <a href="form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo usuario</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr><th>Nombre</th><th>Correo</th><th>Perfil</th><th class="text-center" style="width:130px">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge badge-<?= $u['profile_code'] === 'ADMIN' ? 'danger' : 'info' ?>">
                        <?= e($u['profile']) ?></span></td>
                    <td class="text-center">
                        <a href="form.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <?php if ($u['id'] != current_user()['id']): ?>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('¿Eliminar el usuario?')">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
