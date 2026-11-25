<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$profile = ['id' => '', 'code' => '', 'name' => '', 'description' => ''];

if ($id) {
    $profile = db_one(
        "SELECT id, code, name, description FROM profiles WHERE id = $1 AND deleted_at IS NULL",
        [$id]
    );
    if (!$profile) {
        flash('danger', 'El perfil no existe.');
        redirect('modules/profiles/index.php');
    }
}

$page_title  = $id ? 'Editar perfil' : 'Nuevo perfil';
$active_menu = 'profiles';
require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4" style="max-width:640px">
    <div class="card-body">
        <form action="save.php" method="post">
            <input type="hidden" name="id" value="<?= e($profile['id']) ?>">
            <div class="form-group">
                <label>Código *</label>
                <input name="code" class="form-control text-uppercase" maxlength="20" required
                       value="<?= e($profile['code']) ?>" placeholder="ADMIN, USER, VENDEDOR...">
            </div>
            <div class="form-group">
                <label>Nombre *</label>
                <input name="name" class="form-control" maxlength="80" required value="<?= e($profile['name']) ?>">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" class="form-control" rows="3" maxlength="255"><?= e($profile['description']) ?></textarea>
            </div>
            <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
