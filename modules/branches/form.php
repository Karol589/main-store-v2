<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$branch = ['id' => '', 'name' => '', 'address' => '', 'phone' => '',
           'city_id' => '', 'department_id' => '', 'country_id' => ''];

if ($id) {
    // Trae la sucursal + su departamento y país (para precargar los selects)
    $branch = db_one(
        "SELECT b.id, b.name, b.address, b.phone, b.city_id,
                ci.department_id, d.country_id
           FROM branches b
           JOIN cities ci     ON ci.id = b.city_id
           JOIN departments d ON d.id  = ci.department_id
          WHERE b.id = $1 AND b.deleted_at IS NULL",
        [$id]
    );
    if (!$branch) {
        flash('danger', 'La sucursal no existe.');
        redirect('modules/branches/index.php');
    }
}

$countries = db_all("SELECT id, name FROM countries WHERE deleted_at IS NULL ORDER BY name");

$page_title  = $id ? 'Editar sucursal' : 'Nueva sucursal';
$active_menu = 'branches';
$extra_js    = '<script>const BASE_URL = "' . BASE_URL . '";</script>'
             . '<script src="' . BASE_URL . 'assets/js/location-cascade.js"></script>';
require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4" style="max-width:820px">
    <div class="card-body">
        <form action="save.php" method="post">
            <input type="hidden" name="id" value="<?= e($branch['id']) ?>">

            <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-globe-americas"></i> Ubicación</h6>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="country_id">País *</label>
                    <select id="country_id" class="form-control" required>
                        <option value="">Seleccione un país</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $branch['country_id'] ? 'selected' : '' ?>>
                                <?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="department_id">Departamento *</label>
                    <select id="department_id" class="form-control" required disabled
                            data-selected="<?= e($branch['department_id']) ?>">
                        <option value="">Seleccione primero un país</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="city_id">Ciudad *</label>
                    <!-- Solo city_id tiene name: es lo único que se guarda -->
                    <select id="city_id" name="city_id" class="form-control" required disabled
                            data-selected="<?= e($branch['city_id']) ?>">
                        <option value="">Seleccione primero un departamento</option>
                    </select>
                </div>
            </div>

            <h6 class="text-primary font-weight-bold mb-3 mt-2"><i class="fas fa-building"></i> Datos de la sucursal</h6>
            <div class="form-group">
                <label>Nombre *</label>
                <input name="name" class="form-control" maxlength="120" required value="<?= e($branch['name']) ?>">
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label>Dirección *</label>
                    <input name="address" class="form-control" maxlength="200" required value="<?= e($branch['address']) ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>Teléfono</label>
                    <input name="phone" class="form-control" maxlength="30" value="<?= e($branch['phone']) ?>">
                </div>
            </div>
            <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
