<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$store = ['id' => '', 'branch_id' => '', 'name' => '', 'description' => '',
          'email' => '', 'phone' => '', 'is_active' => 't'];

if ($id) {
    $store = db_one(
        "SELECT id, branch_id, name, description, email, phone, is_active
           FROM stores WHERE id = $1 AND deleted_at IS NULL",
        [$id]
    );
    if (!$store) {
        flash('danger', 'La tienda no existe.');
        redirect('modules/stores/index.php');
    }
}

// Sucursales con su ciudad para que el admin identifique cuál elegir
$branches = db_all(
    "SELECT b.id, b.name, ci.name AS city
       FROM branches b JOIN cities ci ON ci.id = b.city_id
      WHERE b.deleted_at IS NULL
      ORDER BY b.name"
);

$page_title  = $id ? 'Editar tienda' : 'Nueva tienda';
$active_menu = 'stores';
require __DIR__ . '/../../includes/header.php';
?>
<?php if (!$branches): ?>
    <div class="alert alert-warning">Primero debe crear una <a href="../branches/form.php">sucursal</a>.</div>
<?php endif; ?>
<div class="card shadow mb-4" style="max-width:720px">
    <div class="card-body">
        <form action="save.php" method="post">
            <input type="hidden" name="id" value="<?= e($store['id']) ?>">
            <div class="form-group">
                <label>Sucursal *</label>
                <select name="branch_id" class="form-control" required>
                    <option value="">Seleccione la sucursal</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $b['id'] == $store['branch_id'] ? 'selected' : '' ?>>
                            <?= e($b['name']) ?> — <?= e($b['city']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nombre de la tienda *</label>
                <input name="name" class="form-control" maxlength="120" required value="<?= e($store['name']) ?>">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input name="description" class="form-control" maxlength="255" value="<?= e($store['description']) ?>">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Correo</label>
                    <input type="email" name="email" class="form-control" maxlength="120" value="<?= e($store['email']) ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Teléfono</label>
                    <input name="phone" class="form-control" maxlength="30" value="<?= e($store['phone']) ?>">
                </div>
            </div>
            <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                       <?= pg_bool($store['is_active']) ? 'checked' : '' ?>>
                <label class="custom-control-label" for="is_active">Tienda activa</label>
            </div>
            <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
