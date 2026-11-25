<?php
require_once __DIR__ . '/../../includes/auth.php';
require_access('profiles');

$page_title  = 'Perfiles';
$active_menu = 'profiles';

$profiles = db_all(
    "SELECT p.id, p.code, p.name, p.description,
            (SELECT COUNT(*) FROM users u
              WHERE u.profile_id = p.id AND u.deleted_at IS NULL)        AS total_users,
            (SELECT COUNT(*) FROM profile_menu pm
              WHERE pm.profile_id = p.id AND pm.deleted_at IS NULL)      AS total_menus
       FROM profiles p
      WHERE p.deleted_at IS NULL
      ORDER BY p.id"
);

require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Listado de perfiles</h6>
        <a href="form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo perfil</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr><th>Código</th><th>Nombre</th><th>Descripción</th>
                <th class="text-center">Usuarios</th><th class="text-center">Menús</th>
                <th class="text-center" style="width:190px">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($profiles as $p): ?>
                <tr>
                    <td><span class="badge badge-secondary"><?= e($p['code']) ?></span></td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['description']) ?></td>
                    <td class="text-center"><?= e($p['total_users']) ?></td>
                    <td class="text-center"><?= e($p['total_menus']) ?></td>
                    <td class="text-center">
                        <a href="menus.php?id=<?= $p['id'] ?>" class="btn btn-info btn-sm" title="Asignar menús">
                            <i class="fas fa-bars"></i></a>
                        <a href="form.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-edit"></i></a>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('¿Eliminar el perfil <?= e($p['name']) ?>?')">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
