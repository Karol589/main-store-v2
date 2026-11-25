<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$user = ['id' => '', 'name' => '', 'email' => '', 'profile_id' => ''];

if ($id) {
    $user = db_one("SELECT id, name, email, profile_id FROM users WHERE id = $1 AND deleted_at IS NULL", [$id]);
    if (!$user) {
        flash('danger', 'El usuario no existe.');
        redirect('modules/users/index.php');
    }
}

// NUEVO en v2.0: lista de perfiles para el select
$profiles = db_all("SELECT id, name FROM profiles WHERE deleted_at IS NULL ORDER BY name");

$page_title  = $id ? 'Editar usuario' : 'Nuevo usuario';
$active_menu = 'users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4" style="max-width:640px">
    <div class="card-body">
        <form action="save.php" method="post">
            <input type="hidden" name="id" value="<?= e($user['id']) ?>">
            <div class="form-group">
                <label>Nombre *</label>
                <input name="name" class="form-control" required value="<?= e($user['name']) ?>">
            </div>
            <div class="form-group">
                <label>Correo *</label>
                <input type="email" name="email" class="form-control" required value="<?= e($user['email']) ?>">
            </div>
            <div class="form-group">
                <label>Perfil *</label>
                <select name="profile_id" class="form-control" required>
                    <?php foreach ($profiles as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= $pr['id'] == $user['profile_id'] ? 'selected' : '' ?>>
                            <?= e($pr['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Contraseña <?= $id ? '(dejar vacía para no cambiarla)' : '*' ?></label>
                <input type="password" name="password" class="form-control" minlength="6" <?= $id ? '' : 'required' ?>>
            </div>
            <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
