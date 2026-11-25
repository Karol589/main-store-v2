<?php
/**
 * Asignación de menús a un perfil (gestión de permisos).
 * GET  => muestra checkboxes
 * POST => guarda la selección dentro de una transacción
 */
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
   ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$profile = db_one("SELECT id, code, name FROM profiles WHERE id = $1 AND deleted_at IS NULL", [$id]);
if (!$profile) {
    flash('danger', 'El perfil no existe.');
    redirect('modules/profiles/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = array_map('intval', $_POST['menus'] ?? []);

    db_exec('BEGIN');
    try {
        // 1. Desactiva todos los permisos actuales del perfil
        db_exec(
            "UPDATE profile_menu SET deleted_at = NOW()
              WHERE profile_id = $1 AND deleted_at IS NULL",
            [$id]
        );
        // 2. Activa (o crea) solo los seleccionados
        foreach ($selected as $menuId) {
            db_exec(
                "INSERT INTO profile_menu (profile_id, menu_id) VALUES ($1, $2)
                 ON CONFLICT (profile_id, menu_id)
                 DO UPDATE SET deleted_at = NULL",
                [$id, $menuId]
            );
        }
        db_exec('COMMIT');
        flash('success', 'Menús del perfil ' . $profile['name'] . ' actualizados.');
    } catch (RuntimeException $ex) {
        db_exec('ROLLBACK');
        flash('danger', 'Error al guardar los permisos.');
    }
    redirect('modules/profiles/index.php');
}

// Todos los menús + marca si el perfil lo tiene asignado
$menus = db_all(
    "SELECT m.id, m.name, m.icon, m.url,
            (pm.id IS NOT NULL) AS assigned
       FROM menus m
       LEFT JOIN profile_menu pm
              ON pm.menu_id = m.id AND pm.profile_id = $1 AND pm.deleted_at IS NULL
      WHERE m.deleted_at IS NULL
      ORDER BY m.sort_order",
    [$id]
);

$page_title  = 'Menús del perfil: ' . $profile['name'];
$active_menu = 'profiles';
require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4" style="max-width:720px">
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="id" value="<?= $profile['id'] ?>">
            <ul class="list-group mb-3">
                <?php foreach ($menus as $m): ?>
                    <li class="list-group-item">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="menus[]"
                                   id="m<?= $m['id'] ?>" value="<?= $m['id'] ?>"
                                   <?= pg_bool($m['assigned']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="m<?= $m['id'] ?>">
                                <i class="<?= e($m['icon']) ?> fa-fw text-primary"></i>
                                <?= e($m['name']) ?>
                                <small class="text-muted ml-2"><?= e($m['url']) ?></small>
                            </label>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button class="btn btn-success"><i class="fas fa-save"></i> Guardar permisos</button>
            <a href="index.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
